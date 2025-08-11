<?php
	// получаем все данные
	$id = $_POST ['id'];
	$name = $_POST ['name'];
	$email = $_POST['email'];
	$title = $_POST['title'];
	$text = $_POST['text'];
	$status = $_POST['status'];
	if($status)
		$status= 1;
	else
		$status = 0;
require_once ('../model/dbOperations.php');

	if (updateTask ($id, $name, $title,$email, $status, $text, $connect)) {
			header('Location:../views/successFullGhange.php'); // обновляем , перенаправляем
		}
		else
			echo "Добавить ваше задание в базу данных не удалось !";


	






