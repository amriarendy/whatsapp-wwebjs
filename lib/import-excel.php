<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");
include_once("excel.php");
$login = cekSession();
if ($login == 0) {
    redirect(".../auth/login.php");
}

if (post("personal")) {
    if (!empty($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        if (is_uploaded_file($_FILES['file']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }
        // Rename the uploaded file
        $uploadName = $_FILES['file']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));

        $allow = ['xls', 'xlsx'];
        if (in_array($ext, $allow)) {
            if ($ext == "xls") {
                $filename = round(microtime(true)) . mt_rand() . '.xls';
            }

            if ($ext == "xlsx") {
                $filename = round(microtime(true)) . mt_rand() . '.xlsx';
            }
        } else {

            toastr_set("error", "Format xls, xlsx only");
            redirect("./../pages/master/contact.php");
            exit;
        }

        move_uploaded_file($_FILES['file']['tmp_name'], 'excel/' . $filename);

        // Insert it into our tracking along with the original name
        $file = "excel/" . $filename;
    } else {
        $file = null;
    }

    if ($file == null) {
        toastr_set("error", "Format file tidak sesuai");
        redirect("./../pages/master/contact.php");
        exit;
    }

    $categoryContact = post("contact_category");
    $a = 2;
    $b = 1;
    $c = 2;
    $d = 3;
    if ($a && $b && $c && $file) {
        $a = $a - 1;
        $b = $b - 1;
        $c = $c - 1;
        $d = $d - 1;
        if ($xlsx = SimpleXLSX::parse($file)) {
            $i = 0;
            foreach ($xlsx->rows() as $elt) {
                if ($i >= $a) {
                    $name = $elt[$b];
                    $numbers = $elt[$c];
                    $type = "PERSONAL";
                    $categories = $categoryContact;
                    $user_id = $_SESSION['email'];

                    $count = countDB("contacts", "contact_number", $numbers);
                    if ($count == 0) {
                        $query = mysqli_query($conn, "INSERT INTO contacts(`contact_name`, `contact_number`, `contact_type`, `contact_category`, `user_id`, `created_at`)
                                VALUES('$name', '$numbers', '$type', '$categories', '$user_id', '$timeZone')");
                    }
                }
                $i++;
            }
        } else {
            // echo SimpleXLSX::parseError();
            // die;
            toastr_set("error", "Kesalahan parsing file 2");
            redirect("./../pages/master/contact.php");
            exit;
        }
        toastr_set("success", "Sukses input nomor");
        redirect("./../pages/master/contact.php");
    }
}

if (post("group")) {
    if (!empty($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {

        if (is_uploaded_file($_FILES['file']['tmp_name']) === false) {

            throw new \Exception('Error on upload: Invalid file definition');
        }
        // Rename the uploaded file
        $uploadName = $_FILES['file']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));

        $allow = ['xls', 'xlsx'];
        if (in_array($ext, $allow)) {

            if ($ext == "xls") {

                $filename = round(microtime(true)) . mt_rand() . '.xls';
            }

            if ($ext == "xlsx") {

                $filename = round(microtime(true)) . mt_rand() . '.xlsx';
            }
        } else {

            toastr_set("error", "Format xls, xlsx only");
            redirect("./../pages/master/contact.php");
            exit;
        }

        move_uploaded_file($_FILES['file']['tmp_name'], 'excel/' . $filename);

        // Insert it into our tracking along with the original name
        $file = "excel/" . $filename;
    } else {

        $file = null;
    }

    if ($file == null) {
        toastr_set("error", "Format file tidak sesuai");
        redirect("./../pages/master/contact.php");
        exit;
    }

    $categoryContact = post("contact_category");
    $a = 2;
    $b = 1;
    $c = 2;
    $d = 3;
    if ($a && $b && $c && $file) {
        $a = $a - 1;
        $b = $b - 1;
        $c = $c - 1;
        $d = $d - 1;
        if ($xlsx = SimpleXLSX::parse($file)) {
            $i = 0;
            foreach ($xlsx->rows() as $elt) {
                if ($i >= $a) {
                    $name = $elt[$b];
                    $numbers = $elt[$c];
                    $type = "GROUP";
                    $categories = $categoryContact;
                    $user_id = $_SESSION['email'];

                    $count = countDB("contacts", "contact_number", $numbers);
                    if ($count == 0) {
                        $query = mysqli_query($conn, "INSERT INTO contacts(`contact_name`, `contact_number`, `contact_type`, `contact_category`, `user_id`, `created_at`)
                                VALUES('$name', '$numbers', '$type', '$categories', '$user_id', '$timeZone')");
                    }
                }
                $i++;
            }
        } else {
            toastr_set("error", "Kesalahan parsing file 2");
            redirect("./../pages/master/contact.php");
            exit;
        }
        toastr_set("success", "Sukses input nomor");
        redirect("./../pages/master/contact.php");
    }
}

// media
if (post("media")) {
    if (!empty($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {

        if (is_uploaded_file($_FILES['file']['tmp_name']) === false) {

            throw new \Exception('Error on upload: Invalid file definition');
        }
        // Rename the uploaded file
        $uploadName = $_FILES['file']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));

        $allow = ['xls', 'xlsx'];
        if (in_array($ext, $allow)) {

            if ($ext == "xls") {

                $filename = round(microtime(true)) . mt_rand() . '.xls';
            }

            if ($ext == "xlsx") {

                $filename = round(microtime(true)) . mt_rand() . '.xlsx';
            }
        } else {

            toastr_set("error", "Format xls, xlsx only");
            redirect("./../pages/master/media.php");
            exit;
        }

        move_uploaded_file($_FILES['file']['tmp_name'], 'excel/' . $filename);

        // Insert it into our tracking along with the original name
        $file = "excel/" . $filename;
    } else {

        $file = null;
    }

    if ($file == null) {
        toastr_set("error", "Format file tidak sesuai");
        redirect("./../pages/master/media.php");
        exit;
    }

    $a = 2;
    $b = 1;
    $c = 2;
    $d = 3;
    if ($a && $b && $c && $file) {
        echo "if insert 2";
        $a = $a - 1;
        $b = $b - 1;
        $c = $c - 1;
        $d = $d - 1;
        if ($xlsx = SimpleXLSX::parse($file)) {
            $i = 0;
            foreach ($xlsx->rows() as $elt) {
                if ($i >= $a) {
                    $media = $elt[$b];
                    $user_id = $_SESSION['email'];

                    $count = countDB("media", "media", $media);
                    if ($count == 0) {
                        $query = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`)
                                VALUES('$media', '$user_id', '$timeZone')");
                    }
                }
                $i++;
            }
        } else {
            toastr_set("error", "Kesalahan parsing file 2");
            redirect("./../pages/master/media.php");
            exit;
        }
        toastr_set("success", "Sukses input data media");
        redirect("./../pages/master/media.php");
    }
}
