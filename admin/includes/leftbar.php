	<nav class="ts-sidebar">
			<ul class="ts-sidebar-menu">
		<?php
		if ($_SESSION['alogin']=='ADMIN')
		{
		?>	
				<li class="ts-label">Main</li>
				<li><a href="../PRIVAT661309945PRIX656585916/asist00.php"><i class="fa fa-dashboard"></i> Asist</a></li>

				<li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			
			<li><a href="userlist.php"><i class="fa fa-users"></i> Lista Alumnos</a>
			</li>
			<li><a href="profile.php"><i class="fa fa-user"></i> &nbsp;Perfil</a>
			</li>


			<li><a href="admin_cromos.php"><i class="fa fa-envelope"></i> &nbsp;Retos/Cromos</a>
			</li>
			<li><a href="crear_reto.php"><i class="fa fa-envelope"></i> &nbsp;Crear Reto</a>
			</li>
			<li><a href="crear_juicio.php"><i class="fa fa-envelope"></i> &nbsp;Crear Juicio</a>
			</li>
			<li><a href="crear_fastest.php"><i class="fa fa-envelope"></i> &nbsp;Crear Test Rápido</a>
			</li>
			<li><a href="admin_niveles.php"><i class="fa fa-envelope"></i> &nbsp;Niveles</a>
			</li>
			<li><a href="conf_generales.php"><i class="fa fa-envelope"></i> &nbsp;Conf generales</a>
			</li>
			<li><a href="feedback.php"><i class="fa fa-envelope"></i> &nbsp;Feedback</a>
			</li>
			<li><a href="notification.php"><i class="fa fa-bell"></i> &nbsp;Notificaciones <sup style="color:red">*</sup></a>
			</li>
			<li><a href="deleteduser.php"><i class="fa fa-user-times"></i> &nbsp;Alumnos Borrados</a>
			</li>
			<li><a href="download.php"><i class="fa fa-download"></i> &nbsp;Descargar Lista Alumnos</a>
			</li>
		<?php
		}
		else if ($_SESSION['alogin']=='ADMIN_FCT')
		{
		?>	
						<li class="ts-label">FCT</li>
<li><a href="resumenFCT.php"><i class="fa fa-dashboard"></i>RESUMEN Y TABLAS</a></li>
<?php
	$aCiclos = ejecutarQuery($dbh,"SELECT * FROM FCT_CICLOS");
	foreach ($aCiclos as $ciclo) 
	{
		echo '<li><a href="controlFCT.php?idCiclo='.$ciclo['ID'].'"><i class="fa fa-users"></i>'.$ciclo['INFO'].'</a></li>';
	}
?>
		<?php
		}
		?>	
			</ul>
			<p class="text-center" style="color:#ffffff; margin-top: 100px;">© Gilbert</p>
		</nav>

		