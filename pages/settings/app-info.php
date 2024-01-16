<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");

$login = cekSession();

if ($login == 0) {
    toastr_set("error", "Forbiden: sorry you are not login!");
    redirect("../../login.php");
}

if ($_SESSION['level'] !== 'admin') {
    toastr_set("error", "Forbiden Access!!");
    redirect("../dashboard.php");
}

if (post("seo")) {
    csrf_validate();
    $title = post("title");
    $description = post("description");
    $keyword = post("keyword");
    $author = post("author");
    $indexing = post("indexing");
    $url = post("url");
    $copyright = post("copyright");

    if (!empty($_FILES['favicon']) && $_FILES['favicon']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['favicon']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['favicon']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));
        $size = $_FILES['favicon']['size'];
        if ($size > 1000000) {
            toastr_set("error", "Maximal 1 mb");
            redirect("app-info.php");
            exit;
        }

        $allow = ['ico', 'png', 'jpeg', 'jpg'];
        if (in_array($ext, $allow)) {
            if ($ext == "ico") {
                $filename = $uploadName;
            }

            if ($ext == "png") {
                $filename = $uploadName;
            }

            if ($ext == "jpg") {
                $filename = $uploadName;
            }

            if ($ext == "jpeg") {
                $filename = $uploadName;
            }
        } else {
            toastr_set("error", "Format png, jpg, pdf only");
            redirect("app-info.php");
            exit;
        }

        move_uploaded_file($_FILES['favicon']['tmp_name'], '../../public/files/' . $filename);
        print_r(error_get_last());
        // Insert it into our tracking along with the original name
        $favicon = $base_url . "public/files/" . $filename;
    } else {
        $favicon = null;
    }

    if (!empty($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['image']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['image']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));
        $size = $_FILES['image']['size'];
        if ($size > 1000000) {
            toastr_set("error", "Maximal 1 mb");
            redirect("app-info.php");
            exit;
        }

        $allow = ['png', 'jpeg', 'jpg'];
        if (in_array($ext, $allow)) {

            if ($ext == "png") {
                $filename = $uploadName;
            }

            if ($ext == "jpg") {
                $filename = $uploadName;
            }

            if ($ext == "jpeg") {
                $filename = $uploadName;
            }
        } else {
            toastr_set("error", "Format png, jpg, pdf only");
            redirect("app-info.php");
            exit;
        }

        move_uploaded_file($_FILES['image']['tmp_name'], '../../public/files/' . $filename);
        print_r(error_get_last());
        // Insert it into our tracking along with the original name
        $image = $base_url . "public/files/" . $filename;
    } else {
        $image = null;
    }

    if (!empty($_FILES['images']) && $_FILES['images']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['images']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['images']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));
        $size = $_FILES['images']['size'];
        if ($size > 1000000) {
            toastr_set("error", "Maximal 1 mb");
            redirect("app-info.php");
            exit;
        }

        $allow = ['png', 'jpeg', 'jpg'];
        if (in_array($ext, $allow)) {

            if ($ext == "png") {
                $filename = $uploadName;
            }

            if ($ext == "jpg") {
                $filename = $uploadName;
            }

            if ($ext == "jpeg") {
                $filename = $uploadName;
            }
        } else {
            toastr_set("error", "Format png, jpg, pdf only");
            redirect("app-info.php");
            exit;
        }

        move_uploaded_file($_FILES['images']['tmp_name'], '../../public/files/' . $filename);
        print_r(error_get_last());
        // Insert it into our tracking along with the original name
        $images = $base_url . "public/files/" . $filename;
    } else {
        $images = null;
    }

    if ($title) {
        $query = mysqli_query(
            $conn,
            "UPDATE seo SET 
            title = '$title',
            description = '$description',
            keyword = '$keyword',
            author = '$author',
            favicon = '$favicon', 
            image = '$image', 
            indexing = '$indexing', 
            url = '$url', 
            copyright = '$copyright', 
            updated_at = '$timeZone', 
            created_at = '$timeZone' WHERE id = '1'"
        );
        toastr_set("success", "Success: Updated SEO");
        redirect("app-info.php");
        exit;
    } else {
        toastr_set("error", "Aborted: Something problem update data!");
        redirect("app-info.php");
        exit;
    }
}

if (post("information")) {
    csrf_validate();
    $title = post("title");
    $about = post("about");
    $telephone = post("telephone");
    $website = post("website");
    $email = post("email");
    $address = post("address");
    $owner = post("owner");

    if (!empty($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['logo']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['logo']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));
        $size = $_FILES['logo']['size'];
        if ($size > 1000000) {
            toastr_set("error", "Maximal 1 mb");
            redirect("app-info.php");
            exit;
        }

        $allow = ['ico', 'png', 'jpeg', 'jpg'];
        if (in_array($ext, $allow)) {
            if ($ext == "ico") {
                $filename = $uploadName;
            }

            if ($ext == "png") {
                $filename = $uploadName;
            }

            if ($ext == "jpg") {
                $filename = $uploadName;
            }

            if ($ext == "jpeg") {
                $filename = $uploadName;
            }
        } else {
            toastr_set("error", "Format png, jpg, pdf only");
            redirect("app-info.php");
            exit;
        }

        move_uploaded_file($_FILES['logo']['tmp_name'], '../../public/files/' . $filename);
        print_r(error_get_last());
        // Insert it into our tracking along with the original name
        $logo = $base_url . "public/files/" . $filename;
    } else {
        $logo = null;
    }

    if ($title) {
        $query = mysqli_query(
            $conn,
            "UPDATE information SET 
            title = '$title',
            about = '$about',
            telephone = '$telephone',
            website = '$website',
            email = '$email', 
            address = '$address', 
            logo = '$logo', 
            owner = '$owner', 
            updated_at = '$timeZone', 
            created_at = '$timeZone' WHERE id = '1'"
        );
        toastr_set("success", "Success: Update information app!");
        redirect("app-info.php");
        exit;
    } else {
        toastr_set("error", "Aborted: Something problem update data!!");
        redirect("app-info.php");
        exit;
    }
}

if (post('gateway')) {
    csrf_validate();
    $url_gateway = post('url_gateway');
    $webhook = post('webhook');
    $callback = post('callback');
    $limited = post('limited');
    if ($url_gateway) {
        $query = mysqli_query($conn, "UPDATE setting SET
        url_gateway = '$url_gateway',
        webhook = '$webhook',
        callback = '$callback',
        limited = '$limited',
        updated_at = '$timeZone',
        created_at = '$timeZone' WHERE id='1'");

        toastr_set("success", "Success: Updated data setting!");
        redirect("app-info.php");
        exit;
    } else {
        toastr_set("error", "Aborted: Something problem update data!");
        redirect("app-info.php");
        exit;
    }
}

if (get("act") == "generateToken") {
    $token = md5(rand(100000, 999999));
    mysqli_query($conn, "UPDATE setting SET token='$token' WHERE id='1'");
    toastr_set("success", "Success: Generate Token!");
    redirect("app-info.php");
}

require_once('../templates/header.php');
?>
</style>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-cogs"></i> <b>Apps & Setting</b></h1>
    </div>
    <div class="row">
        <div class="col-lg-5 col-md-6 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tag Meta and SEO</h6>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="text" name="seo" value="seo" hidden>
                        <?= csrf_input(); ?>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input class="form-control" type="text" name="title" id="title" value="<?= getSingleValDB("seo", "id", "1", "title") ?>" />
                        </div>
                        <div class="form-group">
                            <label for="keyword">Keyword</label>
                            <input class="form-control" type="text" name="keyword" id="keyword" value="<?= getSingleValDB("seo", "id", "1", "keyword") ?>" />
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" cols="30" rows="5"><?= getSingleValDB("seo", "id", "1", "description") ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="indexing">SEO Indexing</label>
                            <select class="form-control" name="indexing" id="indexing">
                                <option value="<?= getSingleValDB("seo", "id", "1", "indexing") ?>" selected><?= getSingleValDB("seo", "id", "1", "indexing") ?></option>
                                <option value="all">all</option>
                                <option value="nofollow">nofollow</option>
                                <option value="none">none</option>
                                <option value="noarchive">noarchive</option>
                                <option value="index, follow">index, follow</option>
                                <option value="noindex,nofollow">noindex, nofollow</option>
                                <option value="noarchive">noarchive</option>
                                <option value="noimageindex">noimageindex</option>
                                <option value="nofollow, noimageindex">nofollow, noimageindex</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input class="form-control" type="text" name="author" id="author" value="<?= getSingleValDB("seo", "id", "1", "author") ?>" />
                        </div>
                        <div class="form-group">
                            <label for="url">URL</label>
                            <input class="form-control" type="url" name="url" id="url" value="<?= getSingleValDB("seo", "id", "1", "url") ?>" />
                        </div>
                        <div class="form-group">
                            <label for="copyright">Copyright</label>
                            <input class="form-control" type="text" name="copyright" id="copyright" value="<?= getSingleValDB("seo", "id", "1", "copyright") ?>" />
                        </div>
                        <div class="form-group">
                            <label for="favicon">Favicon</label>
                            <input class="form-control" type="file" name="favicon" id="favicon" />
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input class="form-control" type="file" name="image" id="image" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-7 col-md-6 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Generate Token</h6>
                </div>
                <form action="" method="POST">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="token">Token</label>
                            <input class="form-control" type="text" name="token" id="token" value="<?= getSingleValDB("setting", "id", "1", "token") ?>" placeholder="YOUR API KEY" readonly />
                        </div>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-danger" href="app-info.php?act=generateToken">Generate Token</a>
                    </div>
                </form>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Link Gateway</h6>
                </div>
                <form action="" method="POST">
                    <input id="gateway" name="gateway" type="hidden" class="form-control" value="gateway" />
                    <div class="card-body">
                        <?= csrf_input(); ?>
                        <div class="form-group">
                            <label for="url_gateway">URL Gateway</label>
                            <input class="form-control" type="text" name="url_gateway" id="url_gateway" value="<?= getSingleValDB("setting", "id", "1", "url_gateway") ?>" placeholder="http://127.0.0.1:8000/" />
                        </div>
                        <div class="form-group">
                            <label for="webhook">Web Hook</label>
                            <input class="form-control" type="text" name="webhook" id="webhook" value="<?= getSingleValDB("setting", "id", "1", "webhook") ?>" placeholder="https://domain.com/webhook.php" />
                        </div>
                        <div class="form-group">
                            <label for="callback">Call Back</label>
                            <input class="form-control" type="text" name="callback" id="callback" value="<?= getSingleValDB("setting", "id", "1", "callback") ?>" placeholder="https://localhost:8000/" />
                        </div>
                        <div class="form-group">
                            <label for="limited">Limit Messages</label>
                            <input class="form-control" type="number" name="limited" id="limited" value="<?= getSingleValDB("setting", "id", "1", "limited") ?>" placeholder="Default Limit: 300" />
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">App Info</h6>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <input type="text" name="information" value="information" hidden>
                                <?= csrf_input(); ?>
                                <div class="form-group">
                                    <label for="title">Title Website</label>
                                    <input class="form-control" type="text" name="title" id="title" value="<?= getSingleValDB("information", "id", "1", "title") ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="owner">Owner</label>
                                    <input class="form-control" type="text" name="owner" id="owner" value="<?= getSingleValDB("information", "id", "1", "owner") ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input class="form-control" type="file" name="image" id="image" disabled />
                                </div>
                                <div class="form-group">
                                    <label for="video">Video</label>
                                    <input class="form-control" type="file" name="video" id="video" disabled />
                                </div>
                                <div class="form-group">
                                    <label for="website">URL Website</label>
                                    <input class="form-control" type="url" name="website" id="website" value="<?= getSingleValDB("information", "id", "1", "website") ?>" />
                                </div>
                            </div>
                            <div class="col-lg-6 mb-4">
                                <div class="form-group">
                                    <label for="email">Email Organization</label>
                                    <input class="form-control" type="text" name="email" id="email" value="<?= getSingleValDB("information", "id", "1", "email") ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="telephone">Telephone</label>
                                    <input class="form-control" type="text" name="telephone" id="telephone" value="<?= getSingleValDB("information", "id", "1", "telephone") ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input class="form-control" type="file" name="logo" id="logo" />
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" name="address" id="address" cols="30" rows="5"><?= getSingleValDB("information", "id", "1", "address") ?></textarea>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-4">
                                <div class="form-group">
                                    <label for="about">About</label>
                                    <textarea class="form-control" name="about" id="about" cols="30" rows="5"><?= getSingleValDB("information", "id", "1", "about") ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
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
</body>

</html>