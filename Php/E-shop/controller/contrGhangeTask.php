<?php
$idTask = $_GET['id']; 
require_once ('../model/dbOperations.php');
$task = getTask ($idTask, $connect);
$task=$task->fetch_assoc(); // получаем задачу по id

require_once ('../views/ghangeTask.php'); // отоброжаем
