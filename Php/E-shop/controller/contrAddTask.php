<?php
	$name = $_POST ['name'];
	$email = $_POST['email'];
	$title = $_POST['title'];
	$text = $_POST['text'];

	require_once ('../model/dbOperations.php'); // подключаем файл модели с со всеми опрациями с базой данных
	$add = addTask ( $name, $email, $title, $text, $connect); // добавление записи
	if ($add) {
			header('Location:../views/successFullAdd.php'); // пернеправляем 
		}
		else{
			echo "Добавить ваше задание в базу данных не удалось !";
		}





