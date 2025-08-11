<?php
	if (isset($_GET['page']))
	{
		$page = $_GET['page'];
	}
	else
		$page = 1;	


	


	require_once ('model/getFromCount.php');
	require_once ('model/dbOperations.php');
	$Tasks = getTasks ($values['from'],$values['count'], $sortBy, $connect); //извлекаем записи с "$values['from']" по "$values['count']"
	$taskCount = (getAllTasks ($connect))->num_rows; // общее количество записей в базе

?>