
<div class="brand clearfix">
<h4 class="pull-left text-white" style="margin:20px 0px 0px 20px"><i class="fa fa-rocket"></i>&nbsp; TutoZ</h4>
		<span class="menu-btn"><i class="fa fa-bars"></i></span>
		<ul class="ts-profile-nav">
			
			<li class="ts-account">
				<a href="#"><img src="img/rompecabezas.png" class="ts-avatar hidden-side" alt=""> Mi cuenta <i class="fa fa-angle-down hidden-side"></i></a>
				<ul>
					<?php
		if ($_SESSION['alogin']=='ADMIN')
		{
		?>	
		<li><a href="change-password.php">Cambiar contraseña</a></li>
		<?php
		}
		?>
					<li><a href="logout.php">Salir</a></li>
				</ul>
			</li>
		</ul>
	</div>
