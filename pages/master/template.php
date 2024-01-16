<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();

if ($login == 0) {
    toastr_set("error", "Forbiden: sorry you are not login!");
    redirect("../../login.php");
}

$email = $_SESSION['email'];
if (post("_token")) {
    csrf_validate();
    $email = $_SESSION['email'];
    $template_title = post("template_title");
    $template_content = post("template_content");
    $postMedia = post("media");

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
            redirect("template.php");
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
            toastr_set("error", "Format png, jpg, pdf only");
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

    if ($media == null) {
        echo "if";
        $query = mysqli_query($conn, "INSERT INTO templates(`template_title`, `template_content`, `user_id`, `created_at`)
        VALUES('$template_title', '$template_content', '$email', '$timeZone')");
    } else {
        echo "else";
        $query = mysqli_query($conn, "INSERT INTO templates(`template_title`, `template_content`, `template_media`, `user_id`, `created_at`)
        VALUES('$template_title', '$template_content', '$media', '$email', '$timeZone')");
        $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$email', '$timeZone')");
    }

    toastr_set("success", "Success: Create data!");
    redirect("template.php");
    exit;
}

if (get("act") == "delete") {
    $id = get("id");
    $where = mysqli_query($conn, "SELECT * FROM templates WHERE id='$id'");
    $row = mysqli_fetch_assoc($where);
    $url = parse_url($row['template_media']);
    $fileOnly = str_replace("/wa-api/public/uploads/", '', $url['path']);
    $dir = "../../public/uploads/" . $fileOnly;
    unlink($dir);
    $query = mysqli_query($conn, "DELETE FROM templates WHERE id='$id'");
    toastr_set("success", "Success: Delete data!");
    redirect("template.php");
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-pencil-alt"></i> <b>Master Template</b></h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#addModal" title="Add Data"><b>Create</b></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Images</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Images</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM templates");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM templates WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td>' . $row['template_title'] . '</td>';
                            echo '<td>' . $row['template_content'] . '</td>';
                            echo '<td class="text-center">
                                        <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 10rem;" src="' . $row['template_media'] . '" alt="images" title="images"> 
                                    </td>';
                            echo '<td class="text-center">
                                <a id="details" class="btn-circle btn-info btn-sm" data-toggle="modal" data-target="#detailModal" title="Info"
                                    data-template_title="' . $row['template_title'] . '" 
                                    data-template_content="' . $row['template_content'] . '" 
                                    data-template_media="' . $row['template_media'] . '" 
                                    data-created_at="' . $row['created_at'] . '">
                                    <i class="fas fa-info"></i>
                                </a>
                                <a class="btn-circle btn-danger btn-sm" href="template.php?act=delete&id=' . $row['id'] . '" title="Delete"><i class="fas fa-trash"></i></a>
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
                    <div class="form-group">
                        <label for="template_title">Title <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="template_title" name="template_title" placeholder="Title Template.." required />
                    </div>
                    <div class="form-group">
                        <label for="template_content">Message <span style="color: red;">*</span></label>
                        <textarea class="form-control" name="template_content" id="template_content" cols="30" rows="10" placeholder="text your message" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="media">Media <span style="color: red;">*</span></label>
                        <input type="url" class="form-control" id="media" name="media" placeholder="Example: https://source.unsplash.com/360x200?games" />
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
<!-- Detail Modal-->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Detail Template</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <h6 class="card-title"><b>Title</b></h6>
                <p id="templateTitle" class="card-text text-center">.</p>
                <hr>
                <h6 class="card-title"><b>Content</b></h6>
                <p id="templateContent" class="card-text">.</p>
                <hr>
                <h6 class="card-title"><b>Media</b></h6>
                <p class="card-text text-center">
                    <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" id="templateMedia" alt="images" title="images">
                </p>
                <hr>
                <h6 class="card-title"><b>Created At</b></h6>
                <p id="createdAt" class="card-text text-center">admin@admin.com</p>
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
<!-- <script type="text/javascript" src="<?= $base_url ?>/assets/app/load/load.js"></script> -->
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

    $(document).ready(function() {
        $(document).on('click', '#details', function() {
            var templateTitle = $(this).data('template_title');
            var templateContent = $(this).data('template_content');
            var templateMedia = $(this).data('template_media');
            var createdAt = $(this).data('created_at');
            $('#templateTitle').text(templateTitle);
            $('#templateContent').text(templateContent);
            $('#templateMedia').attr('src', +templateMedia);
            $('#createdAt').text(createdAt);
        });
    });
</script>
</body>

</html>