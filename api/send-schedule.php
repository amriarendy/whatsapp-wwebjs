<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");

# data requset from raw
$data = json_decode(file_get_contents('php://input'), true);
$key = $data['api_key'];
$number = $data['number'];
$message = $data['message'];
$url_file = $data['media'];
$date = $data['date'];
$time = $data['time'];
header('Content-Type: application/json');

if (!isset($number) && !isset($message) && !isset($key) && !isset($url_file) && !isset($date) && !isset($time)) {
    $ret['status'] = 422;
    $ret['msg'] = "Unprocessable Entity: Wrong parameters!";
    echo json_encode($ret, true);
    exit;
}

if ($url_file) {
    $a = explode('/', $url_file);
    $filename = $a[count($a) - 1];
    $a2 = explode('.', $filename);
    $namefile = $a2[count($a2) - 2];
    $ext = $a2[count($a2) - 1];
    if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'pdf') {
        $ret['status'] = 422;
        $ret['msg'] = "Unprocessable Entity: Support Only Ext: jpg, jpeg, png dan pdf";
        echo json_encode($ret, true);
        exit;
    }
} else {
    $url_file = NULL;
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

// $res = sendMedia($number, $message, $url_file);
// if ($res['status'] == true) {
if ($number && $message && $key && $email) {
    if ($number) {
        $categories = "api-send-schedule";
        $status = "waiting";
        $schedule = date("Y-m-d H:i:s", strtotime($date . " " . $time));
        $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
        VALUES('$message', '$number', '$categories', '$url_file', '$schedule', '$status', '$email', '$timeZone')");
        if ($query) {
            $ret['status'] = 200;
            $ret['msg'] = "Success: Created message schedule!";
        } else {
            $ret['status'] = 500;
            $ret['msg'] = "Internal Server Error: Something went wrong creat data!";
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
// }