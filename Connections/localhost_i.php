<?php
# FileName="WADYN_MYSQLI_CONN.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_localhost_i = "localhost";
$database_localhost_i = "klarobg_jpop";
$username_localhost_i = "klarobg_jpop";
$password_localhost_i = "}o&T;z.WKWnl";
@session_start();

$localhost_i = new mysqli($hostname_localhost_i, $username_localhost_i, $password_localhost_i, $database_localhost_i);
$localhost_i->set_charset('utf8');
?>