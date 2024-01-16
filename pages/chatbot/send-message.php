<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "Forbiden: Sorry you are not login!");
    redirect("../../login.php");
}

$success = "success";
$waiting = "waiting";
$failed = "failed";
$sender = $_SESSION['email'];
$key = api_key();
if (post("sendMessage")) {
    csrf_validate();
    $number = post("send_to");
    $message = utf8_encode($_POST["message"]);
    $categories = "send-message";
    $media = NULL;

    $res = sendMSG($number, $message, $key);
    $count = countDB("message", "message", $message);
    if ($res['status'] == 200) {
        if ($count == 0) {
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$success', '$number', '$sender', '$timeZone')");
            toastr_set("success", "Success: Message sent successfully");
        }
        redirect("send-message.php");
    } elseif ($res['status'] == 500) {
        if ($count == 0) {
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$waiting', '$number', '$sender', '$timeZone')");
            toastr_set("error", "Aborted: Internal Server Error");
        }
        redirect("send-message.php");
    } else {
        $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
        VALUES ('$message', '$categories', '$media', '$failed', '$number', '$sender', '$timeZone')");
        toastr_set("error", "Aborted: Have problem to sending message");
        redirect("send-message.php");
    }
}

if (post("sendGroup")) {
    csrf_validate();
    $number = post("send_to");
    $message = utf8_encode($_POST["message"]);
    $categories = "send-group";
    $media = NULL;

    $res = sendGroup($number, $message, $key);
    $count = countDB("message", "message", $message);
    if ($res['status'] == 200) {
        if ($count == 0) {
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$success', '$number', '$sender', '$timeZone')");
            toastr_set("success", "Success: Message sent successfully");
        }
        redirect("send-message.php");
    } elseif ($res['status'] == 500) {
        if ($count == 0) {
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$waiting', '$number', '$sender', '$timeZone')");
            toastr_set("error", "Aborted: Internal Server Error");
        }
        redirect("send-message.php");
    } else {
        $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
        VALUES ('$message', '$categories', '$media', '$failed', '$number', '$sender', '$timeZone')");
        toastr_set("error", "Aborted: Have problem to sending message");
        redirect("send-message.php");
    }
}

if (post("sendMedia")) {
    csrf_validate();
    $number = post("number");
    $message = utf8_encode($_POST["message"]);
    $categories = "send-media";

    if ($_FILES["media"]) {
        $target_directory = "../../public/uploads/";
        $target_send_media = "public/uploads/" . basename($_FILES["media"]["name"]);
        $target_file = $target_directory . basename($_FILES["media"]["name"]);
        $uploadOK = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // check if image file is a actual image or fake image
        $check = getimagesize($_FILES["media"]["tmp_name"]);
        if ($check !== false) {
            $uploadOK = 1;
        } else {
            $msg = "File is not an image";
            $uploadOK = 0;
        }

        // allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf") {
            $msg = "Aborted: Sorry, only extension JPG, JPEG, PNG, PDF and GIF files allowed";
            $uploadOK = 0;
        }

        if ($uploadOK == 0) {
            toastr_set("error", $msg);
        } else {
            if (move_uploaded_file($_FILES['media']['tmp_name'], $target_file)) {
                $url_file = $base_url . $target_send_media;
                $res = sendMedia($number, $message, $url_file, $key);
                if ($res['status'] == 200) {
                    if ($count == 0) {
                        $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
                        VALUES ('$message', '$categories', '$media', '$success', '$number', '$sender', '$timeZone')");
                        toastr_set("success", "Success: Message sent successfully");
                    }
                    redirect("send-message.php");
                } else {
                    toastr_set("error", "Aborted: Have problem to sending message");
                    redirect("send-message.php");
                }
            } else {
                toastr_set("error", $msg);
                redirect("send-message.php");
            }
        }
    }

    if (post("media")) {
        $url_file = post("media");
        $res = sendMedia($number, $message, $url_file, $key);
        if ($res['status'] == 200) {
            if ($count == 0) {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
                VALUES ('$message', '$categories', '$media', '$success', '$number', '$sender', '$timeZone')");
                toastr_set("success", "Success: Message sent successfully");
            }
            redirect("send-message.php");
        } elseif ($res['status'] == 500) {
            if ($count == 0) {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
                VALUES ('$message', '$categories', '$media', '$waiting', '$number', '$sender', '$timeZone')");
                toastr_set("error", "Aborted: Internal Server Error");
            }
            redirect("send-message.php");
        } else {
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$failed', '$number', '$sender', '$timeZone')");
            toastr_set("error", "Aborted: Have problem to sending message");
            redirect("send-message.php");
        }
    }
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-paper-plane text-success"></i> <b>Send Message</b></h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="modal-title"><i class="fas fa-paper-plane"></i> <b>Send Message</b></h5>
                </div>
                <form action="" method="post">
                    <div class="card-body">
                        <input type="hidden" class="form-control" id="sendMessage" name="sendMessage" value="sendMessage" />
                        <?= csrf_input(); ?>
                        <div class="form-group">
                            <label for="send_to">Send to number <span style="color: red;">*</span></label>
                            <input type="number" class="form-control" id="send_to" name="send_to" placeholder="Format number: 628 or 082" required />
                        </div>
                        <div class="form-group">
                            <label for="message">Messages <span style="color: red;">*</span></label>
                            <textarea class="form-control" id="message" name="message" placeholder="type your holly text.." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="modal-title"><i class="fas fa-paper-plane"></i> <b>Send To Group</b></h5>
                </div>
                <form action="" method="post">
                    <div class="card-body">
                        <input type="hidden" class="form-control" id="sendGroup" name="sendGroup" value="sendGroup" />
                        <?= csrf_input(); ?>
                        <div class="form-group">
                            <label for="send_to">ID Group or Name Group <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="send_to" name="send_to" placeholder="Format ID Group: 123456*****@g.us" required />
                        </div>
                        <div class="form-group">
                            <label for="message">Messages <span style="color: red;">*</span></label>
                            <textarea class="form-control" id="message" name="message" placeholder="type your holly text.." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="modal-title"><i class="fas fa-paper-plane"></i> <b>Send Media</b></h5>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" class="form-control" id="sendMedia" name="sendMedia" value="sendMedia" />
                        <?= csrf_input(); ?>
                        <div class="form-group">
                            <label for="number">Sent to number <span style="color: red;">*</span></label>
                            <input type="number" class="form-control" id="number" name="number" placeholder="Format number: 628 or 082" required />
                        </div>
                        <div class="form-group">
                            <label for="message">Messages <span style="color: red;">*</span></label>
                            <textarea class="form-control" id="message" name="message" placeholder="type your holly text.." required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="media">Media <span style="color: red;">*</span></label>
                            <input type="url" class="form-control" id="media" name="media" placeholder="Example: https://source.unsplash.com/360x200?games" required />
                            <div class="form-check">
                                <input type="checkbox" name="mediacheck" class="form-check-input" id="mediacheck" checked>
                                <label class="form-check-label" for="mediacheck" id="labelMedia">Type url image</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
<script type="text/javascript" src="<?= $base_url ?>/assets/app/datatables.js"></script>
</body>

<script>
    $(document).ready(function() {
        $('#mediacheck').click(function() {
            if ($(this).is(':checked')) {
                $('#media').attr('type', 'url');
                $('#labelMedia').text("Type url image");
            } else {
                $('#media').attr('type', 'file');
                $('#labelMedia').text("Browse file. ext: jpg, jpeg, png & pdf. max: 1MB.");
            }
        })
    })
</script>

</html>