<?php 
include('layauts/headerAdmin.php');
?>
	<div class="container-fluid ">
		<div class="row content">
			<div class="col-3 sidebur">
				<ul class="nav flex-column">
				  <li class="nav-item">
				    <a class="nav-link active" href="#">Active</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" href="#">Link</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" href="#">Link</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link disabled" href="#">Disabled</a>
				  </li>
				</ul>
			</div>
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
					include('../controller/contrGetTasks.php');							
								echo "$num_rows";
								$taskCount = $Tasks->num_rows;
								


								$num = 0;
							    while(($row = $Tasks->fetch_assoc()) != FALSE)
									{

										$num++;
								        $id = $row['id_task'];
								        echo "<form action='../controller/contrGhangeTask.php' method='GET'>";
								        echo "<input type='hidden' name='id' value='".$id."'>";
										echo "<h6>".$row['name_author']."</h6>";
										echo "<h6>".$row['email_author']."</h6>";
										echo "<p><h6><b>".$row['title_author']."</b></h6>".$row['text_task']."</p>";
													if($row['status'])
												echo "<input  value='отредактировано администратором '><br>";
										echo "<a href=''><input type='submit' name='' value='изменить''></a>";
										echo "</form>";
										echo "<hr>";

											
			  						}

			  		?>

					</div>


			</div>
		</div>
	</div>	

<?php 
include('layauts/footer.php');
?>
</body>
</html>