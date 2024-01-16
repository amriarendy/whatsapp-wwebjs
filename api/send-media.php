<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");

# data requset from raw
$data = json_decode(file_get_contents('php://input'), true);
$number = $data['number'];
$message = $data['message'];
$key = $data['api_key'];
$url_file = $data['media'];
header('Content-Type: application/json');

if (!isset($number) && !isset($message) && !isset($url_file) && !isset($key)) {
    $ret['status'] = 422;
    $ret['msg'] = "Unprocessable Entity: Wrong parameters!";
    echo json_encode($ret, true);
    exit;
}

$a = explode('/', $url_file);
$filename = $a[count($a) - 1];
$a2 = explode('.', $filename);
$namefile = $a2[count($a2) - 2];
$ext = $a2[count($a2) - 1];

if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'pdf') {
    $ret['status'] = 422;
    $ret['msg'] = "Unprocessable Entity: Support only ext: jpg, jpeg, png dan pdf";
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

// $res = sendMedia($number, $message, $url_file, $key);
// if ($res['status'] == 200) {
if ($number && $message && $url_file && $key) {
    if ($number) {
        $categories = "api-send-media";
        $status = "waiting";
        $schedule = date("Y-m-d H:i:s");
        $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
        VALUES ('$message', '$categories', '$url_file', '$status', '$number', '$schedule', '$email', '$timeZone')");
        if ($query) {
            $ret['status'] = 200;
            $ret['msg'] = "Success: Sent and created message media!";
        } else {
            $ret['status'] = 500;
            $ret['msg'] = "Internal Server Error: Something went wrong create data!";
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
