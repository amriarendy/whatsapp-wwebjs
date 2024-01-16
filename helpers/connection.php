<?php
$host = "localhost";
$username = "root";
$password = "";
$db = "wa_blast";
error_reporting(0);
$conn = mysqli_connect($host, $username, $password, $db) or die("Field connection database");

$base_url = "http://localhost/wa-api/";
date_default_timezone_set('Asia/Jakarta');
$timeZone = date("Y-m-d H:i:s");
