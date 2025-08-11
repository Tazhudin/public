<?php 
include('layauts/header.php');
?>
			<div class="col-9 tasks">
				<h2>вход администратора</h2>
				<form action="../controller/contrAdmin.php" method="POST" >
					<h6>имя</h6>
					<input type="text" name="login">
					<h6>пороль</h6>
					<input type="password" required name="pass"><br><br>
					<input type="submit" name="" required value="ввойти">
				</form>
				


			</div>
		</div>
	</div>	

<?php 
include('layauts/footer.php');
?>
</body>
</html>