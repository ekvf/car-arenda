<?php

$dbhost="localhost";
$dbuser="volkov.d.r";
$dbpassword="2345f";
$database="volkov.d.r";

$conn=mysqli_connect($dbhost, $dbuser,$dbpassword, $database);
if(!$conn){
         die("Connection Failed". mysql_connect_error());
}else {
       "Успех";
} ?>          