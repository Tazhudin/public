<?php 
include('views/layauts/headerAdmin.php');  //подключаем шапку
?>
			<div class="col-9 tasks">
				<h2>все задачи</h2>
				<div class="sort">
					<button type="button" class="btn-sm btn-success-primary disabled">сортировать</button>
					<button type="button" class="btn-sm btn-success">по имени</button>
					<button type="button" class="btn-sm btn-success">по email </button>
					<button type="button" class="btn-sm btn-success">по статусу</button>
				</div>
				<div class="">

					<div class='task'>
					<?php				
					include('controller/contrGetTasks.php');//подключаем контроллер возвращающий задачи по 3 и общее кол-во записей в бд 	
							    while(($row = $Tasks->fetch_assoc()) != FALSE)
									{
								        $id = $row['id_task']; 
								        echo "<form action='controller/contrGhangeTask.php' method='GET'>";
								        echo "<input type='hidden' name='id' value='".$id."'>";  // записываем id каждой задачи, чтоб найти его при клике на изменить
										echo "<h6t>".$row['name_author']."</h6>";
										echo "<h6".$row['email_author']."</h6>";
										echo "	<pt>
													<h6><b>".$row['title_author']."</b></h6>"
													.$row['text_task']."
												</p>";
										
										if($row['admin_ghange']) 			// проверяем изменение админом
											{
												echo "<p id='AdminEdit>Отредактировано администратором</p>";
											}
										if($row['status']) 			// проверяем статус проверки админом
											{
												echo "<p id='AdminCheck'><img width='20px' src='template/images/galka.png'</img> выполнено</p>";
											}
										echo "<a href=''><input type='submit' name='' value='изменить''></a>";
										echo "</form>";
										echo "<hr>";
										

											
			  						}

			  		?>
			  			<ul class="pagination">
					<?php
									$pages= $taskCount / $values['count'];
			  						for ($i=0; $i < $pages; $i++) { 
			  							$page = $i + 1;  
			  							echo "<li class='page-item'><a class='page-link' href='?page=". $page ."''>". $page ."</a></li>";
			  						}
			  		?>
						</ul>

					</div>


			</div>
		</div>
	</div>	

<?php 
include('views/layauts/footer.php'); //подключаем футер
?>

</body>
</html>	


	