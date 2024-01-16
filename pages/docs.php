<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "You no have access!!");
    redirect("../login.php");
}

require_once('./templates/header.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-fire text-danger"></i><b>Docs and API</b></h1>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">App Info</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 40rem;" src="<?= $base_url ?>/public/files/dashboard.png" alt="...">
                    </div>
                    <!-- Starter Content -->
                    <p></p>
                    <h3 style="text-align: center; "><span style="font-size: 23px;"><b>DOCUMENTATION WHATSAPP BOT API</b></span></h3>
                    <p><b style="font-size: 1rem;">Endpoint:</b><span style="font-size: 1rem;"> </span><a href="https://domain.com/api/send-message.php" target="_blank" style="background-color: rgb(255, 255, 255); font-size: 1rem;">https://domain.com/api/send-message.php</a><br></p>
                    <p><b>Method:</b> POST</p>
                    <table class="table table-bordered" style="width: 1150.75px; color: rgb(33, 37, 41); background-color: rgb(255, 255, 255); text-align: center;">
                        <tbody>
                            <tr>
                                <td><span style="font-weight: bolder;">Parameter</span></td>
                                <td><span style="font-weight: bolder;">Validation</span></td>
                                <td><span style="font-weight: bolder;">Type</span></td>
                                <td><span style="font-weight: bolder;">More</span></td>
                            </tr>
                            <tr>
                                <td>api_key</td>
                                <td>required</td>
                                <td>string</td>
                                <td>Validation Key Api</td>
                            </tr>
                            <tr>
                                <td>send_to</td>
                                <td>required</td>
                                <td>string/number</td>
                                <td>Target Number</td>
                            </tr>
                            <tr>
                                <td>message</td>
                                <td>required</td>
                                <td>string</td>
                                <td>Content Message</td>
                            </tr>
                        </tbody>
                    </table>
                    <p><span style="font-weight: bolder; font-size: 1rem;"><br></span></p>
                    <p><span style="font-weight: bolder; font-size: 1rem;">Endpoint:</span><span style="font-size: 1rem;">&nbsp;</span><a href="https://domain.com/api/send-message.php" target="_blank" style="background-color: rgb(255, 255, 255); font-size: 1rem;">https://domain.com/api/send-media.php</a><br></p>
                    <p><span style="font-weight: bolder;">Method:</span>&nbsp;POST</p>
                    <table class="table table-bordered" style="width: 1150.75px; color: rgb(33, 37, 41); background-color: rgb(255, 255, 255); text-align: center;">
                        <tbody>
                            <tr>
                                <td><span style="font-weight: bolder;">Parameter</span></td>
                                <td><span style="font-weight: bolder;">Validation</span></td>
                                <td><span style="font-weight: bolder;">Type</span></td>
                                <td><span style="font-weight: bolder;">More</span></td>
                            </tr>
                            <tr>
                                <td>api_key</td>
                                <td>required</td>
                                <td>string</td>
                                <td>Validation Key Api</td>
                            </tr>
                            <tr>
                                <td>send_to</td>
                                <td>required</td>
                                <td>string/number</td>
                                <td>Target Number</td>
                            </tr>
                            <tr>
                                <td>message</td>
                                <td>required</td>
                                <td>string</td>
                                <td>Content Message</td>
                            </tr>
                            <tr>
                                <td>url_media</td>
                                <td>required</td>
                                <td>string</td>
                                <td>Media</td>
                            </tr>
                        </tbody>
                    </table>&nbsp;<p></p>
                    <p><span style="font-weight: bolder; font-size: 1rem;">Endpoint:</span><span style="font-size: 1rem;">&nbsp;</span><a href="https://domain.com/api/send-message.php" target="_blank" style="background-color: rgb(255, 255, 255); font-size: 1rem;">https://domain.com/api/send-schedule.php</a><br></p>
                    <p><span style="font-weight: bolder;">Method:</span>&nbsp;POST</p>
                    <table class="table table-bordered" style="width: 1150.75px; color: rgb(33, 37, 41); background-color: rgb(255, 255, 255); text-align: center;">
                        <tbody>
                            <tr>
                                <td><span style="font-weight: bolder;">Parameter</span></td>
                                <td><span style="font-weight: bolder;">Validation</span></td>
                                <td><span style="font-weight: bolder;">Type</span></td>
                                <td><span style="font-weight: bolder;">More</span></td>
                            </tr>
                            <tr>
                                <td>api_key</td>
                                <td>required</td>
                                <td>string</td>
                                <td>Validation Key Api</td>
                            </tr>
                            <tr>
                                <td>send_to</td>
                                <td>required</td>
                                <td>string/number</td>
                                <td>Target Number</td>
                            </tr>
                            <tr>
                                <td>message</td>
                                <td>required</td>
                                <td>string</td>
                                <td>Content Message</td>
                            </tr>
                            <tr>
                                <td>url_media</td>
                                <td>required</td>
                                <td>string</td>
                                <td>Media<br><br></td>
                            </tr>
                            <tr>
                                <td>date</td>
                                <td>required</td>
                                <td>date</td>
                                <td>Date to push message</td>
                            </tr>
                            <tr>
                                <td>time</td>
                                <td>required</td>
                                <td>time</td>
                                <td>Date to push time</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- End Content -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require_once('./templates/footer.php'); ?>
</body>

</html>