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
            redirect("media.php");
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
            redirect("media.php");
            exit;
        }

        move_uploaded_file($_FILES['media']['tmp_name'], '../../public/uploads/' . $filename);
        print_r(error_get_last());
        $media = $base_url . "public/uploads/" . $filename;
    } else {
        toastr_set("error", "File input empty!");
        redirect("media.php");
        exit;
    }

    if (isset($_POST['link_image'])) {
        csrf_validate();
        $query = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$email', '$timeZone')");

        toastr_set("success", "Success: Create data!");
        redirect("media.php");
        exit;
    } else {
        toastr_set("error", "Aborted: Failed create data!!");
        redirect("media.php");
        exit;
    }
}

if (get("act") == "delete") {
    $id = get("id");
    $where = mysqli_query($conn, "SELECT * FROM media WHERE id='$id'");
    $row = mysqli_fetch_assoc($where);
    $url = parse_url($row['media']);
    $fileOnly = str_replace("/wa-api/public/uploads/", '', $url['path']);
    $dir = "../../public/uploads/" . $fileOnly;
    unlink($dir);
    $query = mysqli_query($conn, "DELETE FROM media WHERE id='$id'");
    toastr_set("success", "Success: Delete data!");
    redirect("media.php");
}

if (get("act") == "delete_all") {
    $query = mysqli_query($conn, "DELETE FROM media");
    toastr_set("success", "Success: Delete all data!");
    redirect("media.php");
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-image"></i> <b>Master Media</b></h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#addModal" title="Add Data"><b>Create</b></a>
            <a class="btn btn-success" data-toggle="modal" data-target="#importMedia" title="Import Data"><i class="fas fa-file-excel fa-sm text-white-50"></i> <b>Import Excel</b></a>
            <?php if ($_SESSION['level'] == 'admin') { ?>
                <a class="btn btn-danger" href="media.php?act=delete_all" title="Delete All"><i class="fas fa-dumpster fa-sm text-white-50"></i> <b>Delete All</b></a>
            <?php } ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>media</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>media</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM media");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM media WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 10rem;" src="' . $row['media'] . '" alt="images" title="images"/> <br> 
                            <input class="form-control" type="text" id="urlMedia" name="urlMedia" value="' . $row['media'] . '" /></td>';
                            echo '<td class="text-center">
                            <button class="btn-circle btn-secondary btn-sm" title="' . $row['media'] . '"onclick="copyMediaURL()"><i class="fas fa-copy"></i></button>
                            <a class="btn-circle btn-danger btn-sm" href="media.php?act=delete&id=' . $row['id'] . '" title="Delete"><i class="fas fa-trash"></i></a>
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
                        <input type="hidden" name="link_image" id="link_image" />
                        <label for="media">Media <span style="color: red;"><a id="resourceMedia" class="text-info" href="<?= $base_url . '/pages/master/media.php' ?>">to media resource</a></span></label>
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

<!-- Import Media-->
<div class="modal fade" id="importMedia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Import Link Media</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="../../lib/import-excel.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="media" id="media" value="media">
                    <?= csrf_input(); ?>
                    <div class="form-group">
                        <label for="id_data">Import <span style="color: red;">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" required />
                    </div>
                    <p>Download Template <a href="<?php echo $base_url . 'public/files/Template Import Media.xlsx' ?>" target="_blank">Disini</a></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal end -->

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

    function copyMediaURL() {
        var copyURL = document.getElementById("urlMedia");
        copyURL.select();
        copyURL.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyURL.value);
        alert("Copied the text: " + copyURL.value);
    }
</script>
</body>

</html>