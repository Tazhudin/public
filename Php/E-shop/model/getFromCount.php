<?php
$count = 3; // по сколько выводить
$values = array(
		'count' => $count,
		'from' => ($page-1) * $count // отткуда выводить
		);
return $values;


?>