<?php
include_once("connection.php");
include_once("function.php");

$count = 0;
$now = strtotime(date("Y-m-d H:i:s"));
$chunk = getSingleValDB("setting", "id", "1", "limited");
// $query = mysqli_query($conn, "SELECT * FROM messages WHERE message_status='waiting' ORDER BY id ASC LIMIT $chunk");
$query = mysqli_query(
    $conn,
    "SELECT m.id AS id_msg, 
            m.message, 
            m.message_categories, 
            m.message_media, 
            m.message_status, 
            m.send_to, 
            m.message_schedule, 
            m.user_id,
            u.id AS id_user,
            u.email,u.api_key 
    FROM messages AS m 
    INNER JOIN users AS u 
    ON m.user_id = u.email 
    WHERE message_status='waiting' 
    ORDER BY id_msg 
    ASC LIMIT $chunk"
);
$i = 0;
while ($data = $query->fetch_assoc()) {
    $schedule = strtotime($data['message_schedule']);
    $key = $data['api_key'];
    if ($schedule < $now) {
        $number = $data['send_to'];
        $message = utf8_decode($data['message']);
        $messageCategories = $data['message_categories'];
        if ($data['message_media'] == null) {
            if ($messageCategories === 'send-broadcast' || $messageCategories === 'api-send-schedule' || $messageCategories === 'api-send-message' || $messageCategories === 'api-send-media' || $messageCategories === 'api-send-group') {
                $sendMSG = sendMSG($number, $message, $key);
                if ($sendMSG['status'] == 200) {
                    $i++;
                    $this_id = $data['id_msg'];
                    $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'success' WHERE id='$this_id'");
                } elseif ($sendMSG['status'] == 500) {
                    $i++;
                    $this_id = $data['id_msg'];
                    $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'waiting' WHERE id='$this_id'");
                } else {
                    $i++;
                    $this_id = $data['id_msg'];
                    $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'failed' WHERE id='$this_id'");
                }
            }
            if ($messageCategories === 'send-group' || $messageCategories === 'api-send-group') {
                $sendGroup = sendGroup($number, $message);
                if ($sendGroup['status'] == 200) {
                    $i++;
                    $this_id = $data['id_msg'];
                    $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'success' WHERE id='$this_id'");
                } elseif ($sendGroup['status'] == 500) {
                    $i++;
                    $this_id = $data['id_msg'];
                    $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'failed' WHERE id='$this_id'");
                }
            }
        } else {
            $number = $data['send_to'];
            $message = utf8_decode($data['message']);
            $media = utf8_decode($data['message_media']);
            $sendMedia = sendMedia($number, $message, $media, $key);
            if ($sendMedia['status'] == 200) {
                $i++;
                $this_id = $data['id_msg'];
                $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'success' WHERE id='$this_id'");
            } elseif ($sendMedia['status'] == 500) {
                $i++;
                $this_id = $data['id_msg'];
                $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'waiting' WHERE id='$this_id'");
            } else {
                $i++;
                $this_id = $data['id_msg'];
                $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'failed' WHERE id='$this_id'");
            }
        }
        sleep(1);
    }
}
echo 'Success Send to ' . $i . ' Numbers';
