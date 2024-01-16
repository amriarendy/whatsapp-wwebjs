<?php
include_once("./helpers/connection.php");
include_once("./helpers/function.php");

session_destroy();
redirect("login.php");