<?php
if (!esUsernameAdministrador($dbh,$_SESSION['alogin']))
{

require_once("cssutils.php");
?>

	<nav class="ts-sidebar">
			<ul class="ts-sidebar-menu">
			
			<li class="ts-label">Menú</li>
			<?php
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Perfil"))
			{
			?>
				<li><a href="profile.php"><i class="fa fa-user"></i> &nbsp;Mi Perfil</a>
				</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Mi Clan"))
			{
			?>
				<li><a href="clan.php"><i class="fa fa-users"></i> &nbsp;Mi Clan</a>
				</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Mi cromo"))
			{
			?>
			<li><a href="micromo.php"><i class="fa fa-plus-square"></i> &nbsp;Mi Cromo</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Mi Album"))
			{
			?>
			<li><a href="mialbum.php"><i class="fa fa-book"></i> &nbsp;Mi Album</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Abrir sobre"))
			{
			?>
			<li><a href="abrirsobre.php"><i class="fa fa-envelope"></i> &nbsp;Abrir sobre</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Ranking"))
			{
			?>
			<li><a href="ranking.php"><i class="fa fa-globe"></i> &nbsp;Mi Cartilla</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Mercado"))
			{
			?>
			<li><a href="mimercado.php"><i class="fa fa-hand-o-right"></i> &nbsp;Mercado</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Mi bot"))
			{
			?>
			<li><a href="mibot.php"><i class="fa fa-male"></i> &nbsp;Mi bot</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Jugar"))
			{
			?>
			<li><a href="https://magicomagico.com/SalleZGamE" target="_blank"><i class="fa fa-gamepad"></i> &nbsp;Jugar</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Feedback"))
			{
			?>			<li><a href="feedback.php"><i class="fa fa-envelope"></i> &nbsp;Feedback</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Notificaciones"))
			{
			?>
			<li><a href="notification.php"><i class="fa fa-bell"></i> &nbsp;Notificaciones<sup style="color:red">*</sup></a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Mensajes"))
			{
			?>
			<li><a href="messages.php"><i class="fa fa-envelope"></i> &nbsp;Mensajes</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Juicio"))
			{
			?>
			<li><a href="vota_juicio.php"><i class="fa fa-balance-scale"></i> &nbsp;Juicio</a>
			</li>
			<?php
			}
			if (opcionMenuOk($dbh,$_SESSION['alogin'],"Mi QR"))
			{
			?>
			<li><a href="miQR.php"><i class="fa fa-th"></i> &nbsp;Mi QR</a>
			</li>
			<?php
			}
			?>
			</ul>
			<p class="text-center" style="color:#ffffff; margin-top: 100px;">© Gilbert</p>
		</nav>

		<script type="text/javascript">
	
 var cols = document.getElementsByClassName('ts-sidebar');
  for(i = 0; i < cols.length; i++) {
    cols[i].style.backgroundColor = '#<?php echo getColorDay()[0]?>';
  }
</script>

<?php
}
?>