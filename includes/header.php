<?php
require_once("cssutils.php");
?>
<div class="brand clearfix">
<h4 class="pull-left text-white text-uppercase" style="margin:20px 0px 0px 20px"><i class="fa fa-user"></i>&nbsp; <?php echo htmlentities($_SESSION['alogin']);?></h4>
		<span class="menu-btn"><i class="fa fa-bars"></i></span>
		<ul class="ts-profile-nav">
			
			<li class="ts-account">
				<a href="#" style="background-color: #<?php echo getColorDay()[0]?>"><img src="img/rompecabezas.png" class="ts-avatar hidden-side" alt=""> Mi cuenta <i class="fa fa-angle-down hidden-side"></i></a>
				<ul>
					<li><a href="change-password.php">Cambiar contraseña</a></li>
					<li><a href="logout.php">Salir</a></li>
				</ul>
			</li>
		</ul>
	</div>


	<script type="text/javascript">
	
 var cols = document.getElementsByClassName('brand');
  for(i = 0; i < cols.length; i++) {
    cols[i].style.backgroundColor = '#<?php echo getColorDay()[1]?>';
  }
</script>
