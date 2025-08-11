<?php 
include('layauts/header.php');
?>
			<div class="col-9 tasks ">
				<form method="post"  action="../controller/contrAddTask.php">
					<h6> имя:</h6>
					<input type="text" name="name" required placeholder="ваше имя"><br>
					<h6>E-mail:</h6>
					<input type="email" name="email" required placeholder="email"><br>
					<h6>Заголовок задания:</h6>
					<input type="text" name="title" required placeholder="название задачи"><br>
					<h6>Задача:</h6>
					<textarea cols="50" rows="10" name="text" required> </textarea><br>
					<input type="submit" name="" value="добавить">
				</form>
			
			</div>
		</div>
	</div>	

<?php 
include('layauts/footer.php');
?>
</body>
</html>