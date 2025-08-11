<?php
	require_once ('../model/dbOperations.php'); // подключаем файл модели с со всеми опрациями с базой данных
	$login=$_POST['login'];	
	$pass=$_POST['pass'];
	

	$res = isAdmin ($login, $pass, $connect); // есть ли админ с заданным логином и поролем в базе
	if ($res) {
		header('Location:../indexAdmin.php');  // пернеправляем  на главную админа
	}
	else{
		echo "администратор с такими данными не зарегистрирован !!!";
	}




?>