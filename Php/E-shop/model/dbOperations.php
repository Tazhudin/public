<?php
		$host = 'v96341up.beget.tech'; 													// адрес сервера 
		$database = 'v96341up_tasksb'; 											// имя базы данных
		$user = 'v96341up_tasksb'; 														// имя пользователя
		$password = '12345678';															// пароль
		$connect = mysqli_connect("$host", "$user", "$password", "$database") 	// подключаемся к серверу
		    or die("Ошибка " . mysqli_error($connect));
		$connect->query("SET NAMES 'utf8' ");



  function  addTask ($name, $email, $title, $text, $connect) {
		$add = $connect->query("
			INSERT INTO tasks SET
			title_task = '$title',
			name_author = '$name', 
			email_author = '$email', 
			text_task = '$text'
				");

		return $add;
	}

 function getAllTasks ($connect)
{
		$allTasks= $connect->query("SELECT * FROM tasks ");
		return $allTasks;
}


 function getTasks ($from, $count, $connect)
{
		$Tasks= $connect->query("SELECT * FROM tasks WHERE id_task > 0 LIMIT $from,$count");
		return $Tasks;
}


 function getTask ($id, $connect)
{
		$Task= $connect->query("SELECT * FROM tasks WHERE id_task= '$id'");
		return $Task;
}


function isAdmin ($login, $pass, $connect)
	{
	 	$query ="
	 	SELECT id  
	 	FROM admins 
	 	WHERE login = '$login' 
	 	AND password = '$pass'";

		$result = mysqli_query($connect, $query) 
		or die("Ошибка " . mysqli_error($connect)); 
		
		$res = $result->num_rows;
		return $res;


	}

 function updateTask ($id, $name, $title, $email, $status, $text, $connect)
{
			$res = $connect->query(" 
			UPDATE 
				tasks 
				SET 
				title_task = '$title',
				name_author = '$name', 
				email_author = '$email', 
				text_task = '$text',
				status = '$status'  
			WHERE
				id_task = '$id'
			");
			return $res;

}
