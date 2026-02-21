<?php 
// server, user, password, database, port
$con = mysqli_connect("localhost", "root", "", "project_library", 3307);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
