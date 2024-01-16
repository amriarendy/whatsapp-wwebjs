<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "Forbiden: Sorry you are not login!");
    redirect("../../login.php");
}

if (post("send_broadcast")) {
    csrf_validate();
    $email = $_SESSION['email'];
    $message = post("message");
    $messageCategories = "send-broadcast";
    $messageStatus = "waiting";
    $postMedia = post("media");

    $schedule = date("Y-m-d H:i:s", strtotime(post("date") . " " . post("time")));
    if ($postMedia) {
        $media = $postMedia;
    } else if (!empty($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['media']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['media']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));
        $size = $_FILES['media']['size'];
        if ($size > 1000000) {
            toastr_set("error", "Maximal 1 mb");
            redirect("send-broadcast.php");
            exit;
        }

        $allow = ['png', 'jpeg', 'pdf', 'jpg'];
        if (in_array($ext, $allow)) {
            if ($ext == "png") {
                $filename = round(microtime(true)) . mt_rand() . '.png';
            }

            if ($ext == "pdf") {
                $filename = round(microtime(true)) . mt_rand() . '.pdf';
            }

            if ($ext == "jpg") {
                $filename = round(microtime(true)) . mt_rand() . '.jpg';
            }

            if ($ext == "jpeg") {
                $filename = round(microtime(true)) . mt_rand() . '.jpeg';
            }
        } else {
            toastr_set("error", "Format: png, jpg, pdf only");
            redirect("send-broadcast.php");
            exit;
        }

        move_uploaded_file($_FILES['media']['tmp_name'], '../../public/uploads/' . $filename);
        print_r(error_get_last());
        // Insert it into our tracking along with the original name
        $media = $base_url . "public/uploads/" . $filename;
    } else {
        $media = null;
    }

    if ($_POST['typeTarget'] == "personalBroadcast") {
        csrf_validate();
        if (isset($_POST['target'])) {
            foreach ($_POST['target'] as $data) {
                $n = $data;
                $checkNumbers = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_number = '$n'");
                $data2 = $checkNumbers->fetch_assoc();
                $messages = strtr($message, array(
                    '{contact_name}' => $data2['contact_name'],
                ));
                $messages2 = utf8_encode($messages);
                if ($media == null) {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
                } else {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                    $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `created_at`) VALUES('$media', '$timeZone')");
                }
            }
        } else {
            toastr_set("error", "Abort: Contact empty!");
            redirect("send-broadcast.php");
            exit;
        }
    } else if ($_POST['typeTarget'] == "categoriesBroadcast") {
        if (empty($_POST['targetCategories'])) {
            toastr_set("error", "Abort: Contact category empty!");
            redirect("send-broadcast.php");
            exit;
        } else {
            $categories = $_POST['targetCategories'];
            $n = getCategoryNumber($categories);
            foreach ($n as $nn) {
                if ($media == null) {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                VALUES('$message', '$nn', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
                } else {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$message', '$nn', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                    $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `created_at`) VALUES('$media', '$timeZone')");
                }
            }
        }
    } else if ($_POST['typeTarget'] == "sendAll") {
        $email = $_SESSION['email'];
        $checkNumbers = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_type='PERSONAL'");
        while ($data = $checkNumbers->fetch_assoc()) {
            $messages = strtr($message, array(
                '{contact_name}' => $data['contact_name'],
            ));
            $messages2 = utf8_encode($messages);
            $n = $data['contact_number'];
            if ($media == null) {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
            } else {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `created_at`) VALUES('$media', '$timeZone')");
            }
        }
    }

    // if (!$result) {
    //     die('Query Error : ' . mysqli_errno($conn) .
    //         ' - ' . mysqli_error($conn));
    // }
    toastr_set("success", "Success: Create schedule message");
    redirect("send-broadcast.php");
    exit;
}

if (post("send_template_message")) {
    csrf_validate();
    $email = $_SESSION['email'];
    $message = post("template");
    $messageCategories = "send-template";
    $messageStatus = "waiting";

    if ($message) {
        $where = mysqli_query($conn, "SELECT * FROM templates WHERE id='$message'");
        $row = mysqli_fetch_array($where);
        $content = $row['template_content'];
        $media = $row['template_media'];
    }

    $schedule = date("Y-m-d H:i:s", strtotime(post("date") . " " . post("time")));
    if ($_POST['typeTarget'] == "personalTemplates") {

        if (isset($_POST['target'])) {
            foreach ($_POST['target'] as $data) {
                $n = $data;
                $checkNumbers = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_number = '$n'");
                $data2 = $checkNumbers->fetch_assoc();
                $messages = strtr($content, array(
                    '{contact_name}' => $data2['contact_name'],
                ));
                $messages2 = utf8_encode($messages);
                if ($media == null) {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
                } else {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                }
            }
        } else {
            toastr_set("error", "Abort: Contact is empty!");
            redirect("send-broadcast.php");
            exit;
        }
    } else if ($_POST['typeTarget'] == "categoriesTemplates") {
        if (empty($_POST['targetCategories'])) {
            toastr_set("error", "Abort: Contact category is empty!");
            redirect("send-broadcast.php");
            exit;
        } else {
            $categories = $_POST['targetCategories'];
            $n = getCategoryNumber($categories);
            foreach ($n as $nn) {
                if ($media == null) {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                VALUES('$message', '$nn', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
                } else {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$message', '$nn', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                }
            }
        }
    } else if ($_POST['typeTarget'] == "sendAll") {
        $email = $_SESSION['email'];
        $checkNumbers = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_type='PERSONAL'");
        while ($data = $checkNumbers->fetch_assoc()) {
            $messages = strtr($content, array(
                '{contact_name}' => $data['contact_name'],
            ));
            $messages2 = utf8_encode($messages);
            $n = $data['contact_number'];
            if ($media == null) {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
            } else {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
            }
        }
    }

    // if (!$result) {
    //     die('Query Error : ' . mysqli_errno($conn) .
    //         ' - ' . mysqli_error($conn));
    // }
    toastr_set("success", "Success: create schedule message");
    redirect("send-broadcast.php");
    exit;
}

if (get("act") == "delete_all") {
    $query = mysqli_query($conn, "DELETE FROM messages");
    toastr_set("success", "Success: Data All Deleted!");
    redirect("send-broadcast.php");
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-bullhorn"></i> <b>Send Broadcast</b></h1>
        <?php if ($_SESSION['level'] == 'admin') { ?>
            <a href="../../helpers/cronjob.php" id="linkButton" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" title="Push Cronjob"><b><i class="fas fa-clock"></i> Push Cronjob</b></a>
        <?php } ?>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#messageBroadcast" title="send broadcast message"><b>Create Broadcast</b></a>
            <a class="btn btn-primary" data-toggle="modal" data-target="#messageTemplate" title="send broadcast template"><b>Create Broadcast Template</b></a>
            <a class="btn btn-danger" href="send-broadcast.php?act=delete_all" onclick="return confirm('Hapus semua? Klik OK')" title="Delete All"><b><i class="fas fa-dumpster fa-sm text-white-50"></i> Delete All</b></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Send to</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Schedule</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Send to</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Schedule</th>
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
                            echo '<td class="text-center">' . $row['send_to'] . '</td>';
                            echo '<td class="text-center">' . substr($row['message'], 0, 60) . '...</td>';
                            if ($row['message_status'] == "success") {
                                echo '<td class="text-center"><span class="btn btn-success">Success</span></td>';
                            } else if ($row['message_status'] == "waiting") {
                                echo '<td class="text-center"><span class="btn btn-warning">Waiting</span></td>';
                            } else {
                                echo '<td class="text-center"><span class="btn btn-danger">Failed</span></td>';
                            }
                            echo '<td class="text-center">' . $row['message_schedule'] . '</td>';
                            echo '<td class="text-center">
                                <a id="details" class="btn-circle btn-info btn-sm" data-toggle="modal" data-target="#detailModal" title="Info"
                                    data-send_to="' . $row['send_to'] . '" 
                                    data-message="' . $row['message'] . '" 
                                    data-message_categories="' . $row['message_categories'] . '" 
                                    data-message_media="' . $row['message_media'] . '" 
                                    data-message_status="' . $row['message_status'] . '" 
                                    data-message_schedule="' . $row['message_schedule'] . '" 
                                    data-user_id="' . $row['user_id'] . '">
                                    <i class="fas fa-info"></i>
                                </a>
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

<!-- modal start -->
<!-- Add Modal-->
<div class="modal fade" id="messageBroadcast" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Send Broadcast</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" class="form-control" id="send_broadcast" name="send_broadcast" value="send_broadcast" />
                    <div class="form-group">
                        <label for="message">Messages <span style="color: red;">*</span></label>
                        <textarea class="form-control" id="message" name="message" placeholder="type your holly text.." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="media">Media <a class="text-info" href="<?= $base_url . '/pages/master/media.php' ?>">to media resource</a></label>
                        <input type="url" class="form-control" id="media" name="media" placeholder="Example: https://source.unsplash.com/360x200?games" />
                        <div class="form-check">
                            <input type="checkbox" name="mediacheck" class="form-check-input" id="mediacheck" checked>
                            <label class="form-check-label" for="mediacheck" id="labelMedia">Type url image</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date">Date <span style="color: red;">*</span></label>
                        <input type="date" class="form-control" id="date" name="date" required />
                    </div>
                    <div class="form-group">
                        <label for="time">Time <span style="color: red;">*</span></label>
                        <input type="time" class="form-control" id="time" name="time" required />
                    </div>
                    <div class="form-group">
                        <label for="typeTarget">Send By Contact Categories <span style="color: red;">*</span></label>
                        <select class="form-control js-states" id="typeTarget" name="typeTarget" onchange="sendSelectedBroadcast(this);" required>
                            <option value="" selected>Selected Target</option>
                            <option value="personalBroadcast">To Personal</option>
                            <option value="categoriesBroadcast">To Categories</option>
                            <option value="sendAll">Send to all contact</option>
                        </select>
                    </div>
                    <div id="sendByPersonalBroadcast" class="form-group" style="display: none;">
                        <label for="target">Send To <span style="color: red;">*</span></label>
                        <select id="target" class="form-control js-example-basic-multiple" name="target[]" multiple="multiple" style="width: 100%">
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_type='PERSONAL'");
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['contact_number'] . '">' . $row['contact_name'] . ' - ' . $row['contact_number'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div id="sendByCategoriesBroadcast" class="form-group" style="display: none;">
                        <label for="targetCategories">Send To Categories <span style="color: red;">*</span></label>
                        <select id="targetCategories" class="form-control" name="targetCategories">
                            <option value="" selected>Select Target</option>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM categories");
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['category'] . '">' . $row['category'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="pesan1" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Template -->
<div class="modal fade" id="messageTemplate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Send Broadcast Template</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" class="form-control" id="send_template_message" name="send_template_message" value="send_template_message" />
                    <div class="form-group">
                        <label for="template">Message Template <span style="color: red;">*</span></label>
                        <select id="template" class="form-control" name="template" required>
                            <option value="" selected> Choose Your Templates </option>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM templates");
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['id'] . '">' . $row['template_title'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Date <span style="color: red;">*</span></label>
                        <input type="date" class="form-control" id="date" name="date" required />
                    </div>
                    <div class="form-group">
                        <label for="time">Time <span style="color: red;">*</span></label>
                        <input type="time" class="form-control" id="time" name="time" required />
                    </div>
                    <div class="form-group">
                        <label for="typeTarget">Send By Contact Categories <span style="color: red;">*</span></label>
                        <select class="form-control js-states" id="typeTargetTemplates" name="typeTarget" onchange="sendSelectedTemplates(this);" required>
                            <option value="" selected>Selected Target</option>
                            <option value="personalTemplates">To Personal</option>
                            <option value="categoriesTemplates">To Categories</option>
                            <option value="sendAll">Send to all contact</option>
                        </select>
                    </div>
                    <div id="sendByPersonalTemplates" class="form-group" style="display: none;">
                        <label for="target">Send To <span style="color: red;">*</span></label>
                        <select id="targetTemplates" class="form-control js-example-basic-multiple-template" name="target[]" multiple="multiple" style="width: 100%">
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_type='PERSONAL'");
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['contact_number'] . '">' . $row['contact_name'] . ' - ' . $row['contact_number'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div id="sendByCategoriesTemplates" class="form-group" style="display: none;">
                        <label for="targetCategories">Send To Categories <span style="color: red;">*</span></label>
                        <select id="targetCategoriesTemplates" class="form-control" name="targetCategories">
                            <option value="" selected>Select Target</option>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM categories");
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['category'] . '">' . $row['category'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal-->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Detail Message</b></h5>
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

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
<script type="text/javascript" src="<?= $base_url ?>/assets/app/datatables.js"></script>
<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            dropdownAutoWidth: true
        });
    });
    $(document).ready(function() {
        $('.js-example-basic-multiple-template').select2({
            dropdownAutoWidth: true
        });
    });

    /* for broadcast messages */
    function sendSelectedBroadcast(that) {
        if (that.value == "personalBroadcast") {
            document.getElementById("sendByPersonalBroadcast").style.display = "block";
        } else {
            document.getElementById("sendByPersonalBroadcast").style.display = "none";
        }

        if (that.value == "categoriesBroadcast") {
            document.getElementById("sendByCategoriesBroadcast").style.display = "block";
        } else {
            document.getElementById("sendByCategoriesBroadcast").style.display = "none";
        }
    }

    /* for template messages */
    function sendSelectedTemplates(that) {
        if (that.value == "personalTemplates") {
            document.getElementById("sendByPersonalTemplates").style.display = "block";
        } else {
            document.getElementById("sendByPersonalTemplates").style.display = "none";
        }

        if (that.value == "categoriesTemplates") {
            document.getElementById("sendByCategoriesTemplates").style.display = "block";
        } else {
            document.getElementById("sendByCategoriesTemplates").style.display = "none";
        }
    }

    $(document).ready(function() {
        $('#mediacheck').click(function() {
            if ($(this).is(':checked')) {
                $('#media').attr('type', 'url');
                $('#labelMedia').text("Type url image");
            } else {
                $('#media').attr('type', 'file');
                $('#labelMedia').text("Browse file. ext: jpg, jpeg, png & pdf. max: 1MB.");
            }
        });
    });

    $(document).ready(function() {
        $(document).on('click', '#details', function() {
            var sendTo = $(this).data('send_to');
            var messages = $(this).data('message');
            var messageCategories = $(this).data('message_categories');
            var messageMedia = $(this).data('message_media');
            var messageStatus = $(this).data('message_status');
            var messageSchedule = $(this).data('message_schedule');
            var usersID = $(this).data('user_id');
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