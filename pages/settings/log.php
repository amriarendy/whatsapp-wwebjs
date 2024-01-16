<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");

$login = cekSession();
if ($login == 0) {
    toastr_set("error", "You no have access!!");
    redirect("../login.php");
}

require_once('../templates/header.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-chart-pie text-info"></i><b>Chart and History</b></h1>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-sm-12">
                    <!-- Content Row -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Chart Visitor</h6>
                        </div>
                        <div class="card-body">
                            <!-- Starter Content -->
                            <div class="chart-area" style="height: 100%;">
                                <canvas id="chartVisitor"></canvas>
                            </div>
                            <!-- End Content -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <!-- Content Row -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Total Information</h6>
                        </div>
                        <div class="card-body">
                            <!-- Starter Content -->
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <tr>
                                        <td class="font-weight-bold">TODAY VISITOR</td>
                                        <td class="font-weight-bold">:</td>
                                        <td>1032 public</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">TOTAL VISITOR</td>
                                        <td class="font-weight-bold">:</td>
                                        <td>1032 public</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">TOTAL USERS</td>
                                        <td class="font-weight-bold">:</td>
                                        <td>1032 users</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">TOTAL CONTACT</td>
                                        <td class="font-weight-bold">:</td>
                                        <td>1032 personal - 1565 groups</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">TOTAL SEND MESSAGES</td>
                                        <td class="font-weight-bold">:</td>
                                        <td>1032 message, 1254 media, 15685 groups</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">TOTAL API SEND MESSAGES</td>
                                        <td class="font-weight-bold">:</td>
                                        <td>1032 message, 1254 media, 15685 groups</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">STATUS SEND MESSAGE</td>
                                        <td class="font-weight-bold">:</td>
                                        <td>
                                            <a href="#" title="success"><span class="font-weight-bold text-success">1242 message,</span></a>
                                            <a href="#" title="waiting"><span class="font-weight-bold text-warning">234 message,</span></a>
                                            <a href="#" title="failed"><span class="font-weight-bold text-danger">43 message</span></a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- End Content -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Log History</h6>
                </div>
                <div class="card-body">
                    <!-- Starter Content -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>IP ADDRESS</th>
                                    <th>ROUTE</th>
                                    <th>USERS</th>
                                    <th>ACTIVITY</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>IP ADDRESS</th>
                                    <th>ROUTE</th>
                                    <th>USERS</th>
                                    <th>ACTIVITY</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><?= $_SERVER['REMOTE_ADDR'] ?></td>
                                    <td><?= print_r($_GET); ?></td>
                                    <td>USERS</td>
                                    <td>ACTIVITY</td>
                                    <td>Action</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Content -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
<script type="text/javascript" src="<?= $base_url ?>/assets/app/datatables.js"></script>
<script>
    var delayed;
    var ctx = document.getElementById("chartVisitor");
    var myChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "Mei",
                "Jun",
                "Jul",
                "Agu",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ],
            datasets: [{
                label: "Visitor",
                data: [123, 216, 303, 88, 45, 989],
                borderColor: "rgb(22, 160, 133)",
                borderWidth: 2,
                borderRadius: 4,
                pointBorderWidth: 4,
                lineTension: 0.2,
            }],
        },
        options: {
            animation: {
                onComplete: () => {
                    delayed = true;
                },
                delay: (context) => {
                    let delay = 0;
                    if (context.type === "data" && context.mode === "default" && !delayed) {
                        delay = context.dataIndex * 300 + context.datasetIndex * 100;
                    }
                    return delay;
                },
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            if (value % 1 === 0) {
                                return value;
                            }
                        },
                    },
                }, ],
            },
        },
    });
</script>
</body>

</html>