<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "You no have access!!");
    redirect("../login.php");
}
$level = $_SESSION['level'];
$email = $_SESSION['email'];
if ($level == 'admin') {
    $query = mysqli_query($conn, "SELECT COUNT(message) AS message FROM messages");
    $total_messages = mysqli_fetch_assoc($query, true);
} else {
    $query = mysqli_query($conn, "SELECT COUNT(message) AS message FROM messages WHERE user_id='$email'");
}


$total_success = "";
$total_failed = "";
$month = date('m');
for ($month = 1; $month <= 12; $month++) {
    $year_now = date('Y');
    $query = mysqli_query($conn, "SELECT COUNT(message) AS total_messages FROM messages WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND message_status='success'");
    $row = mysqli_fetch_assoc($query);
    $amount = $row['total_messages'];
    $total_success .= "'$amount'" . ", ";
}

for ($month = 1; $month <= 12; $month++) {
    $year_now = date('Y');
    $query = mysqli_query($conn, "SELECT COUNT(message) AS total_messages FROM messages WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND message_status='waiting'");
    $row = mysqli_fetch_assoc($query);
    $amount = $row['total_messages'];
    $total_waiting .= "'$amount'" . ", ";
}

for ($month = 1; $month <= 12; $month++) {
    $year_now = date('Y');
    $query = mysqli_query($conn, "SELECT COUNT(message) AS total_messages FROM messages WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND message_status='failed'");
    $row = mysqli_fetch_assoc($query);
    $amount = $row['total_messages'];
    $total_failed .= "'$amount'" . ", ";
}

require_once('./templates/header.php');
?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    #app {
        max-width: 500px;
        margin: 20px auto;
    }

    #qrcode {
        display: none;
        /* Showed when qr code received */
        width: 100%;
        margin: 10px 0;
        border: 1px solid #efefef;
        border-radius: 4px;
    }

    ul.logs {
        max-height: 150px;
        padding: 15px 15px 15px 30px;
        margin-top: 5px;
        border-radius: 4px;
        overflow-y: auto;
        background: #efefef;
        color: #666;
        font-size: 14px;
    }

    ul.logs li:first-child {
        color: green;
    }
</style>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><b> Dashboard</b></h1>
        <?php if ($_SESSION['level'] == 'admin') { ?>
            <a href="./../helpers/cronjob.php" id="linkButton" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" title="Push Cronjob"><b><i class="fas fa-clock"></i> Push Cronjob</b></a>
        <?php } ?>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark mb-1">
                                <span style="font-size: 1rem;"><b>Total Message</b></span>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php if ($_SESSION['level'] == "admin") {
                                    echo countDB("messages");
                                } else {
                                    echo countDB('messages', 'user_id', $_SESSION['email']);
                                } ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-whatsapp fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark mb-1">
                                <span style="font-size: 1rem;"><b>All Contact</b></span>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php if ($_SESSION['level'] == "admin") {
                                    echo countDB('contacts');
                                } else {
                                    echo countDB('contacts', 'user_id', $_SESSION['email']);
                                } ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-address-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark mb-1">
                                <span style="font-size: 1rem;"><b>Contact Personal</b></span>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php if ($_SESSION['level'] == "admin") {
                                    echo countDB('contacts', 'contact_type', 'PERSONAL');
                                } else {
                                    echo countDB('contacts', 'user_id', $_SESSION['email']);
                                } ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-person fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark mb-1">
                                <span style="font-size: 1rem;"><b>Contact Group</b></span>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php if ($_SESSION['level'] == "admin") {
                                    echo countDB('contacts', 'contact_type', 'GROUP');
                                } else {
                                    echo countDB('contacts', 'user_id', $_SESSION['email']);
                                } ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-people-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-12">
            <!-- Area Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Message Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 100%;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                    <hr>
                    Styling for the area chart can be found in the
                    <code>/js/demo/chart-area-demo.js</code> file.
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <!-- Area Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Authentification</h6>
                </div>
                <div class="card-body">
                    <div id="app" class="card-body text-center" style="background: #ecf0f1;">
                        <img id="qrcode" class="img-fluid px-3 px-sm-4 mt-3 mb-4" src="" alt="QR Code" title="Scan Me" />
                        <ul class="logs"></ul>
                        <img id="loading" class="img-fluid px-3 px-sm-4 mt-3 mb-4" src="../loading.gif" alt="loading" title="Scan Me" />
                        <p id="error500" class="text-danger font-weight-bold">500 Internal Server Error<br><small>"Error Connecting With Server"</small></p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require_once('./templates/footer.php'); ?>
<!-- Socket.io -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.4/socket.io.js" integrity="sha512-YeeA/Qxn5hYdkukScTCNNOhTrv1C2RubAGButJ1rmgQwZf/HdRaCGl+JAVkqsqaNRaYNHdheiuKKuPf9mDcqKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        // var socket = io();
        var socket = io('<?= getSingleValDB("setting", "id", "1", "url_gateway") ?>', {
            transports: ['websocket',
                'polling',
                'flashsocket'
            ]
        });

        socket.on('message', function(msg) {
            $('.logs').prepend($('<li>').text(msg));
            $('#loading').hide();
            $('#error500').hide();
        });

        socket.on('connection', function(data) {
            $('#loading').show();
            $('#error500').show();
        });

        socket.on('qr', function(src) {
            $('#qrcode').attr('src', src);
            $('#qrcode').show();
            $('#loading').hide();
        });

        socket.on('ready', function(data) {
            $('#qrcode').hide();
            $('#loading').hide();
        });

        socket.on('authenticated', function(data) {
            $('#qrcode').hide();
            $('#loading').hide();
        });

    });
</script>
<script>
    var delayed;
    var ctx = document.getElementById("myAreaChart");
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
                    label: "Messages Success",
                    data: [<?php echo $total_success; ?>],
                    borderColor: "rgb(22, 160, 133)",
                    borderWidth: 2,
                    borderRadius: 4,
                    pointBorderWidth: 4,
                    lineTension: 0.2,
                },
                {
                    label: "Messages Waiting",
                    data: [<?php echo $total_waiting; ?>],
                    borderColor: "rgb(241, 196, 15)",
                    borderWidth: 2,
                    borderRadius: 4,
                    pointBorderWidth: 4,
                    lineTension: 0.2,
                },
                {
                    label: "Messages Failed",
                    data: [<?php echo $total_failed; ?>],
                    borderColor: "rgb(231, 76, 60)",
                    borderWidth: 2,
                    borderRadius: 4,
                    pointBorderWidth: 4,
                    lineTension: 0.2,
                },
            ],
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