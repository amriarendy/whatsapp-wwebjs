<?php
include_once("connection.php");
session_start();

function get($param)
{
    global $conn;
    $d = isset($_GET[$param]) ? $_GET[$param] : null;
    $d = mysqli_real_escape_string($conn, $d);
    $d = filter_var($d, FILTER_SANITIZE_STRING);
    return $d;
}

function post($param)
{
    global $conn;
    $d = isset($_POST[$param]) ? $_POST[$param] : null;
    $d = mysqli_real_escape_string($conn, $d);
    $d = filter_var($d, FILTER_SANITIZE_STRING);
    return $d;
}

function login($email, $password)
{
    global $conn;
    $pwsh = sha1($password);
    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pwsh'");

    if (mysqli_num_rows($q)) {
        $_SESSION['login'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['level'] = getSingleValDB("users", "email", $email, "level");
        return true;
    } else {
        return false;
    }
}

function cekSession()
{
    $login = isset($_SESSION['login']) ? $_SESSION['login'] : null;
    if ($login == true) {
        return 1;
    } else {
        return 0;
    }
}

function getSingleValDB($table, $where, $param, $target)
{
    global $conn;
    $q = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$where`='$param'");
    $row = mysqli_fetch_assoc($q);
    return $row[$target];
}

function countDB($table, $where = null, $param = null)
{
    global $conn;
    if ($where == null && $param == null) {
        $q = mysqli_query($conn, "SELECT * FROM `$table`");
    } else {
        $q = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$where`='$param'");
    }
    $row = mysqli_num_rows($q);
    return $row;
}

// function countPresentase()
// {
//     $a = countDB("pesan", "status" . "TERKIRIM");
//     $b = countDB("pesan");
//     if ($a > 0) {
//         return (countDB("pesan", "status", "TERKIRIM") / countDB("pesan"));
//     } else {
//         return 0;
//     }
// }

function getCategoryNumber($targetCategories)
{
    global $conn;
    $q = mysqli_query($conn, "SELECT * FROM `contacts` WHERE `contact_category`='$targetCategories'");
    $arr = [];

    while ($row = mysqli_fetch_assoc($q)) {
        array_push($arr, $row['contact_number']);
    }
    return $arr;
}

function getCategoryGroupID($targetCategories)
{
    global $conn;
    $q = mysqli_query($conn, "SELECT * FROM `contacts` WHERE `contact_category`='$targetCategories' AND `contact_type`='GROUP'");
    $arr = [];

    while ($row = mysqli_fetch_assoc($q)) {
        array_push($arr, $row['contact_number']);
    }
    return $arr;
}

function getAllNumberandmessage()
{
    global $conn;
    $q = mysqli_query($conn, "SELECT * FROM `contacts`");
    $arr = [];
    while ($row = mysqli_fetch_assoc($q)) {
        array_push($arr, $row['contact_number']);
    }
    return $arr;
}

function getLastID($table)
{
    global $conn;
    $q = mysqli_query($conn, "SELECT * FROM `$table` ORDER BY id DESC LIMIT 1");
    $row = mysqli_fetch_assoc($q);
    return $row['id'];
}

function url_wa()
{
    global $base_url;
    $url = getSingleValDB("setting", "id", "1", "url_gateway");
    return $url;
}

function token()
{
    return getSingleValDB("setting", "id", "1", "token");
}

function redirect($target)
{
    echo '
    <script>
        window.location = "' . $target . '";
    </script>
    ';
    exit;
}

function toastr_set($status, $msg)
{
    $_SESSION['toastr'] = true;
    $_SESSION['toastr_status'] = $status;
    $_SESSION['toastr_msg'] = $msg;
}

function toastr_show()
{
    $t = isset($_SESSION['toastr']) ? $_SESSION['toastr'] : null;
    $t_s = isset($_SESSION['toastr_status']) ? $_SESSION['toastr_status'] : null;
    $t_m = isset($_SESSION['toastr_msg']) ? $_SESSION['toastr_msg'] : null;
    if ($t == true) {
        if ($t_s == "success") {
            echo "
                toastr.success('Success', '" . $t_m . "');
            ";
        }

        if ($t_s == "error") {
            echo "
            toastr.error('Errors', '" . $t_m . "');
            ";
        }

        unset($_SESSION['toastr']);
        unset($_SESSION['toastr_status']);
        unset($_SESSION['toastr_msg']);
    }
}

function sendMSG($number, $msg)
{
    $url = url_wa() . 'send-message';
    $data = [
        "token" => token(),
        "number" => $number,
        "message" => $msg
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10000);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function sendMedia($number, $caption, $file)
{
    $url = url_wa() . 'send-media';
    $data = [
        "token" => token(),
        'number' => $number,
        'caption' => $caption,
        'file' => $file,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function sendGroup($groupID, $message)
{
    $url = url_wa() . 'send-group-message';
    $data = [
        "token" => token(),
        'id' => $groupID,
        'message' => $message,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function cekStatusWA()
{
    $url = url_wa() . "/status";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    // curl_setopt($curl, CURLOPT_POST, 1);
    // curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    $response = curl_exec($curl);
    return json_decode($response, true);
}

function updateStatusMSG($id, $a)
{
    global $conn;
    $q = mysqli_query($conn, "UPDATE pesan SET status='$a' WHERE id='$id'");
}

function base64upload($base64_string, $output_file)
{
    $file = fopen($output_file, "wb");

    $data = explode(',', $base64_string);

    fwrite($file, base64_decode($data[1]));
    fclose($file);

    return $output_file;
}

function phoneToStandard($nomor)
{
    $nomor = explode("@", $nomor)[0];
    $nomor = substr($nomor, 2);
    $nomor = "0" . $nomor;

    return $nomor;
}

function sendApiUrl()
{
    global $base_url;
    return $base_url . "api/send.php?key=" . getSingleValDB("setting", "id", "1", "token");
}

function syncMSG()
{
    global $conn;
    $url = url_wa() . "/getChat";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    $res = json_decode($response, true);
    $final = [];
    $arr_p = [];
    foreach ($res['response'] as $sender) {
        foreach ($sender as $s) {
            $id_pesan = $s['id']['id'];
            if (checkExist("receive_chat", "id_pesan", $id_pesan) == false) {
                if ($s["fromMe"] == true) {
                    $nomor = phoneToStandard($s['to']);
                    $fromme = "1";
                } else {
                    $nomor = phoneToStandard($s['from']);
                    $fromme = "0";
                }
                if (in_array($nomor, $arr_p)) {
                } else {
                    $final[$nomor] = [];
                    array_push($arr_p, $nomor);
                }
                $pesan = $s['body'];
                $tanggal = date("Y-m-d H:i:s", $s['timestamp']);
                $ret = [
                    'id' => $id_pesan,
                    'nomor' => $nomor,
                    'pesan' => $pesan,
                    'fromMe' => $fromme,
                    'tanggal' => $tanggal
                ];

                $nomor_saya = getSingleValDB("pengaturan", "id", "1", "nomor");

                $q = mysqli_query($conn, "INSERT INTO receive_chat(`id_pesan`, `nomor`, `pesan`, `tanggal`, `from_me`, `nomor_saya`)
                                    VALUE('$id_pesan', '$nomor', '$pesan', '$tanggal', '$fromme', '$nomor_saya')");

                array_push($final[$nomor], $ret);
            }
        }
    }
}

function getContact()
{
    global $conn;

    $nomor_saya = getSingleValDB("pengaturan", "id", "1", "nomor");
    $q = mysqli_query($conn, "SELECT DISTINCT nomor, MAX(tanggal) FROM receive_chat WHERE nomor_saya='$nomor_saya' GROUP BY nomor ORDER BY MAX(tanggal) DESC");
    return $q;
}

function csrf_randomhex()
{
    return bin2hex(random_bytes(35));
}

function csrf_create()
{
    if (isset($_SESSION['_token'])) {
        return $_SESSION['_token'];
    }
    $token = csrf_randomhex();
    $_SESSION['_token'] = $token;
    return $token;
}

function csrf_input()
{
    $token = csrf_create();
    return '<input type="hidden" name="_token" class="form-control" value="' . $token . '">';
}

function csrf_validate()
{
    $_SESSION['token_expired'] = time() + 3600;
    if (!isset($_POST["_token"]) || !isset($_SESSION["_token"])) {
        exit("CSRF token not set!");
    }
    if ($_POST["_token"] == $_SESSION["_token"]) {
        if (time() >= $_SESSION["token_expired"]) {
            exit("CSRF token expired, reload the form!");
        }
        unset($_SESSION["_token"]);
        unset($_SESSION["token_expired"]);
    } else {
        exit("Invalid CSRF token!");
    }
}

// function getLastMsg($nomor)
// {
//     global $conn;

//     $nomor_saya = getSingleValDB("pengaturan", "id", "1", "nomor");
//     $q = mysqli_query($conn, "SELECT * FROM receive_chat WHERE nomor='$nomor' AND nomor_saya='$nomor_saya' ORDER BY tanggal DESC LIMIT 1");
//     $row = mysqli_fetch_assoc($q);
//     if (date("Y-m-d", strtotime($row['tanggal'])) == date("Y-m-d")) {
//         $row['tanggal'] = date("H:i", strtotime($row['tanggal']));
//     } else {
//         $row['tanggal'] = date("d M y H:i", strtotime($row['tanggal']));
//     }
//     return $row;
// }

// function checkExist($table, $where, $param)
// {
//     global $conn;
//     $q = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$where`='$param' LIMIT 1");
//     $row = mysqli_num_rows($q);
//     if ($row == 0) {
//         return false;
//     } else {
//         return true;
//     }
// }
