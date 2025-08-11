<?php 
//подключаем шапку
include('views/layauts/header.php');              
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
					//подключаем контроллер возвращающий задачи по 3 и общее кол-во записей в бд 				
					include('controller/contrGetTasks.php');							
								$num = 0;  //
							    while(($row = $Tasks->fetch_assoc()) != FALSE)
									{						       
										echo "<h6>".$row['name_author']."</h6>";
										echo "<h6>".$row['email_author']."</h6>";
										echo "<p class='tasktext'><h6><b>".$row['title_author']."</b></h6>".$row['text_task']."</p>";
										if($row['admin_ghange']) 			// проверяем изменение админом
											{
												echo "<p id='AdminEdit'>Отредактировано администратором</p>";
											}
										if($row['status']) 			// проверяем статус проверки админом
											{
												echo "<p id='AdminCheck'><img width='20px' src='template/images/galka.png'</img> выполнено</p>";
											}
										echo "<hr>";										
			  						}
					?>
						<ul class="pagination">
					<?php
									$pages= $taskCount / $values['count'];  // зная общее кол-во записей и по сколько их выводить получаем кол_во страниц
			  						for ($i=0; $i < $pages; $i++) { 
			  							$page = $i + 1;  
			  							echo "<li class='page-item'><a class='page-link' href='?page=". $page ."''>". $page ."</a></li>"; //
			  						}
			  		?>
						</ul> 
						<div id="addButton"> 
							<a href="views/addTask.php" >
								<input type="button"  name=""value="добавить задание">							
							</a>
						</div>
					</div>
			</div>
		</div>
	</div>	

<?php 
include('views/layauts/footer.php'); //подключаем футер
?>
</body>
</html>