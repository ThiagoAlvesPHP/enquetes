<?php
require 'class.php';
$sql = new enquete();

if (!empty($_POST['id'])) {
	$contador = $sql->getContador($_POST['id']);

	echo $contador;
}