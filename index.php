<!-- author -->
<!-- SUNWOOK KIM -->
<!-- swk23c8 -->
<?php
include_once('./_common.php');

$g5['title'] = '관리자메인';
// include_once ('./admin.head.php');

function console_log($data)
{
  echo '<script>';
  echo 'console.log(' . json_encode($data) . ')';
  echo '</script>';
}

$data_image = 'https://i.imgur.com/QaHz1v1.png';
$logo_image = 'https://i.imgur.com/SizYIZj.png';
$factory_image = 'https://i.imgur.com/47TurLL.jpg';
$machine_image = 'https://i.imgur.com/YUj27fC.jpg';
$sensor_image_1 = 'https://i.imgur.com/6DATpIs.jpg';
$sensor_image_2 = 'https://i.imgur.com/KpyELWL.jpg';
$sensor_image_3 = 'https://i.imgur.com/nMKXYhH.jpg';
$sensor_image_4 = 'https://i.imgur.com/hCgdRR9.jpg';

//

$sql = " select connectionStatus from gorus.sensors where sensorName='Kowon_Vib_BackCrank' ";
$result = sql_fetch($sql);
$backCrankStatus = $result['connectionStatus'];

$sql = " select batteryPercentage from gorus.sensors where sensorName='Kowon_Vib_BackCrank' ";
$result = sql_fetch($sql);
$backCrankBattery = $result['batteryPercentage'];

//

$sql = " select connectionStatus from gorus.sensors where sensorName='Kowon_Vib_BackMotor' ";
$result = sql_fetch($sql);
$backMotorStatus = $result['connectionStatus'];

$sql = " select batteryPercentage from gorus.sensors where sensorName='Kowon_Vib_BackMotor' ";
$result = sql_fetch($sql);
$backMotorBattery = $result['batteryPercentage'];

//

$sql = " select connectionStatus from gorus.sensors where sensorName='Kowon_Vib_FrontCrank' ";
$result = sql_fetch($sql);
$frontCrankStatus = $result['connectionStatus'];

$sql = " select batteryPercentage from gorus.sensors where sensorName='Kowon_Vib_FrontCrank' ";
$result = sql_fetch($sql);
$frontCrankBattery = $result['batteryPercentage'];

//

$sql = " select connectionStatus from gorus.sensors where sensorName='Kowon_Vib_FrontMotor' ";
$result = sql_fetch($sql);
$frontMotorStatus = $result['connectionStatus'];

$sql = " select batteryPercentage from gorus.sensors where sensorName='Kowon_Vib_FrontMotor' ";
$result = sql_fetch($sql);
$frontMotorBattery = $result['batteryPercentage'];

//

// console_log( $backMotorBattery );

if (!$member['mb_id']) {
  alert('로그인 하십시오.', G5_BBS_URL . '/login.php?url=' . urlencode(G5_ADMIN_URL));
} else if ($is_admin != 'super') {
  $auth = array();
  $sql = " select au_menu, au_auth from {$g5['auth_table']} where mb_id = '{$member['mb_id']}' ";
  $result = sql_query($sql);
  for ($i = 0; $row = sql_fetch_array($result); $i++) {
    $auth[$row['au_menu']] = $row['au_auth'];
  }

  if ($member['mb_id'] != "admin") {
    alert('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', G5_URL);
  }
}
$sql = " select count(*) as cnt { from {$g5['member_table']} } { where (1) } { order by {$sst} {$sod} } ";
?>



<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Dashboard 3</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="/assets/plugins/fontawesome-free/css/all.min.css">
  <!-- IonIcons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/assets/css/adminlte.min.css">

  <!-- d3js.org heatmap calendar -->
  <script src="/assets/js/test/d3.min.js"></script>
  <link rel="stylesheet" href="/assets/js/cal-heatmap.css" />
  <script type="text/javascript" src="/assets/js/cal-heatmap.min.js"></script>
  <script type="text/javascript" src="/assets/js/cal-heatmap.js"></script>

  <!-- plotly.js CDN -->
  <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>






  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


  <!-- <script src="/assets/plugins/jquery/jquery.min.js"></script>

  <style>
    #dataTable {
      width: 500px;
      border: 1px sold blue;
    }

    #tBody {
      background: #FFF;
    }

    #tBody .ui-selected {
      background: #FFCCCC
    }

    #tBody .ui-selecting {
      background: #CCCCFF
    }

    #tBody td {
      border: 1px solid red;
    }

    #selectedModemNum {
      width: 350px;
      height: 150px;
    }
  </style>

  <script>
    $(document).ready(function() {
      //tBody 객체에 selectable 기능 구현
      $("#tBody").selectable({
        filter: "tr",
        selected: selectedEvent,
        unselected: selectedEvent
      });
    });


    function selectedEvent(event, ui) {
      var modemNum = "";
      $("#selectedModemNum").val("");

      var selectedObj = $(ui.selected);
      var unselectedObj = $(ui.unselected);

      //표에서 선택된 정보 취득
      $(this).find(".ui-selected").each(function() {
        modemNum += $(this).find("#modemNum").text();
      })

      //unselected 이벤트 처리를 위한 코드
      //unselected될 경우 해당 선택 데이터 취소
      if (ui.unselected != undefined) {
        modemNum = modemNum.replace(unselectedObj.find("#modemNum").text(), "");
      }

      $("#selectedModemNum").val(modemNum);
    }
  </script>
</head>
<!--
`body` tag options:

  Apply one or more of the following classes to to the body tag
  to get the desired effect

  * sidebar-collapse
  * sidebar-mini
-->


<body class="hold-transition sidebar-mini">

  <div class="wrapper" style="height: fit-content; width: fit-content;">
    <!-- Navbar -->

    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->

      <a href="<?php echo G5_URL ?>">
        <img src="<?php echo G5_THEME_URL ?>/images/logo.ico" alt="<?php echo $config['cf_title']; ?>">
      </a>

      <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="brand-link">
        <img src="../theme/basic/images/reshenie_logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"> Reshenie Dashboard</span>
      </a>

      <!-- <a href="dashboard.php" class="brand-link">
        <img src="/assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"> Reshenie Dashboard</span>
      </a> -->

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <!-- <style class="user-panel mt-3 pb-3 mb-3 d-flex">
              .user-panel.mt-3.pb-3.mb-3.d-flex {
                text-align:left;
              }
          </style> -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="/assets/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block">ADMIN</a>
          </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
          <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item menu-open">
              <a href="#" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  KownMetal
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Factory 1</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Factory 2</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="nav-link active">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Factory 3</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  21 Century
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Factory 1</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Factory 2</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="nav-link active">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Factory 3</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  S-Food
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Factory 1</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Factory 2</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo DASHBOARD_ADMIN_URL ?>" class="nav-link active">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Factory 3</p>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">
                <style class="logo_img">
                  .logo_img {
                    text-align: left;
                  }
                </style>
                <div class="logo_img"><img src="<?php echo $logo_image; ?>" width=320></div>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard v3</li>
              </ol>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-6">
              <style class="col-lg-6">
                .col-lg-6 {
                  max-width: 33.6%;
                }
              </style>
              <div class="card">


                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      <font size="+2"><strong>Factory 1</strong></font>
                    </h3>
                    <!-- <a href="javascript:void(0);">View Report</a> -->
                  </div>
                </div>
                <div class="card-body">
                  <style class="factory_img">
                    .factory_img {
                      text-align: center;
                    }
                  </style>
                  <div class="factory_img">
                    <img src="<?php echo $factory_image; ?>" style="width: 100%;max-height: 100%">
                  </div>
                  <!-- <div class="d-flex">
                    <p class="d-flex flex-column">
                      <span class="text-bold text-lg">820</span>
                      <span>Frequency</span>
                    </p>
                    <p class="ml-auto d-flex flex-column text-right">
                      <span class="text-success">
                        <i class="fas fa-arrow-up"></i> 12.5%
                      </span>
                      <span class="text-muted">Since last week</span>
                    </p>
                  </div> -->

                  <!-- /.d-flex -->

                  <!-- <div class="position-relative mb-4">
                    <canvas id="visitors-chart" height="200"></canvas>
                  </div>

                  <div class="d-flex flex-row justify-content-end">
                    <span class="mr-2">
                      <i class="fas fa-square text-primary"></i> This Week
                    </span>
                    <span>
                      <i class="fas fa-square text-gray"></i> Last Week
                    </span>
                  </div> -->
                </div>
              </div>
              <!-- /.card -->

              <div class="card">
                <div class="card-header border-0">
                  <h3 class="card-title">
                    <font size="+2"><strong>Sensors</strong></font>
                  </h3>
                  <div class="card-tools">
                    <a href="#" class="btn btn-tool btn-sm">
                      <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-tool btn-sm">
                      <i class="fas fa-bars"></i>
                    </a>
                  </div>
                </div>


                <div class="card-body table-responsive p-0">
                  <style class="table table-striped table-valign-middle">
                    .table {
                      border-top: 14px solid #e83e8c;
                    }
                  </style>
                  <table class="table table-striped table-valign-middle">
                    <thead>
                      <tr>
                        <th width="30%">Vib-AiR</th>
                        <!-- <th width="20%">Picture</th> -->
                        <th width="1%">connection/battery</th>
                        <!-- <th>More</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <!-- <td> -->
                        <!-- <img src="/assets/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2"> -->
                        <!-- Kowon_Vib_FrontCrank -->
                        <!-- </td> -->
                        <td>
                          <div class="factory_img">
                            <img src="<?php echo $sensor_image_1; ?>" width=300 id="Kowon_Vib_FrontCrank">
                            <p>Kowon_Vib_FrontCrank</p>
                          </div>
                        </td>
                        <td>
                          <input type="text" class="knob" value="<?php echo htmlspecialchars($frontCrankBattery); ?>" data-width="80" data-height="80" data-fgColor="#379928" data-thickness="0.3" readonly>
                          <div class="knob-label"><?php echo htmlspecialchars($frontCrankStatus); ?></div>

                          <small class="text-success mr-1">
                            <!-- <i class="fas fa-arrow-up"></i> -->
                            fe:1e:09:59
                          </small>

                        </td>

                      </tr>
                      <tr>
                        <!-- <td> -->
                        <!-- <img src="/assets/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2"> -->
                        <!-- Kowon_Vib_BackCrank -->
                        <!-- </td> -->
                        <td>
                          <div class="factory_img">
                            <img src="<?php echo $sensor_image_2; ?>" width=300 id="Kowon_Vib_BackCrank">
                            <p>Kowon_Vib_BackCrank</p>
                          </div>

                        </td>
                        <td>

                          <input type="text" class="knob" value="<?php echo htmlspecialchars($backCrankBattery); ?>" data-width="80" data-height="80" data-fgColor="#379928" data-thickness="0.3" readonly>
                          <div class="knob-label"><?php echo htmlspecialchars($backCrankStatus); ?></div>

                          <small class="text-warning mr-1">
                            <!-- <i class="fas fa-arrow-down"></i> -->
                            e0:10:9a:cd
                          </small>
                        </td>

                      </tr>

                      <tr>
                        <!-- <td> -->
                        <!-- <img src="/assets/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2"> -->
                        <!-- Kowon_Vib_FrontMotor -->
                        <!-- <span class="badge bg-danger"> NEW</span> -->
                        <!-- </td> -->
                        <td>
                          <div class="factory_img">
                            <img src="<?php echo $sensor_image_3; ?>" width=300 onclick="changeSensor()" id="Kowon_Vib_FrontMotor">
                            <p>Kowon_Vib_FrontMotor
                              <span class="badge bg-danger"> NEW</span>
                            </p>
                          </div>
                          <script>
                            function changeSensor() {

                            }
                          </script>
                        </td>
                        <td>

                          <input type="text" class="knob" value="<?php echo htmlspecialchars($frontMotorBattery); ?>" data-width="80" data-height="80" data-fgColor="#379928" data-thickness="0.3" readonly>
                          <div class="knob-label"><?php echo htmlspecialchars($frontMotorStatus); ?></div>

                          <small class="text-success mr-1">
                            <!-- <i class="fas fa-arrow-up"></i> -->
                            ec:1a:69:5f
                          </small>
                        </td>

                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="card-body table-responsive p-0">
                  <table class="table table-striped table-valign-middle">
                    <thead>
                      <tr>
                        <th width="30%">Bridge-AiR</th>
                        <!-- <th width="20%">Picture</th> -->
                        <th width="1%">connection/battery</th>
                        <!-- <th>More</th> -->
                      </tr>
                    </thead>
                    <tbody>

                      <tr>
                        <!-- <td> -->
                        <!-- <img src="/assets/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2"> -->
                        <!-- Bed_LowFQ -->
                        <!-- <img src="/assets/img/default-150x150.png" alt="Product 1" class="img-circle img-size-32 mr-2"> -->
                        <!-- Kowon_Vib_BackMotor -->
                        <!-- </td> -->
                        <td>
                          <div class="factory_img">
                            <img src="<?php echo $sensor_image_4; ?>" width=300 id="Kowon_Vib_BackMotor">
                            <p>Kowon_Vib_BackMotor</p>
                          </div>
                        </td>
                        <td>
                          <input type="text" class="knob" value="<?php echo htmlspecialchars($backMotorBattery); ?>" data-width="80" data-height="80" data-fgColor="#379928" data-thickness="0.3" readonly>
                          <div class="knob-label"><?php echo htmlspecialchars($backMotorStatus); ?></div>

                          <small class="text-danger mr-1">
                            <!-- <i class="fas fa-arrow-down"></i> -->
                            38:91:12:21
                          </small>
                        </td>

                      </tr>

                    </tbody>
                  </table>
                </div>
              </div>

              <!-- /.card -->
            </div>
            <!-- /.col-md-6 -->
            <div class="col-lg-7">
              <style class="col-lg-7">
                .col-lg-7 {
                  max-width: 66.3%;
                }
              </style>
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      <font size="+2"><strong>Machines</strong></font>
                    </h3>
                    <a href="javascript:void(0);">View Report</a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-2">
                      <a href=<?php echo $machine_image; ?> data-toggle="lightbox" data-title="sample 1 - white" data-gallery="gallery">
                        <img src=<?php echo $machine_image; ?> class="img-fluid mb-2" alt="white sample" />
                      </a>
                    </div>
                    <!-- <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/FFFFFF.png?text=1" data-toggle="lightbox" data-title="sample 1 - white" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=1" class="img-fluid mb-2" alt="white sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/000000.png?text=2" data-toggle="lightbox" data-title="sample 2 - black" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/000000?text=2" class="img-fluid mb-2" alt="black sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/FF0000/FFFFFF.png?text=3" data-toggle="lightbox" data-title="sample 3 - red" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/FF0000/FFFFFF?text=3" class="img-fluid mb-2" alt="red sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/FF0000/FFFFFF.png?text=4" data-toggle="lightbox" data-title="sample 4 - red" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/FF0000/FFFFFF?text=4" class="img-fluid mb-2" alt="red sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/000000.png?text=5" data-toggle="lightbox" data-title="sample 5 - black" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/000000?text=5" class="img-fluid mb-2" alt="black sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/FFFFFF.png?text=6" data-toggle="lightbox" data-title="sample 6 - white" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=6" class="img-fluid mb-2" alt="white sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/FFFFFF.png?text=7" data-toggle="lightbox" data-title="sample 7 - white" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=7" class="img-fluid mb-2" alt="white sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/000000.png?text=8" data-toggle="lightbox" data-title="sample 8 - black" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/000000?text=8" class="img-fluid mb-2" alt="black sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/FF0000/FFFFFF.png?text=9" data-toggle="lightbox" data-title="sample 9 - red" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/FF0000/FFFFFF?text=9" class="img-fluid mb-2" alt="red sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/FFFFFF.png?text=10" data-toggle="lightbox" data-title="sample 10 - white" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=10" class="img-fluid mb-2" alt="white sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/FFFFFF.png?text=11" data-toggle="lightbox" data-title="sample 11 - white" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=11" class="img-fluid mb-2" alt="white sample"/>
                    </a>
                  </div>
                  <div class="col-sm-2">
                    <a href="https://via.placeholder.com/1200/000000.png?text=12" data-toggle="lightbox" data-title="sample 12 - black" data-gallery="gallery">
                      <img src="https://via.placeholder.com/300/000000?text=12" class="img-fluid mb-2" alt="black sample"/>
                    </a>
                  </div> -->
                  </div>
                </div>
                <!-- <div class="card-body">
                  <div class="d-flex">
                    <p class="d-flex flex-column">
                      <span class="text-bold text-lg">18,230.00</span>
                      <span>Anomalies Over Time</span>
                    </p>
                    <p class="ml-auto d-flex flex-column text-right">
                      <span class="text-success">
                        <i class="fas fa-arrow-up"></i> 33.1%
                      </span>
                      <span class="text-muted">Since last month</span>
                    </p>
                  </div> -->

                <!-- /.d-flex -->

                <!-- <div class="position-relative mb-4">
                    <canvas id="sales-chart" height="200"></canvas>
                  </div>

                  <div class="d-flex flex-row justify-content-end">
                    <span class="mr-2">
                      <i class="fas fa-square text-primary"></i> This year
                    </span>

                    <span>
                      <i class="fas fa-square text-gray"></i> Last year
                    </span>
                  </div>
                </div> -->

              </div>
              <!-- /.card -->

              <div class="card">
                <div class="card-header border-0">
                  <h3 class="card-title">
                    <font size="+2"><strong>Data</strong></font>
                  </h3>
                  <div class="card-tools">
                    <a href="#" class="btn btn-sm btn-tool">
                      <i class="fas fa-download"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-tool">
                      <i class="fas fa-bars"></i>
                    </a>
                  </div>
                </div>
                <div class="card-body">
                  <!-- <style class="factory_img">
                    .factory_img {
                      text-align: center;
                    }
                  </style>
                  <div class="factory_img"><img src="<?php echo $data_image; ?>" width=fill></div> -->
                  <style>
                    #plotlyGraph {
                      float: left;
                    }

                    #plotlyGraph2 {
                      float: right;
                    }
                  </style>
                  <div>
                    <div id="plotlyGraph"></div>
                    <div id="plotlyGraph2"></div>
                  </div>
                  <script>
                    var xarr = ["2021-01-07 17:20:46", "2021-01-08 17:25:39", "2021-01-08 23:27:10", "2021-01-09 05:30:19", "2021-01-09 11:31:34", "2021-01-09 17:33:01", "2021-01-09 23:34:24", "2021-01-10 05:35:48", "2021-01-10 11:36:32", "2021-01-10 17:37:38", "2021-01-11 11:41:38", "2021-01-11 17:43:04", "2021-01-12 17:46:02", "2021-01-29 05:29:19", "2021-01-29 05:32:40", "2021-01-29 05:35:36", "2021-01-29 05:39:43", "2021-01-29 05:42:06", "2021-01-29 05:46:53", "2021-01-29 05:50:28", "2021-01-29 05:54:01", "2021-01-29 05:57:33", "2021-01-29 06:04:00", "2021-01-29 06:05:58", "2021-01-29 06:12:45", "2021-01-29 06:19:26", "2021-01-29 06:25:59", "2021-01-29 06:34:21", "2021-01-29 06:41:14", "2021-01-29 06:48:18", "2021-01-29 06:54:17", "2021-01-29 07:04:24", "2021-01-29 07:12:13", "2021-01-29 07:19:13", "2021-01-29 07:25:58", "2021-01-29 07:32:38", "2021-01-29 07:39:04", "2021-01-29 07:45:45", "2021-01-29 07:51:33", "2021-01-29 07:59:20", "2021-01-29 08:06:03", "2021-01-29 08:12:44", "2021-01-29 08:19:29", "2021-01-29 08:25:55", "2021-01-29 08:32:44", "2021-01-29 08:38:27", "2021-01-29 08:45:11", "2021-01-29 08:51:50", "2021-01-29 08:58:32", "2021-01-29 09:05:18", "2021-01-29 09:11:41", "2021-01-29 09:18:30", "2021-01-29 09:24:22", "2021-01-29 09:31:03", "2021-01-29 09:37:44", "2021-01-29 09:44:37", "2021-01-29 09:51:18", "2021-01-29 09:57:06", "2021-01-29 10:03:47", "2021-01-29 10:10:31", "2021-01-29 10:17:13", "2021-01-29 10:23:54", "2021-01-29 10:30:19", "2021-01-29 10:37:07", "2021-01-29 10:43:50", "2021-01-29 10:50:32", "2021-01-29 10:57:11", "2021-01-29 11:03:42", "2021-01-29 11:10:23", "2021-01-29 11:16:14", "2021-01-29 11:22:59", "2021-01-29 11:29:43", "2021-01-29 11:36:33", "2021-01-29 11:43:12", "2021-01-29 11:49:40", "2021-01-29 11:56:19", "2021-01-29 12:03:06", "2021-01-29 12:09:46", "2021-01-29 12:16:26", "2021-01-29 12:23:06", "2021-01-29 12:29:48", "2021-01-29 12:36:17", "2021-01-29 12:43:18", "2021-01-29 12:49:46", "2021-01-29 12:56:45", "2021-01-29 13:03:32", "2021-01-29 13:10:13", "2021-01-29 13:16:53", "2021-01-29 13:22:41", "2021-01-29 13:29:20", "2021-01-29 13:35:06", "2021-01-29 13:41:48", "2021-01-29 13:48:29", "2021-01-29 13:55:13", "2021-01-29 14:01:54", "2021-01-29 14:08:21", "2021-01-29 14:15:05", "2021-01-29 14:21:31", "2021-01-29 14:28:11", "2021-01-29 14:34:53", "2021-01-29 14:41:35", "2021-01-29 14:48:16", "2021-01-29 14:55:07", "2021-01-29 15:01:48", "2021-01-29 15:08:11", "2021-01-29 15:14:51", "2021-01-29 15:21:11", "2021-01-29 15:27:53", "2021-01-29 15:34:35", "2021-01-29 15:40:39", "2021-01-29 15:47:20", "2021-01-29 15:53:46", "2021-01-29 16:00:27", "2021-01-29 16:06:19", "2021-01-29 16:13:01", "2021-01-29 16:19:47", "2021-01-29 16:26:36", "2021-01-29 16:33:20", "2021-01-29 16:39:46", "2021-01-29 16:46:27", "2021-01-29 16:52:49", "2021-01-29 16:59:29", "2021-01-29 17:06:09", "2021-01-29 17:12:15", "2021-01-29 17:19:03", "2021-01-29 17:24:50", "2021-01-29 17:31:29", "2021-01-29 17:38:11", "2021-01-29 17:44:54", "2021-01-29 17:51:37", "2021-01-29 17:58:00", "2021-01-29 18:04:41", "2021-01-29 18:11:08", "2021-01-29 18:17:49", "2021-01-29 18:24:29", "2021-01-29 18:31:10", "2021-01-29 18:37:51", "2021-01-29 18:44:18", "2021-01-29 18:50:59", "2021-01-29 18:57:39", "2021-01-29 19:04:19", "2021-01-29 19:11:00", "2021-01-29 19:17:27", "2021-01-29 19:24:11", "2021-01-29 19:30:06", "2021-01-29 19:36:48", "2021-01-29 19:43:30", "2021-01-29 19:49:53", "2021-01-29 19:56:35", "2021-01-29 20:02:19", "2021-01-29 20:09:05", "2021-01-29 20:15:46", "2021-01-29 20:22:27", "2021-01-29 20:29:10", "2021-01-29 20:35:36", "2021-01-29 20:42:18", "2021-01-29 20:48:47", "2021-01-29 20:55:27", "2021-01-29 21:02:08", "2021-01-29 21:08:49", "2021-01-29 21:15:28", "2021-01-29 21:21:53", "2021-01-29 21:28:37", "2021-01-29 21:35:02", "2021-01-29 21:41:43", "2021-01-29 21:48:23", "2021-01-29 21:55:30", "2021-01-29 22:02:10", "2021-01-29 22:08:35", "2021-01-29 22:15:17", "2021-01-29 22:21:44", "2021-01-29 22:28:25", "2021-01-29 22:35:10", "2021-01-29 22:41:50", "2021-01-29 22:48:31", "2021-01-29 22:54:55", "2021-01-29 23:01:41", "2021-01-29 23:07:48", "2021-01-29 23:14:30", "2021-01-29 23:21:09", "2021-01-29 23:27:55", "2021-01-29 23:34:36", "2021-01-29 23:42:00", "2021-01-29 23:48:40", "2021-01-29 23:54:32", "2021-01-30 00:01:12", "2021-01-30 00:07:55", "2021-01-30 00:14:43", "2021-01-30 00:21:23", "2021-01-30 00:27:46", "2021-01-30 00:34:27", "2021-01-30 00:40:11", "2021-01-30 00:46:51", "2021-01-30 00:53:37", "2021-01-30 01:00:26", "2021-01-30 01:07:06", "2021-01-30 01:12:49", "2021-01-30 01:19:33", "2021-01-30 01:26:14", "2021-01-30 01:32:57", "2021-01-30 01:39:38", "2021-01-30 01:46:01", "2021-01-30 01:52:41", "2021-01-30 01:59:06", "2021-01-30 02:05:45", "2021-01-30 02:12:36", "2021-01-30 02:19:25", "2021-01-30 02:26:08", "2021-01-30 02:31:51", "2021-01-30 02:39:00", "2021-01-30 02:44:58", "2021-01-30 02:51:38", "2021-01-30 02:58:19", "2021-01-30 03:05:03", "2021-01-30 03:11:42", "2021-01-30 03:18:05", "2021-01-30 03:24:45", "2021-01-30 03:30:47", "2021-01-30 03:37:27", "2021-01-30 03:44:09", "2021-01-30 03:50:43", "2021-01-30 03:57:23", "2021-01-30 04:03:16", "2021-01-30 04:09:58", "2021-01-30 04:16:37", "2021-01-30 04:23:24", "2021-01-30 04:30:04", "2021-01-30 04:36:29", "2021-01-30 04:43:08", "2021-01-30 04:49:12", "2021-01-30 04:55:53", "2021-02-01 02:28:16", "2021-02-01 02:36:03", "2021-02-01 02:42:43", "2021-02-01 02:49:25", "2021-02-01 02:56:08", "2021-02-01 03:02:37", "2021-02-01 03:09:22", "2021-02-01 03:15:49", "2021-02-01 03:23:01", "2021-02-01 03:30:50", "2021-02-01 03:43:47", "2021-02-01 03:50:14", "2021-02-01 03:56:55", "2021-02-01 04:02:41", "2021-02-01 04:09:24", "2021-02-01 04:16:06", "2021-02-01 04:23:26", "2021-02-01 04:30:07", "2021-02-01 04:36:51", "2021-02-01 04:43:36", "2021-02-01 04:50:02", "2021-02-01 04:56:42", "2021-02-01 05:03:24", "2021-02-01 05:10:05", "2021-02-01 05:16:46", "2021-02-01 05:23:13", "2021-02-01 05:29:55", "2021-02-01 05:35:50", "2021-02-01 05:42:31", "2021-02-01 05:49:12", "2021-02-01 05:55:58", "2021-02-01 06:02:42", "2021-02-01 06:08:36", "2021-02-01 06:15:18", "2021-02-01 06:21:59", "2021-02-01 06:28:52", "2021-02-01 06:35:34", "2021-02-01 06:42:03", "2021-02-01 06:49:51", "2021-02-01 06:56:16", "2021-02-01 07:02:57", "2021-02-01 07:09:40", "2021-02-01 07:16:26", "2021-02-01 07:23:08", "2021-02-01 07:29:30", "2021-02-01 07:36:26", "2021-02-01 07:44:46", "2021-02-01 07:51:28", "2021-02-01 07:58:08", "2021-02-01 08:04:37", "2021-02-01 08:11:18", "2021-02-01 08:17:43", "2021-02-01 08:24:28", "2021-02-01 08:31:14", "2021-02-01 08:37:57", "2021-02-01 08:44:41", "2021-02-01 08:51:29", "2021-02-01 08:58:10", "2021-02-01 09:04:35", "2021-02-01 09:11:17", "2021-02-01 09:17:09", "2021-02-01 09:23:48", "2021-02-01 09:30:27", "2021-02-01 09:37:13", "2021-02-01 09:45:04", "2021-02-01 09:51:32", "2021-02-01 09:58:14", "2021-02-01 10:03:58", "2021-02-01 10:10:44", "2021-02-01 10:17:24", "2021-02-01 10:24:09", "2021-02-01 10:30:49", "2021-02-01 10:37:19", "2021-02-01 10:44:03", "2021-02-01 10:50:56", "2021-02-01 10:57:37", "2021-02-01 11:05:31", "2021-02-01 11:12:10", "2021-02-01 11:18:55", "2021-02-01 11:25:42", "2021-02-01 11:33:27", "2021-02-01 11:39:52", "2021-02-01 11:46:33", "2021-02-01 11:54:01", "2021-02-01 12:00:45", "2021-02-01 12:08:48", "2021-02-01 12:15:32", "2021-02-01 12:22:13", "2021-02-01 12:29:05", "2021-02-01 12:36:51", "2021-02-01 12:43:17", "2021-02-01 12:50:00", "2021-02-01 12:56:29", "2021-02-01 13:10:22", "2021-02-01 13:17:03", "2021-02-01 13:23:43", "2021-02-01 13:30:06", "2021-02-01 13:36:46", "2021-02-01 13:43:38", "2021-02-01 13:56:34", "2021-02-01 14:03:19", "2021-02-01 14:15:50", "2021-02-01 14:22:36", "2021-02-01 14:29:18", "2021-02-01 14:49:00", "2021-02-01 15:01:17", "2021-02-01 15:07:57", "2021-02-01 15:14:36", "2021-02-01 15:20:19", "2021-02-01 15:27:00", "2021-02-01 15:34:29", "2021-02-01 15:41:09", "2021-02-01 15:47:00", "2021-02-01 15:59:49", "2021-02-01 16:06:15", "2021-02-01 16:13:03", "2021-02-01 16:19:37", "2021-02-01 16:26:16", "2021-02-01 16:33:09", "2021-02-01 16:39:55", "2021-02-01 16:46:50", "2021-02-01 16:53:33", "2021-02-01 17:00:18", "2021-02-01 17:06:47", "2021-02-01 17:13:32", "2021-02-01 17:20:21", "2021-02-01 17:27:00", "2021-02-01 17:33:45", "2021-02-01 17:40:33", "2021-02-01 17:47:14", "2021-02-01 17:53:37", "2021-02-01 18:00:20", "2021-02-01 18:07:07", "2021-02-01 18:15:12", "2021-02-01 18:21:54", "2021-02-01 18:28:40", "2021-02-01 18:35:20", "2021-02-01 18:41:48", "2021-02-01 18:48:27", "2021-02-01 18:54:22", "2021-02-01 19:01:17", "2021-02-01 19:08:16", "2021-02-01 19:14:11", "2021-02-01 19:20:50", "2021-02-01 19:27:16", "2021-02-01 19:33:56", "2021-02-01 19:39:36", "2021-02-01 19:46:19", "2021-02-01 19:52:58", "2021-02-01 19:59:46", "2021-02-01 20:06:26", "2021-02-01 20:12:49", "2021-02-01 20:19:28", "2021-02-01 20:26:12", "2021-02-01 20:32:52", "2021-02-01 20:39:31", "2021-02-01 20:45:58", "2021-02-01 20:52:37", "2021-02-01 21:00:05", "2021-02-01 21:06:46", "2021-02-01 21:12:38", "2021-02-01 21:19:17", "2021-02-01 21:25:56", "2021-02-01 21:32:43", "2021-02-01 21:39:26", "2021-02-01 21:45:49", "2021-02-01 21:52:29", "2021-02-01 21:59:13", "2021-02-01 22:05:59", "2021-02-01 22:12:39", "2021-02-01 22:19:28", "2021-02-01 22:26:08", "2021-02-01 22:32:33", "2021-02-01 22:39:11", "2021-02-01 22:45:51", "2021-02-01 22:52:33", "2021-02-01 22:59:00", "2021-02-01 23:05:22", "2021-02-01 23:12:03", "2021-02-01 23:18:33", "2021-02-01 23:29:39", "2021-02-01 23:41:23", "2021-02-01 23:58:45", "2021-02-02 00:11:51", "2021-02-02 00:23:43", "2021-02-02 00:35:22", "2021-02-02 00:46:07", "2021-02-02 00:59:04", "2021-02-02 01:10:30", "2021-02-02 01:22:13", "2021-02-02 01:35:30", "2021-02-02 01:48:39", "2021-02-02 02:00:25", "2021-02-02 02:13:10", "2021-02-02 02:24:50", "2021-02-02 02:35:57", "2021-02-02 02:49:05", "2021-02-02 03:02:44", "2021-02-02 03:15:31", "2021-02-02 03:27:12", "2021-02-02 03:40:18", "2021-02-02 03:53:23", "2021-02-02 04:06:10", "2021-02-02 04:17:52", "2021-02-02 04:30:57", "2021-02-04 02:28:30", "2021-02-04 02:41:08", "2021-02-04 02:50:51", "2021-02-04 03:00:13", "2021-02-04 03:11:42", "2021-02-04 03:23:09", "2021-02-04 03:35:33", "2021-02-04 03:44:43", "2021-02-04 03:54:53", "2021-02-04 04:04:15", "2021-02-04 04:28:31", "2021-02-04 04:57:38", "2021-02-04 05:23:38", "2021-02-04 05:47:28", "2021-02-04 06:10:52", "2021-02-04 06:36:06", "2021-02-04 07:00:55", "2021-02-04 07:26:53", "2021-02-04 07:53:53", "2021-02-04 08:17:39", "2021-02-04 08:42:33", "2021-02-04 09:07:26", "2021-02-04 09:30:49", "2021-02-04 09:56:49", "2021-02-04 10:22:29", "2021-02-04 10:47:23", "2021-02-04 11:12:20", "2021-02-04 11:36:09", "2021-02-04 12:01:01", "2021-02-04 12:24:48", "2021-02-04 12:49:39", "2021-02-04 13:13:27", "2021-02-04 13:38:17", "2021-02-04 14:03:32", "2021-02-04 14:27:18", "2021-02-04 14:52:13", "2021-02-04 15:17:07", "2021-02-04 15:41:58", "2021-02-04 16:05:47", "2021-02-04 16:34:53", "2021-02-04 16:58:38", "2021-02-04 17:23:31", "2021-02-04 17:47:22", "2021-02-04 18:12:13", "2021-02-04 18:35:59", "2021-02-04 19:00:50", "2021-02-04 19:25:42", "2021-02-04 19:54:51", "2021-02-04 20:18:41", "2021-02-04 20:43:33", "2021-02-04 21:08:49", "2021-02-04 21:33:59", "2021-02-04 21:57:46", "2021-02-04 22:23:28", "2021-02-04 22:48:19", "2021-02-04 23:15:19", "2021-02-04 23:39:09", "2021-02-05 00:03:57", "2021-02-05 00:27:57", "2021-02-05 00:51:59", "2021-02-05 01:16:50", "2021-02-05 01:43:56", "2021-02-05 02:07:53", "2021-02-05 02:32:47", "2021-02-05 02:58:48", "2021-02-05 03:24:47", "2021-02-05 03:52:35", "2021-02-05 04:19:31", "2021-02-05 04:46:37", "2021-02-05 06:08:07", "2021-02-05 06:33:29", "2021-02-05 06:57:17", "2021-02-05 07:22:59", "2021-02-05 07:46:55", "2021-02-05 08:11:52", "2021-02-05 08:35:40", "2021-02-05 09:00:20", "2021-02-05 09:24:09", "2021-02-05 09:49:53", "2021-02-05 10:13:45", "2021-02-05 10:38:40", "2021-02-05 11:01:51", "2021-02-05 11:26:46", "2021-02-05 11:54:51", "2021-02-05 12:20:37", "2021-02-05 12:44:14", "2021-02-05 13:09:09", "2021-02-05 13:34:25", "2021-02-05 13:59:31", "2021-02-05 23:44:12", "2021-02-06 00:08:05", "2021-02-06 00:36:14", "2021-02-06 01:00:03", "2021-02-06 01:24:50", "2021-02-06 01:48:43", "2021-02-06 02:13:41", "2021-02-06 02:38:17", "2021-02-06 03:01:57", "2021-02-06 03:27:58", "2021-02-08 00:18:27", "2021-03-02 12:16:55", "2021-03-02 14:00:07", "2021-03-02 17:54:17", "2021-03-02 22:41:32", "2021-03-03 12:51:36", "2021-03-05 01:51:18", "2021-03-05 02:12:55", "2021-03-05 02:23:51", "2021-03-05 02:34:34", "2021-03-05 02:44:04", "2021-03-05 02:53:10", "2021-03-05 03:02:51", "2021-03-05 03:12:13", "2021-03-05 03:21:04", "2021-03-05 03:30:54", "2021-03-05 04:03:03", "2021-03-05 04:38:18", "2021-03-05 04:47:38", "2021-03-05 04:56:52", "2021-03-05 05:06:12", "2021-03-05 05:15:42", "2021-03-05 05:25:06", "2021-03-05 05:34:35", "2021-03-05 05:43:01", "2021-03-05 05:52:26", "2021-03-05 06:01:45", "2021-03-05 06:11:09", "2021-03-05 06:20:12", "2021-03-05 06:30:35", "2021-03-05 06:39:31", "2021-03-05 06:49:02", "2021-03-05 06:58:40", "2021-03-05 07:07:04", "2021-03-05 07:16:31", "2021-03-05 07:25:42", "2021-03-05 07:35:27", "2021-03-05 07:44:46", "2021-03-05 07:53:22", "2021-03-05 08:02:46", "2021-03-05 08:15:44", "2021-03-05 08:25:09", "2021-03-05 09:06:22", "2021-03-05 09:15:47", "2021-03-05 09:24:52", "2021-03-05 09:34:09", "2021-03-05 09:43:13", "2021-03-05 09:52:40", "2021-03-05 10:02:08", "2021-03-05 10:11:48", "2021-03-05 10:21:29", "2021-03-05 10:30:39", "2021-03-05 10:40:04", "2021-03-05 10:49:27", "2021-03-05 10:58:54", "2021-03-05 11:07:59", "2021-03-05 11:17:31", "2021-03-05 11:27:15", "2021-03-07 23:43:16", "2021-03-07 23:52:20", "2021-03-08 00:01:44", "2021-03-08 00:11:15", "2021-03-08 00:20:45", "2021-03-08 00:30:14", "2021-03-08 00:38:40", "2021-03-08 00:48:05", "2021-03-08 00:58:11", "2021-03-08 01:07:30", "2021-03-08 01:16:54", "2021-03-08 01:25:26", "2021-03-08 01:34:56", "2021-03-08 01:44:09", "2021-03-08 01:53:34", "2021-03-08 02:02:50", "2021-03-08 02:12:18", "2021-03-08 02:21:40", "2021-03-08 02:30:48", "2021-03-08 02:40:10", "2021-03-08 02:49:41", "2021-03-08 03:00:13", "2021-03-08 03:09:36", "2021-03-08 03:20:46", "2021-03-08 03:30:35", "2021-03-08 04:37:22", "2021-03-08 04:47:37", "2021-03-08 04:58:47", "2021-03-08 05:08:33", "2021-03-08 05:17:58", "2021-03-08 05:27:23", "2021-03-08 05:36:41", "2021-03-08 05:46:03", "2021-03-08 05:56:09", "2021-03-08 06:05:29", "2021-03-08 06:14:52", "2021-03-08 06:23:30", "2021-03-08 06:32:54", "2021-03-08 06:41:41", "2021-03-08 06:51:12", "2021-03-08 07:02:33", "2021-03-08 07:11:56", "2021-03-08 07:22:19", "2021-03-08 07:31:13", "2021-03-08 07:40:33", "2021-03-08 07:49:58", "2021-03-08 07:59:21", "2021-03-08 08:08:42", "2021-03-08 08:17:52", "2021-03-08 08:28:26", "2021-03-08 09:09:24", "2021-03-08 09:18:31", "2021-03-08 09:27:57", "2021-03-08 09:36:45", "2021-03-08 09:46:24", "2021-03-08 09:55:54", "2021-03-08 10:05:14", "2021-03-08 10:14:43", "2021-03-08 10:23:49", "2021-03-08 10:32:57", "2021-03-08 10:42:16", "2021-03-08 10:50:42", "2021-03-08 11:00:10", "2021-03-08 11:11:22", "2021-03-08 23:37:24", "2021-03-08 23:46:52", "2021-03-08 23:57:47", "2021-03-09 00:07:18", "2021-03-09 00:16:41", "2021-03-09 00:27:03", "2021-03-09 00:36:53", "2021-03-09 00:46:26", "2021-03-09 00:56:16", "2021-03-09 01:06:41", "2021-03-09 01:16:56", "2021-03-09 01:25:37", "2021-03-09 01:35:05", "2021-03-09 01:44:28", "2021-03-09 01:55:04", "2021-03-09 02:05:44", "2021-03-09 02:16:00", "2021-03-09 02:25:32", "2021-03-09 02:34:23", "2021-03-09 02:44:13", "2021-03-09 02:53:48", "2021-03-09 03:02:37", "2021-03-09 03:11:59", "2021-03-09 03:21:20", "2021-03-09 03:31:57", "2021-03-09 04:35:09", "2021-03-09 04:44:32", "2021-03-09 04:53:34", "2021-03-09 05:03:01", "2021-03-09 05:12:15", "2021-03-09 05:21:40", "2021-03-09 05:31:03", "2021-03-09 05:40:39", "2021-03-09 05:50:03", "2021-03-09 05:59:07", "2021-03-09 06:08:44", "2021-03-09 06:19:20", "2021-03-09 06:28:31", "2021-03-09 06:38:07", "2021-03-09 06:46:57", "2021-03-09 06:56:29", "2021-03-09 07:05:58", "2021-03-09 07:14:35", "2021-03-09 07:23:54", "2021-03-09 07:33:29", "2021-03-09 07:43:03", "2021-03-09 07:53:43", "2021-03-09 08:03:48", "2021-03-09 08:14:29", "2021-03-09 08:24:00", "2021-03-09 09:04:31", "2021-03-09 09:17:15", "2021-03-09 09:29:56", "2021-03-09 09:39:41", "2021-03-09 09:49:03", "2021-03-09 09:58:35", "2021-03-09 10:07:55", "2021-03-09 10:17:24", "2021-03-09 10:27:03", "2021-03-09 10:36:49", "2021-03-09 10:46:24", "2021-03-09 10:55:38", "2021-03-09 11:05:02", "2021-03-09 11:15:31", "2021-03-09 11:24:39", "2021-03-09 11:34:06", "2021-03-09 11:45:09", "2021-03-09 11:54:35", "2021-03-09 12:04:04", "2021-03-09 12:12:38", "2021-03-09 12:22:02", "2021-03-09 12:32:27", "2021-03-10 10:54:36", "2021-03-10 23:36:29", "2021-03-10 23:45:48", "2021-03-10 23:54:28", "2021-03-11 00:05:13", "2021-03-11 00:14:34", "2021-03-11 00:23:58", "2021-03-11 00:33:26", "2021-03-11 00:43:30", "2021-03-11 00:53:08", "2021-03-11 01:03:05", "2021-03-11 01:12:19", "2021-03-11 01:21:51", "2021-03-11 01:32:02", "2021-03-11 01:41:35", "2021-03-11 01:51:27", "2021-03-11 02:00:18", "2021-03-11 02:09:39", "2021-03-11 02:19:06", "2021-03-11 02:29:15", "2021-03-11 02:40:31", "2021-03-11 02:50:07", "2021-03-11 02:59:37", "2021-03-11 03:09:00", "2021-03-11 03:18:29", "2021-03-11 03:28:04", "2021-03-11 04:33:11", "2021-03-11 04:43:22", "2021-03-11 04:53:12", "2021-03-11 05:02:48", "2021-03-11 05:13:19", "2021-03-11 05:22:45", "2021-03-11 05:32:15", "2021-03-11 05:41:49", "2021-03-11 05:51:27", "2021-03-11 06:00:43", "2021-03-11 06:10:28", "2021-03-11 06:19:59", "2021-03-11 06:31:21", "2021-03-11 06:40:57", "2021-03-11 06:49:54", "2021-03-11 06:59:23", "2021-03-11 07:08:43", "2021-03-11 07:19:16", "2021-03-11 07:29:58", "2021-03-11 07:38:33", "2021-03-11 07:47:59", "2021-03-11 07:56:21", "2021-03-11 08:07:19", "2021-03-11 08:18:20", "2021-03-11 08:26:52", "2021-03-11 09:07:25", "2021-03-11 09:17:41", "2021-03-11 09:27:50", "2021-03-11 09:37:14", "2021-03-11 09:48:15", "2021-03-11 09:57:37", "2021-03-11 10:07:18", "2021-03-11 10:16:39", "2021-03-11 10:26:15", "2021-03-11 10:35:47", "2021-03-11 10:44:19", "2021-03-11 10:53:53", "2021-03-11 11:04:18", "2021-03-11 11:13:46", "2021-03-11 11:23:17", "2021-03-11 11:31:46", "2021-03-12 01:18:27", "2021-03-12 01:27:49", "2021-03-12 01:36:52", "2021-03-12 01:45:57", "2021-03-12 01:55:30", "2021-03-12 02:04:29", "2021-03-12 02:14:07", "2021-03-12 02:23:53", "2021-03-12 02:34:15", "2021-03-12 02:43:35", "2021-03-12 05:32:37", "2021-03-12 05:41:44", "2021-03-12 05:51:19", "2021-03-12 06:01:00", "2021-03-12 06:09:32", "2021-03-12 06:19:23", "2021-03-12 06:29:42", "2021-03-12 06:39:13", "2021-03-12 06:48:45", "2021-03-12 06:58:56", "2021-03-12 07:08:23", "2021-03-12 07:17:43", "2021-03-12 07:26:33", "2021-03-12 07:36:54", "2021-03-12 07:46:33", "2021-03-12 07:56:22", "2021-03-12 08:05:36", "2021-03-12 08:14:18", "2021-03-12 08:23:40", "2021-03-12 23:36:40", "2021-03-12 23:46:14", "2021-03-12 23:56:48", "2021-03-13 00:06:16", "2021-03-13 00:15:25", "2021-03-13 00:24:55", "2021-03-13 00:34:33", "2021-03-13 00:43:12", "2021-03-13 00:54:09", "2021-03-13 01:04:09", "2021-03-13 01:13:34", "2021-03-13 01:23:08", "2021-03-13 01:32:27", "2021-03-13 01:41:50", "2021-03-13 01:51:16", "2021-03-13 02:01:49", "2021-03-13 02:11:24", "2021-03-13 02:20:38", "2021-03-13 02:30:05", "2021-03-13 02:39:31", "2021-03-13 02:48:55", "2021-03-13 02:58:28", "2021-03-13 03:07:48", "2021-03-13 03:17:04", "2021-03-13 03:27:55", "2021-03-13 04:33:00", "2021-03-13 04:42:32", "2021-03-13 04:51:50", "2021-03-13 05:01:46", "2021-03-13 05:10:48", "2021-03-13 05:20:14", "2021-03-13 05:29:44", "2021-03-13 05:39:43", "2021-03-13 05:49:16", "2021-03-13 05:59:19", "2021-03-13 06:09:01", "2021-03-13 06:18:47", "2021-03-13 06:28:04", "2021-03-13 06:37:59", "2021-03-13 06:47:29", "2021-03-13 06:57:15", "2021-03-13 07:07:06", "2021-03-13 07:15:41", "2021-03-13 07:25:56", "2021-03-13 07:37:01", "2021-03-13 07:46:32", "2021-03-13 07:55:49", "2021-03-13 08:05:01", "2021-03-13 08:14:31", "2021-03-13 08:23:59", "2021-03-14 23:34:40", "2021-03-14 23:44:17", "2021-03-14 23:53:47", "2021-03-15 00:02:21", "2021-03-15 00:12:56", "2021-03-15 00:23:15", "2021-03-15 00:32:46", "2021-03-15 00:42:32", "2021-03-15 00:51:48", "2021-03-15 01:01:03", "2021-03-15 01:10:09", "2021-03-15 01:20:19", "2021-03-15 01:29:37", "2021-03-15 01:39:56", "2021-03-15 01:49:31", "2021-03-15 02:02:27", "2021-03-15 02:11:36", "2021-03-15 02:20:57", "2021-03-15 02:30:29", "2021-03-15 02:39:54", "2021-03-15 02:49:20", "2021-03-15 02:58:26", "2021-03-15 03:07:45", "2021-03-15 03:18:11", "2021-03-15 03:26:52", "2021-03-15 04:32:47", "2021-03-15 04:42:40", "2021-03-15 04:52:05", "2021-03-15 05:00:43", "2021-03-15 05:10:06", "2021-03-15 05:19:32", "2021-03-15 05:28:55", "2021-03-15 05:38:28", "2021-03-15 05:48:14", "2021-03-15 05:57:34", "2021-03-15 06:06:12", "2021-03-15 06:15:41", "2021-03-15 06:24:59", "2021-03-15 06:34:22", "2021-03-15 06:43:09", "2021-03-15 06:52:35", "2021-03-15 07:01:36", "2021-03-15 07:11:34", "2021-03-15 07:21:05", "2021-03-15 07:30:59", "2021-03-15 07:40:21", "2021-03-15 07:49:45", "2021-03-15 07:58:22", "2021-03-15 08:07:40", "2021-03-15 08:16:58", "2021-03-15 08:26:35", "2021-03-15 09:08:31", "2021-03-15 09:19:57", "2021-03-15 09:29:11", "2021-03-15 09:38:56", "2021-03-15 09:47:54", "2021-03-15 09:56:14", "2021-03-15 10:05:40", "2021-03-15 10:40:17", "2021-03-15 10:49:44", "2021-03-15 10:59:16", "2021-03-15 11:08:36", "2021-03-15 11:18:10", "2021-03-15 11:28:13", "2021-03-15 11:37:20", "2021-03-15 23:35:17", "2021-03-15 23:45:49", "2021-03-15 23:55:23", "2021-03-16 00:04:35", "2021-03-16 00:14:23", "2021-03-16 00:24:01", "2021-03-16 00:33:51", "2021-03-16 00:43:37", "2021-03-16 00:52:40", "2021-03-16 01:02:21", "2021-03-16 01:12:08", "2021-03-16 01:21:52", "2021-03-16 01:31:23", "2021-03-16 01:40:17", "2021-03-16 01:49:48", "2021-03-16 01:58:51", "2021-03-16 02:08:19", "2021-03-16 02:17:53", "2021-03-16 02:26:44", "2021-03-16 02:36:16", "2021-03-16 02:44:47", "2021-03-16 02:54:00", "2021-03-16 03:03:35", "2021-03-16 03:13:21", "2021-03-16 03:22:48", "2021-03-16 03:31:21", "2021-03-16 04:35:25", "2021-03-16 04:47:04", "2021-03-16 04:59:56", "2021-03-16 05:09:26", "2021-03-16 05:18:48", "2021-03-16 05:28:23", "2021-03-16 05:37:05", "2021-03-16 05:46:58", "2021-03-16 05:56:13", "2021-03-16 06:05:44", "2021-03-16 06:15:35", "2021-03-16 06:25:22", "2021-03-16 06:33:58", "2021-03-16 06:43:30", "2021-03-16 06:51:56", "2021-03-16 07:01:25", "2021-03-16 07:11:05", "2021-03-16 07:21:03", "2021-03-16 07:30:26", "2021-03-16 07:39:39", "2021-03-16 07:49:44", "2021-03-16 07:59:15", "2021-03-16 08:09:04", "2021-03-16 08:19:07", "2021-03-16 08:29:00", "2021-03-16 09:06:07", "2021-03-16 09:14:46", "2021-03-16 09:24:16", "2021-03-16 09:34:15", "2021-03-16 09:43:40", "2021-03-16 09:53:32", "2021-03-16 10:03:20", "2021-03-16 10:12:45", "2021-03-16 10:21:52"];
                    var yarr = [0.013275440782308578, 0.013983958400785923, 0.015409713611006737, 0.013767682947218418, 0.012600678019225597, 0.01329796202480793, 0.013126992620527744, 0.013952427543699741, 0.013501881621778011, 0.0136896762996912, 0.012598970904946327, 0.013510122895240784, 0.01316357683390379, 0.014702110551297665, 0.013014286756515503, 0.013722852803766727, 0.01358884572982788, 0.0148940309882164, 0.014612666331231594, 0.014079870656132698, 0.01343792024999857, 0.014153523370623589, 0.014946556650102139, 0.014786154963076115, 0.01357706356793642, 0.013511464931070805, 0.01490695308893919, 0.013319708406925201, 0.013903966173529625, 0.014044887386262417, 0.013938053511083126, 0.014158951118588448, 0.015254253521561623, 0.013880928047001362, 0.014006256125867367, 0.014648057520389557, 0.01467751432210207, 0.014094274491071701, 0.014221723191440105, 0.014464465901255608, 0.014268292114138603, 0.026225637644529343, 0.015872865915298462, 0.013779879547655582, 0.014387560077011585, 0.014865009114146233, 0.014736046083271503, 0.014345895498991013, 0.014414842240512371, 0.015061085112392902, 0.014920501969754696, 0.014470362104475498, 0.014311576262116432, 0.014396609738469124, 0.014393472112715244, 0.013492451049387455, 0.013402328826487064, 0.014786365441977978, 0.01506635919213295, 0.013446193188428879, 0.015132712200284004, 0.014636058360338211, 0.015220222994685173, 0.014585618861019611, 0.01380582619458437, 0.015207335352897644, 0.014835095964372158, 0.015468372032046318, 0.015059871599078178, 0.014286304824054241, 0.015228490345180035, 0.01431263331323862, 0.014931624755263329, 0.013493787497282028, 0.014632884413003922, 0.014059312641620636, 0.013955269008874893, 0.01382236648350954, 0.015725456178188324, 0.014612649567425251, 0.013671793043613434, 0.015262504108250141, 0.012851624749600887, 0.013287799432873726, 0.013621791265904903, 0.014214681461453438, 0.014102661982178688, 0.01351770106703043, 0.014296448789536953, 0.014482861384749413, 0.013639737851917744, 0.014218742959201336, 0.014370296150445938, 0.014128537848591805, 0.01376404520124197, 0.013873188756406307, 0.014652847312390804, 0.015069962479174137, 0.015625348314642906, 0.015001687221229076, 0.014797738753259182, 0.01379441563040018, 0.01394062303006649, 0.01513782236725092, 0.015381403267383575, 0.013858377002179623, 0.013627530075609684, 0.014411977492272854, 0.014207083731889725, 0.01483247522264719, 0.01400200929492712, 0.015233250334858894, 0.015410411171615124, 0.014574953354895115, 0.01528897788375616, 0.015111668035387993, 0.014951808378100395, 0.014300673268735409, 0.015061652287840843, 0.015049269422888756, 0.014568927697837353, 0.015452268533408642, 0.014496596530079842, 0.015445255674421787, 0.0143581572920084, 0.014992973767220974, 0.013417697511613369, 0.015284686349332333, 0.014478864148259163, 0.013894819654524326, 0.014276840724050999, 0.014751194044947624, 0.014909360557794571, 0.014353590086102486, 0.014597886241972446, 0.013647161424160004, 0.013855714350938797, 0.013646953739225864, 0.014522709883749485, 0.014616350643336773, 0.013563557527959347, 0.013745341449975967, 0.014097750186920166, 0.013672104105353355, 0.014063911512494087, 0.014111747965216637, 0.014313883148133755, 0.014930293895304203, 0.015493056736886501, 0.0133581031113863, 0.014038726687431335, 0.014411576092243195, 0.014637717977166176, 0.014335737563669682, 0.015146891586482525, 0.013820990920066833, 0.013520698063075542, 0.014595689252018929, 0.013765783980488777, 0.015394262038171291, 0.014159119687974453, 0.0141395078971982, 0.01340272556990385, 0.014124415814876556, 0.01404889952391386, 0.01419824082404375, 0.014391048811376095, 0.014289840124547482, 0.014677979052066803, 0.014985260553658009, 0.0140720559284091, 0.014239405281841755, 0.014014845713973045, 0.015026964247226715, 0.013136878609657288, 0.01536898035556078, 0.013649034313857555, 0.014480303041636944, 0.014414133504033089, 0.014374042861163616, 0.014195775613188744, 0.013963354751467705, 0.014925424009561539, 0.014911589212715626, 0.014391466043889523, 0.0142144076526165, 0.014055665582418442, 0.01393052190542221, 0.01478038914501667, 0.014203658327460289, 0.01480608806014061, 0.013893792405724525, 0.015281261876225471, 0.015380488708615303, 0.014398171566426754, 0.014515695162117481, 0.014830696396529675, 0.014285925775766373, 0.014493729919195175, 0.014110773801803589, 0.014184407889842987, 0.013295186683535576, 0.014927331358194351, 0.014737260527908802, 0.01508928369730711, 0.015160095877945423, 0.01382081862539053, 0.01442453172057867, 0.014368853531777859, 0.014263384975492954, 0.014038363471627235, 0.014042670838534832, 0.014203792437911034, 0.014214122667908669, 0.013890499249100685, 0.015259197913110256, 0.013135780580341816, 0.01571390964090824, 0.013842610642313957, 0.01340156327933073, 0.01418859139084816, 0.01462749857455492, 0.014581129886209965, 0.01387374009937048, 0.014639222994446754, 0.014370087534189224, 0.013353223912417889, 0.014476386830210686, 0.014720939099788666, 0.013591432943940163, 0.015262777917087078, 0.21839149296283722, 0.2074105441570282, 0.20294871926307678, 0.18361078202724457, 0.21428723633289337, 0.17354217171669006, 0.2325446903705597, 0.2052927166223526, 0.16484034061431885, 0.21128135919570923, 0.010041074827313423, 0.010181809775531292, 0.00962851196527481, 0.010523260571062565, 0.009457046166062355, 0.01037839986383915, 0.01028480101376772, 0.009895418770611286, 0.011164484545588493, 0.2666079103946686, 0.21196889877319336, 0.25573527812957764, 0.26727837324142456, 0.22974732518196106, 0.20026405155658722, 0.18846651911735535, 0.18233516812324524, 0.18442079424858093, 0.23600760102272034, 0.19847702980041504, 0.19063131511211395, 0.20557938516139984, 0.18761707842350006, 0.19684094190597534, 0.18937480449676514, 0.19330942630767822, 0.19172854721546173, 0.18501593172550201, 0.1660788357257843, 0.1768679916858673, 0.1899242252111435, 0.16836073994636536, 0.2026176005601883, 0.22080984711647034, 0.19434231519699097, 0.2393072247505188, 0.21109744906425476, 0.19650012254714966, 0.3513698875904083, 0.19901181757450104, 0.5343191623687744, 0.22348693013191223, 0.5377005338668823, 0.21999843418598175, 0.011128069832921028, 0.01107180304825306, 0.010570917278528214, 0.011256519705057144, 0.22301055490970612, 0.22707274556159973, 0.21685868501663208, 0.5098605751991272, 0.22170476615428925, 0.19553886353969574, 0.3085707724094391, 0.19512206315994263, 0.19518111646175385, 0.1766943633556366, 0.18690600991249084, 0.17710989713668823, 0.19734622538089752, 0.18534809350967407, 0.20257093012332916, 0.21088674664497375, 0.27772092819213867, 0.199422687292099, 0.641104519367218, 0.19740115106105804, 0.19696950912475586, 0.6444095373153687, 0.01147420797497034, 0.01112770289182663, 0.01005606073886156, 0.011132955551147461, 0.010859012603759766, 0.010665598325431347, 0.011663631536066532, 0.011613939888775349, 0.010063748806715012, 0.011231010779738426, 0.010957975871860981, 0.010684778913855553, 0.010334622114896774, 0.011001134291291237, 0.012458236888051033, 0.010483457706868649, 0.010952603071928024, 0.010264091193675995, 0.010645492002367973, 0.010960216633975506, 0.017016001045703888, 0.016527652740478516, 0.01652383804321289, 0.013756189495325089, 0.013659785501658916, 0.014182528480887413, 0.014394639991223812, 0.014103654772043228, 0.014109163545072079, 0.013942677527666092, 0.014367697760462761, 0.013217090629041195, 0.01475565042346716, 0.014891381375491619, 0.01488548330962658, 0.014443222433328629, 0.015333721414208412, 0.0149143747985363, 0.014108922332525253, 0.014595896005630493, 0.013579027727246284, 0.014170209877192974, 0.014280010014772415, 0.013523980043828487, 0.014554332941770554, 0.01341828890144825, 0.013938523828983307, 0.014639532193541527, 0.014794278889894485, 0.013139491900801659, 0.014343319460749626, 0.01432905811816454, 0.01395330484956503, 0.013053158298134804, 0.01426529511809349, 0.014779441989958286, 0.014260824769735336, 0.01447693258523941, 0.014856942929327488, 0.013502045534551144, 0.014548865146934986, 0.015169457532465458, 0.01427400391548872, 0.01439718808978796, 0.014160319231450558, 0.014949123375117779, 0.01569480076432228, 0.014435874298214912, 0.013520698994398117, 0.014805261977016926, 0.014512871392071247, 0.014963927678763866, 0.015011458657681942, 0.015070685185492039, 0.014678174629807472, 0.01627834513783455, 0.015014751814305782, 0.01474213320761919, 0.014532621018588543, 0.015561157837510109, 0.014622749760746956, 0.01425057090818882, 0.014477722346782684, 0.014584480784833431, 0.015111817978322506, 0.014993390999734402, 0.014976589940488338, 0.01543868612498045, 0.014380701817572117, 0.013607501983642578, 0.014149470254778862, 0.015500946901738644, 0.015255927108228207, 0.014624839648604393, 0.01452972088009119, 0.013825515285134315, 0.014774504117667675, 0.01537296548485756, 0.015691054984927177, 0.01422179862856865, 0.01384071446955204, 0.1653401106595993, 0.7112914323806763, 0.5924463272094727, 0.13651645183563232, 0.5963901877403259, 0.18728817999362946, 0.6678878664970398, 0.1809200495481491, 0.5071211457252502, 0.161763995885849, 0.22228766977787018, 0.43149295449256897, 0.229219451546669, 0.1691158264875412, 0.5861587524414062, 0.5502932667732239, 0.6014312505722046, 0.5444934964179993, 0.4675309360027313, 0.01139861810952425, 0.010889174416661263, 0.01131600420922041, 0.011205420829355717, 0.2009936422109604, 0.06807603687047958, 0.06529431790113449, 0.06971952319145203, 0.07101493328809738, 0.06843335181474686, 0.05998004972934723, 0.0014337691245600581, 0.001537582604214549, 0.001420715474523604, 0.0014705031644552946, 0.0013913341099396348, 0.019454997032880783, 0.05309351906180382, 0.05828726664185524, 0.018644873052835464, 0.016816256567835808, 0.04579814150929451, 0.04099889099597931, 0.05034457892179489, 0.05497258901596069, 0.0014327550306916237, 0.03859660029411316, 0.07296384125947952, 0.017628896981477737, 0.06213269755244255, 0.06662239134311676, 0.06228432431817055, 0.001415837206877768, 0.0014097787206992507, 0.0013982260134071112, 0.0013860908802598715, 0.001418066443875432, 0.0013687240425497293, 0.0014310549013316631, 0.0014405816327780485, 0.0014357237378135324, 0.0014394347090274096, 0.0014036978827789426, 0.001412437530234456, 0.001425290829502046, 0.001370350830256939, 0.0013878497993573546, 0.001348844263702631, 0.0013564121909439564, 0.0013707991456612945, 0.0013651552144438028, 0.001352432882413268, 0.0014193201204761863, 0.001456946018151939, 0.0013821793254464865, 0.0012974656419828534, 0.0013816682621836662, 0.0013914555311203003, 0.0013728594640269876, 0.0014494553906843066, 0.0013644990976899862, 0.06226969137787819, 0.0660342201590538, 0.06797301769256592, 0.0014468035660684109, 0.0014588631456717849, 0.017479529604315758, 0.06467429548501968, 0.05560924857854843, 0.04416923224925995, 0.051382821053266525, 0.001499619334936142, 0.0013962218072265387, 0.0616740956902504, 0.05062827467918396, 0.02097257599234581, 0.05911993980407715, 0.04944153502583504, 0.04936772584915161, 0.04440741240978241, 0.00146778195630759, 0.0014570900239050388, 0.07713886350393295, 0.04907727614045143, 0.05616547167301178, 0.04736306145787239, 0.053036872297525406, 0.001511249109171331, 0.0015788014279678464, 0.0014422168023884296, 0.0015702132368460298, 0.0014950247714295983, 0.0014191630762070417, 0.0014844671823084354, 0.08094512671232224, 0.06436995416879654, 0.07787837088108063, 0.06388787925243378, 0.06875987350940704, 0.06016415357589722, 0.05208418145775795, 0.0504927784204483, 0.05116874352097511, 0.06601475924253464, 0.04147832840681076, 0.04694969952106476, 0.05264461040496826, 0.030877619981765747, 0.052841853350400925, 0.0493992455303669, 0.5146853923797607, 0.3231746554374695, 0.28222331404685974, 0.26532119512557983, 0.5494376420974731, 0.2474467158317566, 0.4258212447166443, 0.480940043926239, 0.25073516368865967, 0.32358890771865845, 0.011642002500593662, 0.1978854238986969, 0.29668861627578735, 0.4729636013507843, 0.3178592324256897, 0.48482540249824524, 0.574097752571106, 0.20523975789546967, 0.2942940294742584, 0.2012261301279068, 0.41398948431015015, 0.26291748881340027, 0.3976005017757416, 0.6187671422958374, 0.19923727214336395, 0.6671558022499084, 0.24088089168071747, 0.429201602935791, 0.3522114157676697, 0.3089407980442047, 0.20744650065898895, 0.5120353698730469, 0.21340855956077576, 0.2439335435628891, 0.206010103225708, 0.4748678505420685, 0.15360766649246216, 0.44609904289245605, 0.5346243977546692, 0.37991344928741455, 0.32756227254867554, 0.3448830246925354, 0.5503174066543579, 0.6138709783554077, 0.19049768149852753, 0.2230807989835739, 0.20295675098896027, 0.3845246136188507, 0.4866471588611603, 0.5352272987365723, 0.21165886521339417, 0.20008297264575958, 0.4023685157299042, 0.16353754699230194, 0.16836132109165192, 0.23967400193214417, 0.6036930084228516, 0.8513386249542236, 0.26250654458999634, 0.5188311338424683, 0.1545102745294571, 0.15489740669727325, 0.15727315843105316, 0.7362794280052185, 0.15704317390918732, 0.5112589001655579, 0.15749305486679077, 0.6280437707901001, 0.22957386076450348, 0.46709153056144714, 0.4064370393753052, 0.2012837827205658, 0.6186098456382751, 0.517530620098114, 0.15858133137226105, 0.1816810965538025, 0.3857997953891754, 0.4479396343231201, 0.5354029536247253, 0.6746091842651367, 0.3034815490245819, 0.17019695043563843, 0.5107350945472717, 0.5141820907592773, 0.332342267036438, 0.3719644844532013, 0.16339942812919617, 0.18110504746437073, 0.17591357231140137, 0.17011959850788116, 0.17028366029262543, 0.4806101620197296, 0.5154028534889221, 0.5650879740715027, 0.6804084181785583, 0.22011423110961914, 0.1680423617362976, 0.17877197265625, 0.40681222081184387, 0.5228266716003418, 0.44577446579933167, 0.5603044629096985, 0.1798282116651535, 0.1706213653087616, 0.5386170744895935, 0.19417224824428558, 0.5027808547019958, 0.38652968406677246, 0.17713791131973267, 0.4933561384677887, 0.1761285662651062, 0.17676597833633423, 0.1679832935333252, 0.17684073746204376, 0.3682825565338135, 0.5483515858650208, 0.16277821362018585, 0.5979724526405334, 0.144460991024971, 0.5481593012809753, 0.14704546332359314, 0.1734534651041031, 0.18273060023784637, 0.519352912902832, 0.4206758141517639, 0.6003801822662354, 0.3241046071052551, 0.1517334133386612, 0.16175374388694763, 0.1573086678981781, 0.28344154357910156, 0.5904334783554077, 0.6110934019088745, 0.1584300398826599, 0.6904217600822449, 0.1588224470615387, 0.48323094844818115, 0.17567113041877747, 0.18747805058956146, 0.575717568397522, 0.09303275495767593, 0.5143420100212097, 0.3392900228500366, 0.16867560148239136, 0.17246590554714203, 0.4661792516708374, 0.6372047662734985, 0.5719394683837891, 0.46593523025512695, 0.4378800094127655, 0.3943903148174286, 0.16829009354114532, 0.39920127391815186, 0.5806984305381775, 0.17975935339927673, 0.44322144985198975, 0.44151967763900757, 0.6030001640319824, 0.4855194091796875, 0.5015478134155273, 0.17125414311885834, 0.6042411923408508, 0.3694375157356262, 0.3975858688354492, 0.2847766876220703, 0.5220285058021545, 0.294939249753952, 0.4943407475948334, 0.2103956639766693, 0.5151398777961731, 0.515487790107727, 0.5374827980995178, 0.20044487714767456, 0.2002234011888504, 0.23978088796138763, 0.3401681184768677, 0.21153624355793, 0.19223414361476898, 0.6001467108726501, 0.5441485643386841, 0.24998481571674347, 0.19677545130252838, 0.18944576382637024, 0.17992191016674042, 0.1667974889278412, 0.17443327605724335, 0.16817033290863037, 0.16347454488277435, 0.16425667703151703, 0.1693917065858841, 0.502300500869751, 0.1528863161802292, 0.15038076043128967, 0.4127456843852997, 0.17051254212856293, 0.477822870016098, 0.14118915796279907, 0.1441202163696289, 0.147971048951149, 0.1505405753850937, 0.44969820976257324, 0.1458388864994049, 0.15357396006584167, 0.2960279583930969, 0.29718923568725586, 0.15353178977966309, 0.1602722555398941, 0.15872237086296082, 0.4973211884498596, 0.1675291508436203, 0.34120646119117737, 0.1654392033815384, 0.16948488354682922, 0.17479810118675232, 0.1680707186460495, 0.16193780303001404, 0.4808177649974823, 0.3573095202445984, 0.17982536554336548, 0.4551538825035095, 0.5302919149398804, 0.1799454689025879, 0.1959037333726883, 0.5949415564537048, 0.22706854343414307, 0.19718657433986664, 0.5075596570968628, 0.18181730806827545, 0.5506755709648132, 0.19353260099887848, 0.18935295939445496, 0.49808236956596375, 0.21985425055027008, 0.2516433298587799, 0.1867554485797882, 0.22942157089710236, 0.6230249404907227, 0.5633754134178162, 0.6169996857643127, 0.222945436835289, 0.34088027477264404, 0.4366241991519928, 0.5653766989707947, 0.20138485729694366, 0.19145682454109192, 0.2942485213279724, 0.5602577328681946, 0.5375821590423584, 0.5989608764648438, 0.18892870843410492, 0.6390070915222168, 0.19016683101654053, 0.1895475685596466, 0.18118147552013397, 0.17906060814857483, 0.16873273253440857, 0.4662962853908539, 0.154696524143219, 0.1578514128923416, 0.13696792721748352, 0.5868381857872009, 0.4925081133842468, 0.4904259145259857, 0.1451534479856491, 0.33527854084968567, 0.606553316116333, 0.1807439923286438, 0.5861779451370239, 0.5978308916091919, 0.15279366075992584, 0.5776795744895935, 0.15517573058605194, 0.16065049171447754, 0.1685355305671692, 0.15293055772781372, 0.1549496203660965, 0.15642574429512024, 0.15715640783309937, 0.18468455970287323, 0.15612736344337463, 0.16599860787391663, 0.17107097804546356, 0.16442349553108215, 0.16873832046985626, 0.1618426889181137, 0.448504239320755, 0.5374988317489624, 0.14408233761787415, 0.3251996338367462, 0.13994812965393066, 0.4916152060031891, 0.139510840177536, 0.13858562707901, 0.14214399456977844, 0.14578305184841156, 0.18097466230392456, 0.14472100138664246, 0.1448979526758194, 0.5321540236473083, 0.5265421867370605, 0.46664053201675415, 0.4828994870185852, 0.16091345250606537, 0.27485325932502747, 0.16153506934642792, 0.5123358368873596, 0.6371498107910156, 0.1721770167350769, 0.5876119136810303, 0.17882628738880157, 0.17234739661216736, 0.40435338020324707, 0.29847097396850586, 0.38496828079223633, 0.46802425384521484, 0.3128337264060974, 0.6048945784568787, 0.17589174211025238, 0.1725534051656723, 0.3332638144493103, 0.17918919026851654, 0.20185714960098267, 0.17911496758460999, 0.48420777916908264, 0.5747899413108826, 0.31979086995124817, 0.17710177600383759, 0.2469622641801834, 0.23084618151187897, 0.1970577985048294, 0.17276202142238617, 0.5867987871170044, 0.40846872329711914, 0.1772431880235672, 0.20746366679668427, 0.5527114868164062, 0.3667680025100708, 0.5234624743461609, 0.3422866761684418, 0.1597888469696045, 0.30693182349205017, 0.13990260660648346, 0.1580546796321869, 0.6341592073440552, 0.5396462082862854, 0.48752161860466003, 0.16698092222213745, 0.14028900861740112, 0.48425430059432983, 0.3498840928077698, 0.14070051908493042, 0.24285469949245453, 0.1416688859462738, 0.1462002396583557, 0.6317777633666992, 0.40073537826538086, 0.17218363285064697, 0.5223569273948669, 0.2666330933570862, 0.17764844000339508, 0.1825437843799591, 0.15067832171916962, 0.15578216314315796, 0.22604462504386902, 0.5168681144714355, 0.47349390387535095, 0.4276440739631653, 0.5334817171096802, 0.2309540957212448, 0.16137360036373138, 0.5451583862304688, 0.49372535943984985, 0.16044673323631287, 0.16097299754619598, 0.5161970257759094, 0.4762006998062134, 0.5115594267845154, 0.24101367592811584, 0.16772761940956116, 0.1616833508014679, 0.15863288938999176, 0.16420480608940125, 0.16403692960739136, 0.1579359918832779, 0.15944446623325348, 0.17286917567253113, 0.16655471920967102, 0.1673639416694641, 0.16688668727874756, 0.16051888465881348, 0.16042236983776093, 0.16556468605995178, 0.1543102264404297, 0.18695271015167236, 0.1547778993844986, 0.19202519953250885, 0.1538221538066864, 0.1947876363992691, 0.14909392595291138, 0.18435727059841156, 0.22047436237335205, 0.27536121010780334, 0.7052552700042725, 0.21367980539798737, 0.6219318509101868, 0.6544346213340759, 0.3059191107749939, 0.2836013734340668, 0.19112852215766907, 0.5293275713920593, 0.549197256565094, 0.1470092535018921, 0.14972516894340515, 0.6272448897361755, 0.4170841574668884, 0.6121405363082886, 0.3296715319156647, 0.39755937457084656, 0.16367116570472717, 0.23610541224479675, 0.3587116003036499, 0.3582463264465332, 0.5499488115310669, 0.7839270830154419, 0.21028511226177216, 0.17382749915122986, 0.6372101306915283, 0.5747382640838623, 0.6551452279090881, 0.5107794404029846, 0.2027357965707779, 0.4483633041381836, 0.7692917585372925, 0.5581711530685425, 0.17061734199523926, 0.556354284286499, 0.6921732425689697, 0.1684853881597519, 0.6646506786346436, 0.1613331288099289, 0.20287460088729858, 0.733500063419342, 0.7315018177032471, 0.6856275796890259, 0.5439110994338989, 0.8048260807991028, 0.7494964599609375, 0.2319067120552063, 0.5191497206687927, 0.5573008060455322, 0.7549981474876404, 0.18675610423088074, 0.6163647770881653, 0.6317771077156067, 0.44625309109687805, 0.5917128324508667, 0.6136793494224548, 0.4258754551410675, 0.5523003339767456];
                    var traceNew = {
                      x: xarr,
                      y: yarr,
                      type: 'bar',
                    };
                    var plotlyData = [traceNew];

                    var layout = {
                      title: 'RESHENIE V1.0',
                      xaxis: {
                        title: 'X-AXIS',
                        automargin: true
                      },
                      yaxis: {
                        title: 'Y-AXIS',
                        automargin: true
                      },
                      width: 515
                    };
                    Plotly.newPlot('plotlyGraph', plotlyData, layout, {
                      scrollZoom: true
                    });
                    Plotly.newPlot('plotlyGraph2', plotlyData, layout, {
                      scrollZoom: true
                    });
                  </script>
                  <!--  -->

                  <!-- <div id="test"></div>
                  <script>
                    var data = [{
                      x: [0, 10, 20, 30, 40, 50],
                      y: [0, 10, 20, 30, 40, 50]
                    }];
                    var layout = {
                      font: {
                        size: 18
                      }
                    };
                    var config = {
                      responsive: true
                    };
                    TESTER = document.getElementById('test');
                    Plotly.newPlot(TESTER, data, layout, config);
                  </script> -->
                  <!--  -->

                  <div class="example clearfix full-width">
                    <div>
                      <div>
                        <p></p>
                        <!-- <button id="example-g-PreviousDomain-selector" style="margin-bottom: 10px;" class="btn"><i class="icon icon-chevron-left"></i></button>
                        <button id="example-g-NextDomain-selector" style="margin-bottom: 10px;" class="btn"><i class="icon icon-chevron-right"></i></button> -->
                        <div id="example-g" style="float: left;"></div>
                        <p></p>
                        <div>
                          <button type="button" id="example-g-PreviousDomain-selector" style="float: left; margin-left: 15px;">Previous</button>
                          <button type="button" id="example-g-NextDomain-selector" style="float: left; margin-left: 10px;"> Next </button>
                        </div>
                      </div>
                      <style>
                        #example-g .graph-label {
                          font-weight: bold;
                          font-size: 18px;
                        }
                      </style>
                      <script>
                        var Kowon_Vib_FrontCrank = "/assets/XYZ_Kowon_Vib_FrontCrank_epoch_data.json";
                        var Kowon_Vib_BackCrank = "/assets/XYZ_Kowon_Vib_BackCrank_epoch_data.json";
                        var Kowon_Vib_BackCrank_heatmap = "/assets/XYZ_Kowon_Vib_BackCrank_heatmap_data.json";
                        var Kowon_Vib_FrontMotor = "/assets/XYZ_Kowon_Vib_FrontMotor_epoch_data.json";
                        var Kowon_Vib_BackMotor = "/assets/XYZ_Kowon_Vib_BackMotor_epoch_data.json";
                        var contentStatus;
                        var clickedData;
                        var clickedHeatmap;
                        var objEpoch;
                        const i = new Date();
                        i.setMonth(i.getMonth() - 4);
                        (function() {
                          var count = 0;
                          var startDate, endDate;
                          var startDateItems, endDateItems;
                          var cal = new CalHeatMap();
                          cal.init({
                            itemSelector: "#example-g",
                            domain: "month",
                            domainLabelFormat: "%b-%Y",
                            subDomain: "x_day",
                            subDomainTextFormat: "%d",
                            // data: "/assets/datas-years.json",
                            data: Kowon_Vib_FrontCrank,
                            // start: new Date(2000, 0, 5),
                            start: i,
                            cellSize: 20,
                            cellPadding: 5,
                            domainGutter: 10,
                            range: 5,
                            domainDynamicDimension: false,
                            previousSelector: "#example-g-PreviousDomain-selector",
                            nextSelector: "#example-g-NextDomain-selector",
                            legend: [10, 40, 80, 120],
                            onClick: function(date, nb) {
                              count++;
                              if (count == 1) {
                                startDate = date;
                                startDateItems = nb;
                              } else if (count == 2) {
                                endDate = date;
                                endDateItems = nb;
                                count = 0;
                              }
                              $("#onClick-placeholder").html("Start Date <br/> <b>" + startDate + "</b> with <b>" + (startDateItems === null ? "unknown" : startDateItems) + "</b> items<br/>" +
                                "</b> End Date <b> <br/>" + endDate + "</b> with <b>" + (endDateItems === null ? "unknown" : endDateItems) + "</b> items<br/><br/>");

                              console.log(date.toLocaleDateString());
                              console.log(date);

                              function epoch(date) {
                                return Date.parse(date)
                              }
                              var startEpoch = epoch(startDate);
                              var endEpoch = epoch(endDate);
                              console.log("start date", startEpoch, "end date", endEpoch);


                              // sd = (new Date(startDate).getTime() / 1000);
                              // ed = (new Date(endDate).getTime() / 1000);
                              // console.log("start date", sd, " end date ", ed)

                              // fetch(clickedData).then(res => res.json()).then(data => objEpoch = data).then(() => console.log(objEpoch))

                              // filteredData = objEpoch.filter(d => {
                              //   var time = new Date(d.epochDates).getTime();
                              //   return (sd < time && time < ed);
                              // });
                              // console.log(filteredData);



                              if (contentStatus == 1) {
                                $('#tbodyStart').empty();
                                $('#tbodyEnd').empty();
                                contentStatus = 0;
                              }


                              sd1 = (new Date(startDate).getTime() / 1000);
                              sd2 = ((new Date(startDate).getTime() / 1000) + 86400);
                              console.log("start of day (start) 0000", sd1, " end of day (start) 2400", sd2)

                              fetch(clickedData).then(res => res.json()).then(data => objEpoch = data).then(() => console.log(objEpoch))
                              filteredDataStart = objEpoch.filter(d => {
                                var time = new Date(d.epochDates).getTime();
                                return (sd1 < time && time < sd2);
                              });
                              console.log(filteredDataStart, " ", typeof(filteredDataStart));

                              $(function() {
                                var counter = 1;
                                $.each(filteredDataStart, function(index, v) {
                                  epochToDate = (new Date(v.epochDates *= 1000)).toLocaleString();
                                  var nTr = '<tr class="startDate-item">'
                                  nTr += '<td><input id="' + counter + '" type="radio" name="start" value=""><label for ="' + counter + '">' + epochToDate + '</label></td>';
                                  nTr += '</tr>';
                                  $(nTr).appendTo('#tbodyStart');
                                  counter++;
                                  contentStatus = 1;
                                });
                              })



                              ed1 = (new Date(endDate).getTime() / 1000);
                              ed2 = ((new Date(endDate).getTime() / 1000) + 86400);
                              console.log("start of day (end) 0000", ed1, " end of day (end) 2400", ed2)
                              fetch(clickedData).then(res => res.json()).then(data => objEpoch = data).then(() => console.log(objEpoch))
                              filteredDataEnd = objEpoch.filter(d => {
                                var time = new Date(d.epochDates).getTime();
                                return (ed1 < time && time < ed2);
                              });
                              console.log(filteredDataEnd, " ", typeof(filteredDataStart));
                              $(function() {
                                var counter = 1;
                                $.each(filteredDataEnd, function(index, v) {
                                  epochToDate = (new Date(v.epochDates *= 1000)).toLocaleString();
                                  var nTr = '<tr class="endDate-item">'
                                  nTr += '<td><input id="' + counter + '-2" type="radio" name="end" value=""><label for ="' + counter + '-2">' + epochToDate + '</label></td>';
                                  nTr += '</tr>';
                                  $(nTr).appendTo('#tbodyEnd');
                                  counter++;
                                  contentStatus = 1;
                                });
                              })
                            }
                          });

                          $("#example-g-PreviousDomain-selector").on("click", function(e) {
                            e.preventDefault();
                            if (!cal.previous()) {
                              alert("No more domains to load");
                            }
                          });

                          $("#example-g-NextDomain-selector").on("click", function(e) {
                            e.preventDefault();
                            if (!cal.next()) {
                              alert("No more domains to load");
                            }
                          });
                          $("#Kowon_Vib_FrontCrank").on("click", function(e) {
                            clickedData = Kowon_Vib_FrontCrank;
                            // sensorJson = fetch(data).then(response => response.json()).then(data => console.log(data)).catch(error => console.log(error));
                            cal.update(clickedData);
                          });
                          $("#Kowon_Vib_BackCrank").on("click", function(e) {
                            clickedData = Kowon_Vib_BackCrank;
                            // sensorJson = fetch(data).then(response => response.json()).then(data => console.log(data)).catch(error => console.log(error));
                            clickedHeatmap = Kowon_Vib_BackCrank_heatmap;
                            cal.update(clickedHeatmap)
                            // cal.update(clickedData);
                          });
                          $("#Kowon_Vib_FrontMotor").on("click", function(e) {
                            clickedData = Kowon_Vib_FrontMotor;
                            // sensorJson = fetch(data).then(response => response.json()).then(data => console.log(data)).catch(error => console.log(error));
                            cal.update(clickedData);
                          });
                          $("#Kowon_Vib_BackMotor").on("click", function(e) {
                            clickedData = Kowon_Vib_BackMotor;
                            // sensorJson = fetch(data).then(response => response.json()).then(data => console.log(data)).catch(error => console.log(error));
                            cal.update(clickedData);
                          });
                        })();
                      </script>

                    </div>
                    <!-- css for table -->

                    <div>
                      <span id="onClick-placeholder" style="float: right; margin: 40px;">
                        Start Date <br><b>CLICK A DATE ON CALENDAR</b> with <b>undefined</b> items<br> End Date <b> <br>CLICK A DATE ON CALENDAR</b> with <b>undefined</b> items<br><br>
                      </span>
                    </div>
                    <div>
                      <style>
                        .special-table {
                          border: 1px solid black;
                          max-height: 400px;
                          overflow-y: auto;
                        }

                        .special-table table {
                          border: 0px solid gray;
                          margin: 0 auto;
                        }

                        .startDate-item input {
                          display: none;
                        }

                        .startDate-item input:checked+label {
                          background-color: red;

                        }

                        .startDate-item label {
                          cursor: pointer;
                          padding: 0px;
                          font-weight: 1;
                          font-size: 14px;
                        }

                        .startDateTable {
                          display: flow-root;
                          float: left;
                        }


                        .endDate-item input {
                          display: none;
                        }

                        .endDate-item input:checked+label {
                          background-color: red;

                        }

                        .endDate-item label {
                          cursor: pointer;
                          padding: 0px;
                          font-weight: 1;
                          font-size: 14px;
                        }

                        .endDateTable {
                          display: flow-root;
                          float: left;
                        }
                      </style>
                      <div style="display: inline-block;" class="special-table">
                        <table id="startDateTable">
                          <!-- <td style="float: left"><b>Start Date</b></td> -->
                          <thead>
                            <tr>
                              <th><b>Start Date</b></th>
                            </tr>
                          </thead>
                          <tbody id="tbodyStart">
                            <!-- <tr class="startDate-item">
                              <td>
                                <input id="input-1" type="radio" name="start" value="">
                                <label for="input-1">2021-01-07 17:20:46</label>
                              </td>
                            </tr>
                            <tr class="startDate-item">
                              <td>
                                <input id="input-2" type="radio" name="start" value="">
                                <label for="input-2">2021-01-08 17:25:39</label>
                              </td>
                            </tr>
                            <tr class="startDate-item">
                              <td>
                                <input id="input-3" type="radio" name="start" value="">
                                <label for="input-3">2021-01-08 23:27:10</label>
                              </td>
                            </tr>
                            <tr class="startDate-item">
                              <td>
                                <input id="input-4" type="radio" name="start" value="">
                                <label for="input-4">2021-01-09 05:30:19</label>
                              </td>
                            </tr>
                            <tr class="startDate-item">
                              <td>
                                <input id="input-5" type="radio" name="start" value="">
                                <label for="input-5">2021-01-09 05:30:19</label>
                              </td>
                            </tr>
                            <tr class="startDate-item">
                              <td>
                                <input id="input-6" type="radio" name="start" value="">
                                <label for="input-6">2021-01-09 05:30:19</label>
                              </td>
                            </tr> -->
                          </tbody>
                        </table>
                      </div>

                      <div style="display: inline-block;" class="special-table">
                        <table id="endDateTable">
                          <!-- <td style="float: left"><b>Start Date</b></td> -->
                          <thead>
                            <tr>
                              <th><b>End Date</b></th>
                            </tr>
                          </thead>
                          <tbody id="tbodyEnd">
                            <!-- <tr class="endDate-item">
                              <td>
                                <input id="input-1-second" type="radio" name="end" value="">
                                <label for="input-1-second">2021-01-07 17:20:46</label>
                              </td>
                            </tr>
                            <tr class="endDate-item">
                              <td>
                                <input id="input-2-second" type="radio" name="end" value="">
                                <label for="input-2-second">2021-01-08 17:25:39</label>
                              </td>
                            </tr>
                            <tr class="endDate-item">
                              <td>
                                <input id="input-3-second" type="radio" name="end" value="">
                                <label for="input-3-second">2021-01-08 23:27:10</label>
                              </td>
                            </tr> -->
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <!-- /.col-md-6 -->
                </div>
                <!-- /.row -->
              </div>
              <!-- /.container-fluid -->
            </div>
            <!-- /.content -->
          </div>
          <!-- /.content-wrapper -->

          <!-- Control Sidebar -->
          <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
          </aside>
          <!-- /.control-sidebar -->

          <!-- Main Footer -->
          <footer class="main-footer">
            <strong>Copyright &copy; 2018-2021 <a href="http://reshenie.co.kr/">RESHENIE.CO.KR</a>.</strong> All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
              <b>Version</b> 0.8.1
              <!-- By Sunwook Kim swk23c8@riseup.net  -->
            </div>
          </footer>
        </div>
        <!-- ./wrapper -->

        <!-- REQUIRED SCRIPTS -->

        <!-- jQuery -->
        <!-- commented out to use jQuery selectable -->
        <!-- <script src="/assets/plugins/jquery/jquery.min.js"></script> -->
        <!-- Bootstrap -->
        <script src="/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE -->
        <script src="/assets/js/adminlte.js"></script>

        <!-- jQuery Knob -->
        <script src="/assets/plugins/jquery-knob/jquery.knob.min.js"></script>
        <!-- Sparkline -->
        <script src="/assets/plugins/sparklines/sparkline.js"></script>

        <!-- OPTIONAL SCRIPTS -->
        <script src="/assets/plugins/chart.js/Chart.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="/assets/js/demo.js"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="/assets/js/pages/dashboard3.js"></script>
        <!-- d3js.org heatmap calendar -->
        <script src="/assets/js/test/d3.min.js"></script>
        <link rel="stylesheet" href="/assets/js/cal-heatmap.css" />
        <script type="text/javascript" src="/assets/js/cal-heatmap.min.js"></script>



        <script>
          $(function() {
            /* jQueryKnob */

            $('.knob').knob({
              /*change : function (value) {
              //console.log("change : " + value);
              },
              release : function (value) {
              console.log("release : " + value);
              },
              cancel : function () {
              console.log("cancel : " + this.value);
              },*/
              draw: function() {

                // "tron" case
                if (this.$.data('skin') == 'tron') {

                  var a = this.angle(this.cv) // Angle
                    ,
                    sa = this.startAngle // Previous start angle
                    ,
                    sat = this.startAngle // Start angle
                    ,
                    ea // Previous end angle
                    ,
                    eat = sat + a // End angle
                    ,
                    r = true

                  this.g.lineWidth = this.lineWidth

                  this.o.cursor &&
                    (sat = eat - 0.3) &&
                    (eat = eat + 0.3)

                  if (this.o.displayPrevious) {
                    ea = this.startAngle + this.angle(this.value)
                    this.o.cursor &&
                      (sa = ea - 0.3) &&
                      (ea = ea + 0.3)
                    this.g.beginPath()
                    this.g.strokeStyle = this.previousColor
                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
                    this.g.stroke()
                  }

                  this.g.beginPath()
                  this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
                  this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
                  this.g.stroke()

                  this.g.lineWidth = 2
                  this.g.beginPath()
                  this.g.strokeStyle = this.o.fgColor
                  this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
                  this.g.stroke()

                  return false
                }
              }
            })
            /* END JQUERY KNOB */
          })
        </script>
</body>

<?php
// include_once ('./admin.tail.php');
?>