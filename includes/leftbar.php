<?php
require_once("cssutils.php");
?>
	<nav class="ts-sidebar">
			<ul class="ts-sidebar-menu">
			
			<li class="ts-label">Main</li>
			<li><a href="profile.php"><i class="fa fa-user"></i> &nbsp;Perfil</a>
			</li>
			<li><a href="micromo.php"><i class="fa fa-envelope"></i> &nbsp;Mi cromo</a>
			</li>
			<li><a href="mialbum.php"><i class="fa fa-envelope"></i> &nbsp;Mi Album</a>
			</li>
			<li><a href="abrirsobre.php"><i class="fa fa-envelope"></i> &nbsp;Abrir sobre</a>
			</li>
			<li><a href="ranking.php"><i class="fa fa-envelope"></i> &nbsp;Ranking</a>
			</li>
			<li><a href="mercado.php"><i class="fa fa-envelope"></i> &nbsp;Mercado</a>
			</li>
			<li><a href="mibot.php"><i class="fa fa-envelope"></i> &nbsp;Mi bot</a>
			<li><a href="https://magicomagico.com/SalleZGamE" target=”_blank”><i class="fa fa-envelope"></i> &nbsp;Jugar</a>
			<li><a href="feedback.php"><i class="fa fa-envelope"></i> &nbsp;Feedback</a>
			</li>
			<li><a href="notification.php"><i class="fa fa-bell"></i> &nbsp;Notificaciones<sup style="color:red">*</sup></a>
			</li>
			<li><a href="messages.php"><i class="fa fa-envelope"></i> &nbsp;Mensajes</a>
			</li>
			</ul>
			<p class="text-center" style="color:#ffffff; margin-top: 100px;">© Gilbert</p>
		</nav>

		<script type="text/javascript">
	
 var cols = document.getElementsByClassName('ts-sidebar');
  for(i = 0; i < cols.length; i++) {
    cols[i].style.backgroundColor = '#<?php echo getColorDay()[0]?>';
  }
</script>