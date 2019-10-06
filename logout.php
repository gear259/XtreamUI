<?php
include "functions.php";
session_destroy();

header("Location: ./login.php");
?>