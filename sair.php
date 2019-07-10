<?php
session_start();

if (isset($_SESSION['lg']) && !empty($_SESSION['lg'])) {
	unset($_SESSION['lg']);
	header('Location: login.php');
}