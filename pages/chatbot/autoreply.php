<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("error", "Forbiden: Sorry you are not login!");
    redirect("../../login.php");
}

if (post("_token")) {
    csrf_validate();
    $keyword = post("keyword");
    $response = post("response");
    $type = post("type");
    $email = $_SESSION['email'];
    if (post("media")) {
        $media = post("media");
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
            toastr_set("error", "Max. 1 mb");
            redirect("autoreply.php");
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
            redirect("autoreply.php");
            exit;
        }

        move_uploaded_file($_FILES['media']['tmp_name'], '../../public/uploads/' . $filename);
        print_r(error_get_last());
        // Insert it into our tracking along with the original name
        $media = $base_url . "public/uploads/" . $filename;
    } else {
        $media = null;
    }
    if (post("keyword")) {
        if ($media == null) {
            $query = mysqli_query($conn, "INSERT INTO autoreplies(`keyword`, `response`, `user_id`, `created_at`)
            VALUES('$keyword', '$response', '$email', '$timeZone')");
            toastr_set("success", "Success: Create dataa");
            redirect("autoreply.php");
            exit;
        } else {
            $query = mysqli_query($conn, "INSERT INTO autoreplies(`keyword`, `response`, `media`, `user_id`, `created_at`)
            VALUES('$keyword', '$response', '$media', '$email', '$timeZone')");
            $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$email', '$timeZone')");
            toastr_set("success", "Success: Create data");
            redirect("autoreply.php");
            exit;
        }
    } else {
        toastr_set("error", "Aborted: Failed create data");
        redirect("autoreply.php");
        exit;
    }
}

if (get("act") == "delete") {
    $id = get("id");
    $where = mysqli_query($conn, "SELECT * FROM autoreplies WHERE id='$id'");
    $row = mysqli_fetch_assoc($where);
    // $url = parse_url($row['media']);
    // $fileOnly = str_replace("/wa-api/public/uploads/", '', $url['path']);
    // $dir = "../../public/uploads/" . $fileOnly;
    // unlink($dir);
    $media = $row['media'];
    // var_dump($media);
    // die;
    $whereMedia = mysqli_query($conn, "DELETE FROM media WHERE media='$media'");
    $query = mysqli_query($conn, "DELETE FROM autoreplies WHERE id='$id'");
    toastr_set("success", "Success: Delete data!");
    redirect("autoreply.php");
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><b><i class="fas fa-robot"></i> Auto Reply</b></h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#addModal" title="Add"><b>Create Autoreply</b></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Keyword</th>
                            <th width="1px">Reply</th>
                            <th>Media</th>
                            <th>Owner</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Keyword</th>
                            <th>Reply</th>
                            <th>Media</th>
                            <th>Owner</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM autoreplies");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM autoreplies WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo '<tr>';
                            echo '<td>' . $no++ . '</td>';
                            echo '<td>' . $row['keyword'] . '</td>';
                            echo '<td><h5><span class="badge badge-secondary">' . $row['response'] . '</span></h5></td>';
                            echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 10rem;" src="' . $row['media'] . '" alt="images" title="images"/>';
                            echo '<td>' . $row['user_id'] . '</td>';
                            echo '<td class="text-center"><a class="btn-circle btn-danger btn-sm" href="autoreply.php?act=delete&id=' . $row['id'] . '" title="Delete"><i class="fas fa-trash"></i></a></td>';
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
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Create Data</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <div class="form-group" id="keyword" style="display: block;">
                        <label for="keyword">Keyword</label>
                        <input type="text" class="form-control" name="keyword" placeholder="request..">
                    </div>
                    <div class="form-group" id="response" style="display: block;">
                        <label for="response">Response</label>
                        <textarea class="form-control" id="response" name="response" placeholder="response from keyword.."></textarea>
                    </div>
                    <div class="form-group" id="media" style="display: block;"></div>
                    <label for="media">Media <span style="color: red;">*</span></label>
                    <input type="url" class="form-control" id="media" name="media" placeholder="Example: https://source.unsplash.com/360x200?games" required />
                    <div class="form-check">
                        <input type="checkbox" name="mediacheck" class="form-check-input" id="mediacheck" checked>
                        <label class="form-check-label" for="mediacheck" id="labelMedia">Type url image</label>
                    </div>
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

<!-- End of Main Content -->
</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
<script type="text/javascript" src="<?= $base_url ?>/assets/app/datatables.js"></script>
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
</body>

</html>