<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");

# data requset from raw
$data = json_decode(file_get_contents('php://input'), true);
$number = $data['send_to'];
$message = $data['message'];
$key = $data['api_key'];
header('Content-Type: application/json');

if (!isset($number) && !isset($message) && !isset($key)) {
    $ret['status'] = false;
    $ret['msg'] = "Wrong parameters!";
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

$res = sendMSG($number, $message);
if ($res['status'] == true) {
    $ret['status'] = true;
    $ret['msg'] = "Success send message!";
    echo json_encode($ret, true);
    if ($number) {
        $categories = "api-send-message";
        $status = "success";
        $query = mysqli_query($conn, "INSERT INTO message(`message`, `message_categories`, `message_status`, `send_to`, `user_id`, `created_at`) 
        VALUES ('$message', '$categories', '$status', '$number', '$sender', '$timeZone')");
    }
    exit;
} else {
    $ret['status'] = false;
    $ret['msg'] = $res['message'];
    echo json_encode($ret, true);
    exit;
}
