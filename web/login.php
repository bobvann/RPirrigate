<?php
include 'config/config.php';
$db = new DB_CONN();

$user = $_POST['username'];
$pass = $_POST['password'];

$logged = $db->login($user, $pass);

if ($logged>0){
	session_start();
	$_SESSION['RPirrigate_UserID'] = $logged['UserID'];
	header('location: home.php'); 
	die(); 
} else {
	header('location: index.php?wrong'); 
	die(); 
}

?>