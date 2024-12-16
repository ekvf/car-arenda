<?php
require_once('db.php');
$login=$_POST['login'];
$pass=$_POST['pass'];
$repeatpass=$_POST['repeatpass'];
$email=$_POST['email'];


$sql="INSERT INTO `