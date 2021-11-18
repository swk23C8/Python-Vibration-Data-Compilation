import time
import sys
import pytz
import logging
import datetime
from urllib.parse import urlparse
import schedule
import matplotlib.pyplot as plt
import matplotlib.dates as mdates
from opcua import ua, Client
import numpy as np
from scipy.interpolate import interp1d
from scipy.interpolate.interpolate import interp1d
import mysql.connector

import scipy.signal
import math
from dateutil import parser
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

import email.mime.application
import email
import mimetypes

import json


class HighPassFilter(object):

    @staticmethod
    def get_highpass_coefficients(lowcut, sampleRate, order=5):
        nyq = 0.5 * sampleRate
        low = lowcut / nyq
        b, a = scipy.signal.butter(order, [low], btype='highpass')
        return b, a

    @staticmethod
    def run_highpass_filter(data, lowcut, sampleRate, order=5):
        if lowcut >= sampleRate/2.0:
            return data*0.0
        b, a = HighPassFilter.get_highpass_coefficients(lowcut, sampleRate, order=order)
        y = scipy.signal.filtfilt(b, a, data, padtype='even')
        return y
    
    @staticmethod
    def perform_hpf_filtering(data, sampleRate, hpf=3):
        if hpf == 0:
            return data
        data[0:6] = data[13:7:-1] # skip compressor settling
        data = HighPassFilter.run_highpass_filter(
            data=data,
            lowcut=3,
            sampleRate=sampleRate,
            order=1,
        )
        data = HighPassFilter.run_highpass_filter(
            data=data,
            lowcut=int(hpf),
            sampleRate=sampleRate,
            order=2,
        )
        return data

class FourierTransform(object):

    @staticmethod
    def perform_fft_windowed(signal, fs, winSize, nOverlap, window, detrend = True, mode = 'lin'):
        assert(nOverlap < winSize)
        assert(mode in ('magnitudeRMS', 'magnitudePeak', 'lin', 'log'))
    
        # Compose window and calculate 'coherent gain scale factor'
        w = scipy.signal.get_window(window, winSize)
        # http://www.bores.com/courses/advanced/windows/files/windows.pdf
        # Bores signal processing: "FFT window functions: Limits on FFT analysis"
        # F. J. Harris, "On the use of windows for harmonic analysis with the
        # discrete Fourier transform," in Proceedings of the IEEE, vol. 66, no. 1,
        # pp. 51-83, Jan. 1978.
        coherentGainScaleFactor = np.sum(w)/winSize
    
        # Zero-pad signal if smaller than window
        padding = len(w) - len(signal)
        if padding > 0:
            signal = np.pad(signal, (0,padding), 'constant')
    
        # Number of windows
        k = int(np.fix((len(signal)-nOverlap)/(len(w)-nOverlap)))
    
        # Calculate psd
        j = 0
        spec = np.zeros(len(w));
        for i in range(0, k):
            segment = signal[j:j+len(w)]
            if detrend is True:
                segment = scipy.signal.detrend(segment)
            winData = segment*w
            # Calculate FFT, divide by sqrt(N) for power conservation,
            # and another sqrt(N) for RMS amplitude spectrum.
            fftData = np.fft.fft(winData, len(w))/len(w)
            sqAbsFFT = abs(fftData/coherentGainScaleFactor)**2
            spec = spec + sqAbsFFT;
            j = j + len(w) - nOverlap
    
        # Scale for number of windows
        spec = spec/k
    
        # If signal is not complex, select first half
        if len(np.where(np.iscomplex(signal))[0]) == 0:
            stop = int(math.ceil(len(w)/2.0))
            # Multiply by 2, except for DC and fmax. It is asserted that N is even.
            spec[1:stop-1] = 2*spec[1:stop-1]
        else:
            stop = len(w)
        spec = spec[0:stop]
        freq = np.round(float(fs)/len(w)*np.arange(0, stop), 2)
    
        if mode == 'lin': # Linear Power spectrum
            return (spec, freq)
        elif mode == 'log': # Log Power spectrum
            return (10.*np.log10(spec), freq)
        elif mode == 'magnitudeRMS': # RMS Magnitude spectrum
            return (np.sqrt(spec), freq)
        elif mode == 'magnitudePeak': # Peak Magnitude spectrum
            return (np.sqrt(2.*spec), freq)
class OpcUaClient(object):
    CONNECT_TIMEOUT = 15  # [sec]
    RETRY_DELAY = 10  # [sec]
    MAX_RETRIES = 3  # [-]

    class Decorators(object):
        @staticmethod
        def autoConnectingClient(wrappedMethod):
            def wrapper(obj, *args, **kwargs):
                for retry in range(OpcUaClient.MAX_RETRIES):
                    try:
                        return wrappedMethod(obj, *args, **kwargs)
                    except ua.uaerrors.BadNoMatch:
                        raise
                    except Exception:
                        pass
                    try:
                        obj._logger.warning('(Re)connecting to OPC-UA service.')
                        obj.reconnect()
                    except ConnectionRefusedError:
                        obj._logger.warning(
                            'Connection refused. Retry in 10s.'.format(
                                OpcUaClient.RETRY_DELAY
                            )
                        )
                        time.sleep(OpcUaClient.RETRY_DELAY)
                else:  # So the exception is exposed.
                    obj.reconnect()
                    return wrappedMethod(obj, *args, **kwargs)
            return wrapper

    def __init__(self, serverUrl):
        self._logger = logging.getLogger(self.__class__.__name__)
        self._client = Client(
            serverUrl.geturl(),
            timeout=self.CONNECT_TIMEOUT
        )

    def __enter__(self):
        self.connect()
        return self

    def __exit__(self, exc_type, exc_value, traceback):
        self.disconnect()
        self._client = None

    @property
    @Decorators.autoConnectingClient
    def sensorList(self):
        return self.objectsNode.get_children()

    @property
    @Decorators.autoConnectingClient
    def objectsNode(self):
        path = [ua.QualifiedName(name='Objects', namespaceidx=0)]
        return self._client.get_root_node().get_child(path)

    def connect(self):
        self._client.connect()
        self._client.load_type_definitions()

    def disconnect(self):
        try:
            self._client.disconnect()
        except Exception:
            pass

    def reconnect(self):
        self.disconnect()
        self.connect()

    @Decorators.autoConnectingClient
    def get_browse_name(self, uaNode):
        return uaNode.get_browse_name()

    @Decorators.autoConnectingClient
    def get_node_class(self, uaNode):
        return uaNode.get_node_class()

    @Decorators.autoConnectingClient
    def get_namespace_index(self, uri):
        return self._client.get_namespace_index(uri)

    @Decorators.autoConnectingClient
    def get_child(self, uaNode, path):
        return uaNode.get_child(path)

    @Decorators.autoConnectingClient
    def read_raw_history(self,
                         uaNode,
                         starttime=None,
                         endtime=None,
                         numvalues=0,
                         cont=None):
        details = ua.ReadRawModifiedDetails()
        details.IsReadModified = False
        details.StartTime = starttime or ua.get_win_epoch()
        details.EndTime = endtime or ua.get_win_epoch()
        details.NumValuesPerNode = numvalues
        details.ReturnBounds = True
        result = OpcUaClient._history_read(uaNode, details, cont)
        assert(result.StatusCode.is_good())
        return result.HistoryData.DataValues, result.ContinuationPoint

    @staticmethod
    def _history_read(uaNode, details, cont):
        valueid = ua.HistoryReadValueId()
        valueid.NodeId = uaNode.nodeid
        valueid.IndexRange = ''
        valueid.ContinuationPoint = cont

        params = ua.HistoryReadParameters()
        params.HistoryReadDetails = details
        params.TimestampsToReturn = ua.TimestampsToReturn.Both
        params.ReleaseContinuationPoints = False
        params.NodesToRead.append(valueid)
        result = uaNode.server.history_read(params)[0]
        return result


class DataAcquisition(object):
    LOGGER = logging.getLogger('DataAcquisition')
    MAX_VALUES_PER_ENDNODE = 10000  # Num values per endnode
    MAX_VALUES_PER_REQUEST = 10  # Num values per history request

    @staticmethod
    def get_sensor_sub_node(client, macId, browseName, subBrowseName, sub2BrowseName=None, sub3BrowseName=None, sub4BrowseName=None):
        nsIdx = client.get_namespace_index(
            'http://www.iqunet.com'
        )  # iQunet namespace index
        bpath = [
            ua.QualifiedName(name=macId, namespaceidx=nsIdx),
            ua.QualifiedName(name=browseName, namespaceidx=nsIdx),
            ua.QualifiedName(name=subBrowseName, namespaceidx=nsIdx)
        ]
        if sub2BrowseName is not None:
            bpath.append(ua.QualifiedName(name=sub2BrowseName, namespaceidx=nsIdx))
        if sub3BrowseName is not None:
            bpath.append(ua.QualifiedName(name=sub3BrowseName, namespaceidx=nsIdx))
        if sub4BrowseName is not None:
            bpath.append(ua.QualifiedName(name=sub4BrowseName, namespaceidx=nsIdx))
        sensorNode = client.objectsNode.get_child(bpath)
        return sensorNode

    @staticmethod
    def get_endnode_data(client, endNode, starttime, endtime):
        dvList = DataAcquisition.download_endnode(
            client=client,
            endNode=endNode,
            starttime=starttime,
            endtime=endtime
        )
        dates, values = ([], [])
        for dv in dvList:
            dates.append(dv.SourceTimestamp.strftime('%Y-%m-%d %H:%M:%S'))
            values.append(dv.Value.Value)

        # If no starttime is given, results of read_raw_history are reversed.
        if starttime is None:
            values.reverse()
            dates.reverse()
        return (values, dates)

    @staticmethod
    def download_endnode(client, endNode, starttime, endtime):
        endNodeName = client.get_browse_name(endNode).Name
        DataAcquisition.LOGGER.info(
            'Downloading endnode {:s}'.format(
                endNodeName
            )
        )
        dvList, contId = [], None
        while True:
            remaining = DataAcquisition.MAX_VALUES_PER_ENDNODE - len(dvList)
            assert(remaining >= 0)
            numvalues = min(DataAcquisition.MAX_VALUES_PER_REQUEST, remaining)
            partial, contId = client.read_raw_history(
                uaNode=endNode,
                starttime=starttime,
                endtime=endtime,
                numvalues=numvalues,
                cont=contId
            )
            if not len(partial):
                DataAcquisition.LOGGER.warning(
                    'No data was returned for {:s}'.format(endNodeName)
                )
                break
            dvList.extend(partial)
            sys.stdout.write('\r    Loaded {:d} values, {:s} -> {:s}'.format(
                len(dvList),
                str(dvList[0].ServerTimestamp.strftime("%Y-%m-%d %H:%M:%S")),
                str(dvList[-1].ServerTimestamp.strftime("%Y-%m-%d %H:%M:%S"))
            ))
            sys.stdout.flush()
            if contId is None:
                break  # No more data.
            if len(dvList) >= DataAcquisition.MAX_VALUES_PER_ENDNODE:
                break  # Too much data.
        sys.stdout.write('...OK.\n')
        return dvList

    @staticmethod
    def get_anomaly_model_nodes(client, macId):
        sensorNode = \
            DataAcquisition.get_sensor_sub_node(client, macId, "tensorFlow", "models")
        DataAcquisition.LOGGER.info(
            'Browsing for models of {:s}'.format(macId)
        )
        modelNodes = sensorNode.get_children()
        return modelNodes

    @staticmethod
    def get_anomaly_model_parameters(client, macId, starttime, endtime):
        #acquires a list of all subnodes below the models node
        modelNodes = \
            DataAcquisition.get_anomaly_model_nodes(client, macId)
        #to here
        models = dict()
        for mnode in modelNodes:
            key = mnode.get_display_name().Text
            print(key)
            sensorNode = \
                DataAcquisition.get_sensor_sub_node(client, macId, "tensorFlow", "models", key, "lossMAE")
            (valuesraw, datesraw) = \
                DataAcquisition.get_endnode_data(
                    client=client,
                    endNode=sensorNode,
                    starttime=starttime,
                    endtime=endtime
            )

            sensorNode = \
                DataAcquisition.get_sensor_sub_node(client, macId, "tensorFlow", "models", key, "lossMAE", "alarmLevel")
            alarmLevel = sensorNode.get_value()
            modelSet = {
                "raw": (valuesraw, datesraw),
                "alarmLevel": alarmLevel
            }
            models[key] = modelSet
        return models
    @staticmethod
    def get_sensor_data(serverUrl, macId, browseName, starttime, endtime):
        with OpcUaClient(serverUrl) as client:
            assert(client._client.uaclient._uasocket.timeout == 15)
            sensorNode = \
                DataAcquisition.get_sensor_node(client, macId, browseName)
            DataAcquisition.LOGGER.info(
                    'Browsing {:s}'.format(macId)
            )
            (values, dates) = \
                DataAcquisition.get_endnode_data(
                        client=client,
                        endNode=sensorNode,
                        starttime=starttime,
                        endtime=endtime
                )
        return (values, dates)
    @staticmethod
    def get_sensor_node(client, macId, browseName):
        nsIdx = client.get_namespace_index(
                'http://www.iqunet.com'
        )  # iQunet namespace index
        bpath = [
                ua.QualifiedName(name=macId, namespaceidx=nsIdx),
                ua.QualifiedName(name=browseName, namespaceidx=nsIdx)
        ]
        sensorNode = client.objectsNode.get_child(bpath)
        return sensorNode

def getBatteryStatus(serverIP, macId):
    logging.basicConfig(level=logging.INFO)
    logging.getLogger("opcua").setLevel(logging.WARNING)
    timeZone = "Asia/Seoul"

    hpf = 0

    # replace xx.xx.xx.xx with the IP address of your server
    serverUrl = urlparse('opc.tcp://{:s}:4840'.format(serverIP))

    # # replace xx:xx:xx:xx with your sensors macId
    # # Kowon_Vib_BackMotor
    # macId = '38:91:12:21'
    # # Kowon_Vib_BackCrank
    # macId = 'e0:10:9a:cd'
    # # Kowon_Vib_FrontMotor
    # macId = 'ec:1a:69:5f'
    # # Kowon_Vib_FrontCrank
    # macId = 'fe:1e:09:59'


    sensorName = 'Kowon_Vib_BackMotor'
    rangeTime = 1440
    endTime = datetime.datetime.now()
    # endTime2 = datetime.datetime.now(tz=localtimezone)
    startTime = endTime - datetime.timedelta(hours=10)

    print('start time ', startTime)
    print('end time ', endTime)
    # print('end time 2', endTime2)

    browseName=["accelerationPack","axis","batteryVoltage","boardTemperature",
                "firmware","formatRange","gKurtX","gKurtY","gKurtZ","gRmsX","gRmsY",
                "gRmsZ","hardware","mmsKurtX","mmsKurtY","mmsKurtZ",
                "mmsRmsX","mmsRmsY","mmsRmsZ","numSamples","sampleRate"]

    (values,dates) = DataAcquisition.get_sensor_data(
        serverUrl=serverUrl,
        macId=macId,
        browseName=browseName[2],
        starttime=startTime,
        endtime=endTime
        )
    # print(values, dates)
    return values, dates

    # for i in range(len(dates)):
    #     dates[i] = datetime.datetime.strptime(dates[i], "%y-%m-%d %H:%M:%S") + datetime.timedelta(hours=9)

def getBatteryPercentage(values):
    m = interp1d([2.1,3.24],[1,100])
    # print(values)
    if not values:
        print("NO battery or/and NO connection")
        batteryPercentage = 0
        connectionStatus = "disconnected"
    else:
        print("battery and connection GOOD")
        connectionStatus = "connected"
        batteryPercentage = m(values[-1])

    return connectionStatus, batteryPercentage

def connectDB(connectionStatus, batteryPercentage, sensorName):
    mydb = mysql.connector.connect(
        host="enter host here",
        user="enter username here",
        # password="test",

        database="enter database name here",
        port="3306"
    )
    mycursor = mydb.cursor()
    sql = "UPDATE gorus.sensors SET connectionStatus=%s, batteryPercentage=%s WHERE sensorName=%s;"
    val = (connectionStatus, str(batteryPercentage), sensorName)
    mycursor.execute(sql, val)
    mydb.commit()
    print(mycursor.rowcount, "record(s) affected")



if __name__ == "__main__":
    # Kowon_Vib_BackMotor
    # Kowon_Vib_BackCrank
    # Kowon_Vib_FrontMotor
    # Kowon_Vib_FrontCrank
    macIds = ['38:91:12:21', 'e0:10:9a:cd', 'ec:1a:69:5f','fe:1e:09:59']
    macIdNames = ['Kowon_Vib_BackMotor', 'Kowon_Vib_BackCrank', 'Kowon_Vib_FrontMotor','Kowon_Vib_FrontCrank']
    for id, names in zip(macIds,macIdNames):
        print(id)
        (values, dates) = getBatteryStatus("25.100.199.132", id)
        connectionStatus, batteryPercentage = getBatteryPercentage(values)
        print(batteryPercentage)
        connectDB(connectionStatus, batteryPercentage, names)

    print(values[-1])
    print(dates[-1])

    jsonList = []

    for i in range(0, len(values)):
        jsonList.append({"value" : values[i], "dates" : dates[i]})

    data = json.dumps(jsonList, indent = 1)

    with open('C:/enter directory here/test_battery_data.json', 'w') as json_file:
        json_file.write(data)

    with open('C:/enter directory here/test_battery_data.json', 'r') as json_file:
        data = json_file.read()
        
    d = json.loads(data)

    print(d)
    # print(values)
    # print(dates)




    # # jsonString = json.dumps(jsonList, indent=1)
    # # jsonFile = open("data.json", "w")
    # # jsonFile.write(jsonString)
    # # jsonFile.close()


    # with open('data.json', 'w') as json_file:
    #     jsonString = json.dumps({'data': jsonList})
    #     json_file.write(jsonString)
    #     # json.dump(jsonList, json_file, sort_keys=True, indent=1)

    # # print(json.dumps(jsonList, indent = 1))
    # # schedule.every(1).second.do(grab_anomaly)

    while True:
        schedule.run_pending()
        time.sleep(1)
