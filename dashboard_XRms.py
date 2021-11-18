
import time
import sys
import pytz
import logging
import datetime
from urllib.parse import urlparse
import matplotlib.pyplot as plt
import json
import numpy as np
import math
import scipy.signal
import csv

import pandas as pd
from dateutil import parser
from opcua import ua, Client


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
                        obj._logger.warn('(Re)connecting to OPC-UA service.')
                        obj.reconnect()
                    except ConnectionRefusedError:
                        obj._logger.warn(
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
    MAX_VALUES_PER_ENDNODE = 1000000  # Num values per endnode
    MAX_VALUES_PER_REQUEST = 2  # Num values per history request

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
                #DataAcquisition.LOGGER.warn(
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



if __name__ == "__main__":
    rmsList =["gRmsX","gRmsY","gRmsZ"]
    logging.basicConfig(level=logging.INFO)
    logging.getLogger("opcua").setLevel(logging.WARNING)

    # replace xx.xx.xx.xx with the IP address of your server
    #serverIP = "25.77.104.183"    #reshenieYC_new
    #serverIP = "25.27.135.161"    #Tachyon
    # serverIP= "25.100.74.22"      # 21C
    serverIP = "25.100.199.132"
    
    serverUrl = urlparse('opc.tcp://{:s}:4840'.format(serverIP))

    # replace xx:xx:xx:xx with your sensors macId
    #macId = 'b7:90:74:36'   #reshenieYC_New Vibration_Power
    #macId = '9e:85:50:50'   #Tachyon sensor 1
    #macId = 'b9:07:6c:24'   #Tachyon sensor 4
    #macId = '9d:d7:01:32'   #21C vib2_spindle_lowfq 
    #macId = 'e0:89:b9:3c'   #21C vib1_spindle_highfq
    # macId = 'b4:a0:a4:07'   #21C vib3_Bed
    macId = 'ec:1a:69:5f'
    

    # change settings
    timeZone = "Asia/Seoul" # local time zone
    limit = 1000 # limit limits the number of returned measurements
    axis = 'Y'  # axis allows to select data from only 1 or multiple axes
    hpf = 0
    
    # note: time should be set in 
    # starttime = pytz.utc.localize(
    #     datetime.datetime.strptime("2021-06-30 12:00:00", '%Y-%m-%d %H:%M:%S')
    # )
    # endtime = pytz.utc.localize(
    #     datetime.datetime.strptime("2021-06-30 23:59:00", '%Y-%m-%d %H:%M:%S')
    # )
    endtime = datetime.datetime.now()
    starttime = endtime - datetime.timedelta(days=365)

    # acquire history data
    browseName=["accelerationPack","axis","batteryVoltage","boardTemperature",
               "firmware","formatRange","gKurtX","gKurtY","gKurtZ","gRmsX","gRmsY",
               "gRmsZ","hardware","mmsKurtX","mmsKurtY","mmsKurtZ",
               "mmsRmsX","mmsRmsY","mmsRmsZ","numSamples","sampleRate"]

    (values,dates) = DataAcquisition.get_sensor_data(
        serverUrl=serverUrl,
        macId=macId,
        browseName=browseName[9],
        starttime=starttime,
        endtime=endtime
        )




    datesEpoch = []
    for i in range(0, len(dates)):
        date_time = datetime.datetime.strptime(dates[i], "%Y-%m-%d %H:%M:%S")
        # print(date_time)
        date_time_seconds = time.mktime(date_time.timetuple())
        # print(date_time_seconds)
        datesEpoch.append(f"{date_time_seconds}")
        datesEpoch.append(1)


    def list_to_dict(list):
        res_dict = {list[i]: list[i + 1] for i in range(0, len(list), 2)}
        return res_dict

    XRmsEpochValue = list_to_dict(datesEpoch)
    print(XRmsEpochValue)

    XRmsRawValue = [] # stores XRms raw value
    XRmsTimeValue = [] # stores XRms time value

    for i in range(0, len(values)):
        XRmsRawValue.append(values[i])
        XRmsTimeValue.append(dates[i])
    
    data = json.dumps(XRmsEpochValue)
    print(data)
    with open('C:/enter directory here/X_Kowon_Vib_FrontMotor_epoch_data.json', 'w') as json_file:
        json_file.write(data)

    with open('C:/enter directory here/X_Kowon_Vib_FrontMotor_epoch_data.json', 'r') as json_file:
        data = json_file.read()
    
    s = json.loads(data)

    data = json.dumps(XRmsRawValue)
    with open('C:/enter directory here/X_Kowon_Vib_FrontMotor_RmsX_data.json', 'w') as json_file:
        json_file.write(data)

    with open('C:/enter directory here/X_Kowon_Vib_FrontMotor_RmsX_data.json', 'r') as json_file:
        data = json_file.read()
    
    v = json.loads(data)

    # print(v)

    data = json.dumps(XRmsTimeValue)
    with open('C:/enter directory here/X_Kowon_Vib_FrontMotor_RmsX_time_data.json', 'w') as json_file:
        json_file.write(data)

    with open('C:/enter directory here/X_Kowon_Vib_FrontMotor_RmsX_time_data.json', 'r') as json_file:
        data = json_file.read()
    
    d = json.loads(data)

    # print(d)

    # TimejsonList = []
    # # for i in range(0, len(timeValues)):
    # #     TimejsonList.append({"time" : timeValues[i]})
    # TimejsonList.append({"time" : timeValues})

    # data = json.dumps(TimejsonList, cls=NumpyEncoder , indent = 1)

