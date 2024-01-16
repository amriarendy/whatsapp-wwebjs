<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");

# data requset from raw
$data = json_decode(file_get_contents('php://input'), true);
$number = $data['send_to'];
$caption = $data['message'];
$key = $data['api_key'];
$url_file = $data['url_media'];
header('Content-Type: application/json');

if (!isset($number) && !isset($caption) && !isset($key)) {
    $ret['status'] = false;
    $ret['msg'] = "Wrong parameters!";
    echo json_encode($ret, true);
    exit;
}

$a = explode('/', $url_file);
$filename = $a[count($a) - 1];
$a2 = explode('.', $filename);
$namefile = $a2[count($a2) - 2];
$ext = $a2[count($a2) - 1];

if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'pdf') {
    $ret['status'] = false;
    $ret['msg'] = "Hanya support jpg, jpeg, png dan pdf";
    echo json_encode($ret, true);
    exit;
}

$check = mysqli_query($conn, "SELECT api_key, email FROM users WHERE api_key = '$key'");
$row = mysqli_fetch_assoc($check);
$sender = $row['email'];
if ($check->num_rows < 1) {
    $ret['status'] = false;
    $ret['msg'] = "Wrong or not found API KEY!";
    echo json_encode($ret, true);
    exit;
}

$res = sendMedia($number, $caption, $url_file);
if ($res['status'] == true) {
    $ret['status'] = true;
    $ret['msg'] = "Success send message media!";
    echo json_encode($ret, true);
    if ($number) {
        $categories = "api-send-message-media";
        $status = "success";
        $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `user_id`, `created_at`) 
        VALUES ('$caption', '$categories', '$url_file', '$status', '$number', '$sender', '$timeZone')");
    }
    exit;
} else {
    $ret['status'] = false;
    $ret['msg'] = $res['message'];
    echo json_encode($ret, true);
    exit;
}
