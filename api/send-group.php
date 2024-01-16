<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");

# data requset from raw
$data = json_decode(file_get_contents('php://input'), true);
$id_group = $data['id_group'];
$message = $data['message'];
$key = $data['api_key'];
$date = $data['date'];
$time = $data['time'];
header('Content-Type: application/json');

if (!isset($id_group) && !isset($message) && !isset($key) && !isset($url_file) && !isset($date) && !isset($time)) {
    $ret['status'] = 422;
    $ret['msg'] = "Unprocessable Entity: Wrong parameters!";
    echo json_encode($ret, true);
    exit;
}

$check = mysqli_query($conn, "SELECT api_key, email FROM users WHERE api_key = '$key'");
$row = mysqli_fetch_assoc($check);
$email = $row['email'];
if ($check->num_rows < 1) {
    $ret['status'] = 401;
    $ret['msg'] = "Unauthorized: Invalid API KEY!";
    echo json_encode($ret, true);
    exit;
}

// $res = sendGroup($id_group, $message);
// if ($res['status'] == 200) {
if ($id_group && $key) {
    if ($id_group) {
        $categories = "api-send-group";
        $status = "waiting";
        $schedule = date("Y-m-d H:i:s", strtotime($date . " " . $time));
        $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
        VALUES('$message', '$id_group', '$categories', NULL, '$schedule', '$status', '$email', '$timeZone')");
    }
    $ret['status'] = 200;
    $ret['msg'] = "Success: Sent and created message!";
    echo json_encode($ret, true);
    exit;
} else {
    $ret['status'] = 500;
    $ret['msg'] = "Internal Server Error: Something went wrong!";
    echo json_encode($ret, true);
    exit;
}
