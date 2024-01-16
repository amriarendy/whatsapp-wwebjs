<?php
include_once("connection.php");
include_once("function.php");

$count = 0;
$now = strtotime(date("Y-m-d H:i:s"));
$chunk = 100;
$query = mysqli_query($conn, "SELECT * FROM messages WHERE message_status='waiting' ORDER BY id ASC LIMIT 50");
$i = 0;
while ($data = $query->fetch_assoc()) {
    $schedule = strtotime($data['message_schedule']);
    if ($schedule < $now) {
        $number = $data['send_to'];
        $message = utf8_decode($data['message']);
        $messageCategories = $data['message_categories'];
        if ($data['message_media'] == null) {
            if ($messageCategories == 'send-broadcast') {
                $sendMSG = sendMSG($number, $message);
                if ($sendMSG['status'] == "true") {
                    $i++;
                    $this_id = $data['id'];
                    $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'success' WHERE id='$this_id'");
                }
            }
            if ($messageCategories == 'send-group') {
                $sendGroup = sendGroup($number, $message);
                if ($sendGroup['status'] == "true") {
                    $i++;
                    $this_id = $data['id'];
                    $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'success' WHERE id='$this_id'");
                }
            }
        } else {
            $number = $data['send_to'];
            $message = utf8_decode($data['message']);
            $media = utf8_decode($data['message_media']);
            $sendMedia = sendMedia($number, $message, $media);
            if ($sendMedia['status'] == "true") {
                $i++;
                $this_id = $data['id'];
                $q3 = mysqli_query($conn, "UPDATE messages SET message_status = 'success' WHERE id='$this_id'");
            }
        }
        sleep(1);
    }
}
echo 'Success Send to ' . $i . ' Numbers';