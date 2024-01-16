<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "Forbiden: Sorry you are not login!");
    redirect("../../login.php");
}

if (get("act") == "delete") {
    $id = get("id");
    $where = mysqli_query($conn, "SELECT * FROM messages WHERE id='$id'");
    $row = mysqli_fetch_assoc($where);
    $url = parse_url($row['message_media']);
    $fileOnly = str_replace("/wa-api/public/uploads/", '', $url['path']);
    $dir = "../../public/uploads/" . $fileOnly;
    unlink($dir);
    $query = mysqli_query($conn, "DELETE FROM messages WHERE id='$id'");
    toastr_set("success", "Success: Delete data");
    redirect("log-sender.php");
}

if (get("act") == "delete_all") {
    $query = mysqli_query($conn, "DELETE FROM messages");
    toastr_set("success", "Success: Delete all data");
    redirect("log-sender.php");
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history"></i> Log Sender</h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <?php if ($_SESSION['level'] == 'admin') { ?>
                <a class="btn btn-danger" href="log-sender.php?act=delete_all" onclick="return confirm('Hapus semua? Klik OK')" title="Delete All"><b><i class="fas fa-dumpster fa-sm text-white-50"></i> Delete All</b></a>
            <?php } ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Log</th>
                            <th>Send to</th>
                            <th>Sender</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Log</th>
                            <th>Send to</th>
                            <th>Sender</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM messages");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td>' . $row['message_categories'] . '</td>';
                            echo '<td class="text-center">' . $row['send_to'] . '</td>';
                            echo '<td class="text-center">' . $row['user_id'] . '</td>';
                            if ($row['message_status'] == "failed") {
                                echo '<td class="text-center"><span class="btn btn-danger">Failed</span></td>';
                            } else if ($row['message_status'] == "waiting") {
                                echo '<td class="text-center"><span class="btn btn-warning">Waiting</span></td>';
                            } else {
                                echo '<td class="text-center"><span class="btn btn-success">Success</span></td>';
                            }
                            echo '<td class="text-center">
                                <a id="details" class="btn-circle btn-info btn-sm" data-toggle="modal" data-target="#detailModal" title="Info"
                                    data-send_to="' . $row['send_to'] . '" 
                                    data-message="' . $row['message'] . '" 
                                    data-message_categories="' . $row['message_categories'] . '" 
                                    data-message_media="' . $row['message_media'] . '" 
                                    data-message_status="' . $row['message_status'] . '" 
                                    data-message_schedule="' . $row['message_schedule'] . '" 
                                    data-users_id="' . $row['users_id'] . '">
                                    <i class="fas fa-info"></i>
                                </a>
                                <a class="btn-circle btn-danger btn-sm" href="log-sender.php?act=delete&id=' . $row['id'] . '" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<!-- Detail Modal-->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Message</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="card-title">Send To</h5>
                <p id="sendTo" class="card-text text-center">.</p>
                <hr>
                <h5 class="card-title">Message</h5>
                <p id="messages" class="card-text">.</p>
                <hr>
                <h5 class="card-title">Date and Time</h5>
                <p id="messageSchedule" class="card-text text-center">.</p>
                <hr>
                <h5 class="card-title">Status Message</h5>
                <p id="messageStatus" class="card-text text-center">
                    <span class="badge badge-success">success</span>
                    <span class="badge badge-warning">waiting</span>
                    <span class="badge badge-danger">failed</span>
                </p>
                <hr>
                <h5 class="card-title">Categories Message</h5>
                <p id="messageCategories" class="card-text text-center">.</p>
                <hr>
                <h5 class="card-title">Media</h5>
                <div class="text-center">
                    <img id="messageMediaAttr" class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" src="" alt="images" title="images">
                    <p id="messageMedia" class="card-text text-center">
                </div>
                </p>
                <hr>
                <h5 class="card-title">Make By</h5>
                <p id="usersID" class="card-text text-center">admin@admin.com</p>
                <hr>
            </div>
        </div>
    </div>
</div>
<!-- End of Main Content -->

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
<script type="text/javascript" src="<?= $base_url ?>/assets/app/datatables.js"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '#details', function() {
            var sendTo = $(this).data('send_to');
            var messages = $(this).data('message');
            var messageCategories = $(this).data('message_categories');
            var messageMedia = $(this).data('message_media');
            var messageStatus = $(this).data('message_status');
            var messageSchedule = $(this).data('message_schedule');
            var usersID = $(this).data('users_id');
            $('#sendTo').text(sendTo);
            $('#messages').text(messages);
            $('#messageCategories').text(messageCategories);
            $('#messageMedia').text(messageMedia);
            $('#messageMediaAttr').attr('src', messageMedia);
            $('#messageStatus').text(messageStatus);
            $('#messageSchedule').text(messageSchedule);
            $('#usersID').text(usersID);
            $("#detailModal").modal('show');
        });
    });
</script>
</body>

</html>