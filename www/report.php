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
    <!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" /> -->
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://api.mapbox.com/mapbox-gl-js/v1.10.0/mapbox-gl.css" rel="stylesheet" />
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="./report.css">
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
                <li class="nav-item active">
                    <a class="nav-link" href="report.php">เพิ่ม/แก้ไขข้อมูล</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="riskpoint.php">รายงานจุดเสี่ยง</a>
                </li>

                <!-- <li class="nav-item">
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
            <p>รายการการเกิดอุบัติเหตุ การบันทึกโดยยึดถือเอาครั้งของเหตุการณ์ที่เกิดอุบัติเหตุ
                ซึ่งในแต่ละเหตุการณ์จำนวนผู้ได้รับบาดเจ็บและเสียชีวิตอาจมากกว่า 1 คน</p>
            <p class="lead">
                <button type="button" class="btn btn-success" id='addData' onclick="insertModal()"><i
                        class='bx bxs-plus-circle'></i>&nbsp; เพิ่มข้อมูลใหม่</button>
            </p>
        </div>
    </div>

    <div class="container-fluid mt-2 mb-1">
        <table id="data" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>วันที่เกิดเหตุ</th>
                    <th>สถานที่เกิดเหตุ</th>
                    <th>ตำบล</th>
                    <th>อำเภอ</th>
                    <th>ประเภทรถ</th>
                    <th>แก้ไข/ลบ</th>
                </tr>
            </thead>
        </table>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content p-2">
                <div class="modal-header">
                    <input type="text" id="pkid" readonly="true" style="border: 0px;" />
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="login-form">
                        <div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="login-field-icon fui-user" for="acc_place">สถานที่เกิดเหตุ</label>
                                        <input type="text" class="form-control" id="acc_place" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="login-field-icon fui-user" for="acc_date">วันที่เกิดเหตุ</label>
                                        <input type="date" class="form-control" id="acc_date" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="login-field-icon fui-user" for="acc_time">เวลาที่เกิดเหตุ</label>
                                        <input type="time" class="form-control" id="acc_time" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        ใช้เมาส์ขยับ marker ไปยังสถานที่เกิดเหตุ <br>
                                        <div id="map"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="login-field-icon fui-user" for="pro">จังหวัด</label>
                                                <input type="text" class="form-control" id="pro" />
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="login-field-icon fui-user" for="amp">อำเภอ</label>
                                                <input type="text" class="form-control" id="amp" />
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label class="login-field-icon fui-user" for="tam">ตำบล</label>
                                            <select class="form-control" id="tam">
                                                <option value="ท่าอิฐ" selected>ท่าอิฐ</option>
                                                <option value="ท่าเสา">ท่าเสา</option>
                                                <option value="บ้านเกาะ">บ้านเกาะ</option>
                                                <option value="ป่าเซ่า">ป่าเซ่า</option>
                                                <option value="คุ้งตะเภา">คุ้งตะเภา</option>
                                                <option value="วังกะพี้">วังกะพี้</option>
                                                <option value="หาดกรวด">หาดกรวด</option>
                                                <option value="น้ำริด">น้ำริด</option>
                                                <option value="งิ้วงาม">งิ้วงาม</option>
                                                <option value="บ้านด่านนาขาม">บ้านด่านนาขาม</option>
                                                <option value="บ้านด่าน">บ้านด่าน</option>
                                                <option value="ผาจุก">ผาจุก</option>
                                                <option value="วังดิน">วังดิน</option>
                                                <option value="แสนตอ">แสนตอ</option>
                                                <option value="หาดงิ้ว">หาดงิ้ว</option>
                                                <option value="ขุนฝาง">ขุนฝาง</option>
                                                <option value="ถ้ำฉลอง">ถ้ำฉลอง</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label class="login-field-icon fui-user" for="x">พิกัด ละติจูด</label>
                                            <input type="number" class="form-control" id="x" />
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label class="login-field-icon fui-user" for="y">พิกัด ลองจิจูด</label>
                                            <input type="number" class="form-control" id="y" />
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <!-- <input type="text" class="form-control" placeholder="vehicle" id="vehicle" formControlName="vehicle" /> -->
                                        <label class="login-field-icon fui-user"
                                            for="vehicle">ยานพาหนะคนเจ็บ/ผู้เสียชีวิต</label>
                                        <select class="form-control" id="vehicle">
                                            <option value="" disabled>ยานพาหนะ</option>
                                            <option value="รถจักรยานยนต์">รถจักรยานยนต์</option>
                                            <option value="รถจักรยาน">รถจักรยาน</option>
                                            <option value="รถยนต์">รถเก๋ง</option>
                                            <option value="รถโดยสาร">รถโดยสาร</option>
                                            <option value="รถปิคอัพ">รถปิคอัพ</option>
                                            <option value="รถบรรทุก">รถบรรทุก</option>
                                            <option value="รถไฟ">รถไฟ</option>
                                            <option value="รถตู้">รถตู้</option>
                                            <option value="รถลากเข็น">รถลากเข็น</option>
                                            <option value="อื่นๆ">อื่นๆ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="login-field-icon fui-user"
                                            for="transfer_by">ยานพาหนะคู่กรณี</label>
                                        <select class="form-control" id="disputant">
                                            <option value="" disabled>ยานพาหนะ</option>
                                            <option value="รถจักรยานยนต์">รถจักรยานยนต์</option>
                                            <option value="รถจักรยาน">รถจักรยาน</option>
                                            <option value="รถยนต์">รถเก๋ง</option>
                                            <option value="รถโดยสาร">รถโดยสาร</option>
                                            <option value="รถปิคอัพ">รถปิคอัพ</option>
                                            <option value="รถบรรทุก">รถบรรทุก</option>
                                            <option value="รถไฟ">รถไฟ</option>
                                            <option value="รถตู้">รถตู้</option>
                                            <option value="รถลากเข็น">รถลากเข็น</option>
                                            <option value="อื่นๆ">อื่นๆ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="login-field-icon fui-user" for="to_hospital">ส่งต่อ
                                            รพ</label>
                                        <input type="text" class="form-control" placeholder="to_hospital"
                                            id="to_hospital" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="login-field-icon fui-user"
                                            for="transfer_type">หน่วยที่นำส่ง</label>
                                        <select class="form-control" id="transfer_type">
                                            <option value="" disabled>หน่วยที่นำส่ง</option>
                                            <option value="ประชาชน/มาเอง">ประชาชน/มาเอง</option>
                                            <option value="EMS 8 นาที">EMS 8 นาที</option>
                                            <option value="EMS 10 นาที">EMS 10 นาที</option>
                                            <option value="EMS เกิน 10 นาที">EMS เกิน 10 นาที</option>
                                            <option value="มูลนิธิอุตรดิตถ์สงเคราะห์">มูลนิธิอุตรดิตถ์สงเคราะห์
                                            </option>
                                            <option value="สมาคมกู้ภัยวัดหมอนไม้">สมาคมกู้ภัยวัดหมอนไม้</option>
                                            <option value="กู้ภัยอบต เทศบาล">กู้ภัยอบต เทศบาล</option>
                                            <option value="กู้ภัยลำน้ำน่าน">กู้ภัยลำน้ำน่าน</option>
                                            <option value="กู้ชีพโรงพยาบาล">กู้ชีพโรงพยาบาล</option>
                                            <option value="อื่นๆ">อื่นๆ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="img">ถ่ายภาพ</label>
                                        <div class="form-group">
                                            <input type="file" accept="image/*" name="imgfile" id="imgfile">
                                            <p></p>
                                            <img src="" id="preview" width="250px">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div id="divInsert"></div>
                            <div id="divEdit"></div>
                            <button type="button" id="addMore" class="btn btn-success"><i
                                    class='bx bx-plus-circle'></i>&nbsp;
                                เพิ่มผู้บาดเจ็บรายอื่น</button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button> -->
                    <button type="button" class="btn btn-info" id="insertData">
                        <i class='bx bxs-save'></i>&nbsp; บันทึก
                    </button>
                    <button type="button" class="btn btn-info" id="updateData">
                        <i class='bx bx-edit'></i>&nbsp; บันทึกการแก้ไข
                    </button>
                    <!-- &nbsp; -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class='bx bx-undo'></i>&nbsp; กลับหน้ารายงาน
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="exampleModalLabel">Modal title</h5> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ลบรายการที่เลือก (id: <input type="text" id="pkid2" style="border: 0px;" />)
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="deleteData"><i
                            class='bx bxs-save'></i>&nbsp;ยืนยันลบข้อมูล</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class='bx bx-message-square-x'></i>&nbsp; กลับหน้ารายงาน</button>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <!-- <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.25.3/locale/th.min.js"></script> -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v1.10.0/mapbox-gl.js"></script>
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
   
    <script>
       $('#usr').text(usr);
       let u = usr;
    </script>
    <script src="./report.js"></script>


</body>

</html>