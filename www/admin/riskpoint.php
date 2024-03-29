
<?php
    header('Content-Type: text/html; charset=utf-8');
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    session_start();   

    if (!$_SESSION["UserID"]) {
        Header("Location: form_login.php");
    }else{
        print "<script>var usr = '" . $_SESSION['UserID'] . "'</script>";

    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>UD-Access</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./../dist/bootstrap.min.united.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
    <!-- <link href="https://api.mapbox.com/mapbox-gl-js/v1.10.0/mapbox-gl.css" rel="stylesheet" /> -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="./riskpoint.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">UD-Accident</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
            aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item ">
                    <a class="nav-link" href="index.php">รายงานการเกิดอุบัติเหตุ</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="report.php">เพิ่ม/แก้ไขข้อมูล</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="riskpoint.php">รายงานจุดเสี่ยง</a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li> -->
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" >ผู้ใช้งาน: <span id="usr"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">ออกจากระบบ</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid mt-2 mb-1">
        <div class="jumbotron">
            <!-- <label for="dateStart">กำหนดวันที่ค้นหาการเกิดอุบัติเหตุ</label> -->
            <div class="row">
                <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                        <label for="dateStart">ค้นหาตั้งแต่วันที่</label>
                        <input type="date" class="form-control" id="dateStart">
                    </div>
                </div>
                <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                        <label for="dateEnd">ถึงวันที่</label>
                        <input type="date" class="form-control" id="dateEnd">
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">

                        <p></p>
                        <!-- <span class="badge badge-primary f16">พบจุดการเกิดอุบัติเหตุที่ถูกรายงานจำนวน <span
                                id="riskall"></span>
                            แห่ง</span> -->
                    </div>
                </div>
            </div>
            <button class="btn btn-info" onclick="getDate()">ค้นหา</button>
            <!-- <hr> -->

        </div>
        <div class="row">
            <div class="col-sm-4">
                <div id="map"></div>
            </div>
            <div class="col-sm-4">
                <!-- <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="customCheck1" checked="">
                    <label class="custom-control-label" for="customCheck1">Check this custom checkbox</label>
                </div> -->
                <div class="list-group list-group-flush fx" id="riskList"></div>
            </div>
            <div class="col-sm-4">
                <div class="p-3 fx">
                    รายการจุดเสี่ยงที่ถูกรายงานทั้งหมด <span id="riskall" class="badge badge-warning"></span> แห่ง
                    <br> ได้รับการแก้ไขแล้ว <span id="fix" class="badge badge-success"></span> แห่ง
                    <br> ยังไม่ได้รับการแก้ไข <span id="notfix" class="badge badge-danger"></span> แห่ง
                    <hr>
                    <div id="chartAmp"></div>

                    <div id="chartTam"></div>
                </div>
            </div>
        </div>

    </div>


    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <img src="" alt="" height="100" id='risk_image'>
                        <input id="gid" name="gid" type="hidden">
                        <div class="form-group">
                            <label for="to_hospital">วันที่รับแจ้ง</label>
                            <input type="date" class="form-control inp" id="date_notify" readonly />
                        </div>
                        <div class="form-group">
                            <label for="to_hospital">ตรวจสอบเบื้องต้น</label>
                            <select class="form-control inp" id="validation" required>
                                <option value="ตรวจสอบแล้ว">ตรวจสอบแล้ว</option>
                                <option value="ยังไม่ได้รับการตรวจสอบ">ยังไม่ได้รับการตรวจสอบ</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="to_hospital">สถานะการแก้ไข</label>
                            <select class="form-control inp" id="status_fix" required>
                                <option value="แก้ไขแล้ว">แก้ไขแล้ว</option>
                                <option value="ยังไม่ได้รับการแก้ไข">ยังไม่ได้รับการแก้ไข</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="to_hospital">วันที่แก้ไข</label>
                            <input type="date" class="form-control inp" id="date_fix" required />
                        </div>
                        <button type="submit" class="btn btn-info" id='saveEdit'><i class='bx bxs-save'></i>&nbsp;
                            บันทึก</button>
                        <button type="button" class="btn btn-outline-danger" id='remove'><i
                                class='bx bx-x-circle'></i>&nbsp;
                            ลบ</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.25.3/locale/th.min.js"></script> -->
    <!-- <script src="https://api.mapbox.com/mapbox-gl-js/v1.10.0/mapbox-gl.js"></script> -->
    <!-- <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="./riskpoint.js"></script>
    <script>
       $('#usr').text(usr);
    </script>


</body>

</html>