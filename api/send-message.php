<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");

# data requset from raw
$data = json_decode(file_get_contents('php://input'), true);
$number = $data['number'];
$message = $data['message'];
$key = $data['api_key'];
header('Content-Type: application/json');

if (!isset($number) && !isset($message) && !isset($key)) {
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

// $res = sendMSG($number, $message, $key);
// if ($res['status'] == 200) {
if ($number && $message && $key) {
    if ($number) {
        $categories = "api-send-message";
        $status = "waiting";
        $schedule = date("Y-m-d H:i:s");
        $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `message_schedule`, `send_to`, `user_id`, `created_at`) 
        VALUES ('$message', '$categories', NULL, '$status', '$schedule', '$number', '$email', '$timeZone')");
        if ($query) {
            $ret['status'] = 200;
            $ret['msg'] = "Success: Sent and created message schedule!";
        } else {
            $ret['status'] = 500;
            $ret['msg'] = "Internal Server Error: Something went wrong!";
        }
        echo json_encode($ret, true);
        exit;
    }
} else {
    $ret['status'] = 500;
    $ret['msg'] = "Internal Server Error: Something went wrong!";
    echo json_encode($ret, true);
    exit;
}
