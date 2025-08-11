<?php 
include('layauts/headerAdmin.php');
?>
			<div class="col-9 tasks ">
				<form method="post" action="../controller/contrUpdateTask.php">
					<input type='hidden' name='id' value='<?php echo $idTask ?> '>
					<h6>имя:</h6>
					<input type="text" name="name" required placeholder="ваше имя" value='<?php echo $task['name_author']  ?>'><br>
					<h6>E-mail:</h6>
					<input type="email" name="email" required placeholder="email" value='<?php echo $task['email_author']  ?>'><br>
					<h6>Заголовок задания:</h6>
					<input type="text" name="title" required placeholder="название задачи" value='<?php echo $task['title_task']  ?>'><br>
					<h6>Задача:</h6>
					<textarea cols="50" rows="10" name="text" required><?php echo $task['text_task']  ?> </textarea><br>
					<label><b> выполнено:</b>
						<input type="checkbox" name="status" >
					</label><br>
					<input type="submit" name="" value="сохранить изменения">
				</form> 
				
			</div>
		</div>
	</div>	

<?php 
include('layauts/footer.php');
?>
</body>
</html>