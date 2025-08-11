<?php 
include('layauts/headerAdmin.php');
?>
			<div class="col-9 tasks ">
				<form method="post" action="../controller/contrUpdateTask.php">
					<input type='hidden' name='id' value='<?php echo $idTask ?> '>
					<input type='hidden' class='edited' >
					<h6>имя:</h6>
					<input type="text" name="name" required class="edit" value='<?php echo $task['name_author']  ?>'><br>
					<h6>E-mail:</h6>
					<input type="email" name="email" required class="edit" value='<?php echo $task['email_author']  ?>'><br>
					<h6>Заголовок задания:</h6>
					<input type="text" name="title" required class="edit" value='<?php echo $task['title_task']  ?>'><br>
					<h6>Задача:</h6>
					<textarea cols="50" rows="10" name="text" required class="edit"><?php echo $task['text_task']  ?> </textarea><br>
					<label><b> выполнено:</b>
						<input type="checkbox" name="status" >
					</label><br>
					<input type="submit" name="" value="сохранить изменения">
				</form> 
				
			</div>
		</div>
	</div>	


<script>
	var item1 = $(".edit");
	
 $(item1).on('click', function()
 	{
     $(".edited").attr('value', '1' );
    });
</script>
<?php 
include('layauts/footer.php');
?>

</body>
</html>