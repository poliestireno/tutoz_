 
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
	
	<title>Mercado</title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">

	<style>
	.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
	background: #dd3d36;
	color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
	background: #5cb85c;
	color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>


</head>

<body>
	<div class="brand clearfix">
<h4 class="pull-left text-white text-uppercase" style="margin:20px 0px 0px 20px"><i class="fa fa-user"></i>&nbsp; p@p</h4>
		<span class="menu-btn"><i class="fa fa-bars"></i></span>
		<ul class="ts-profile-nav">
			
			<li class="ts-account">
				<a href="#"><img src="img/rompecabezas.png" class="ts-avatar hidden-side" alt=""> Mi cuenta <i class="fa fa-angle-down hidden-side"></i></a>
				<ul>
					<li><a href="change-password.php">Cambiar contraseña</a></li>
					<li><a href="logout.php">Salir</a></li>
				</ul>
			</li>
		</ul>
	</div>
	<div class="ts-main-content">
		<nav class="ts-sidebar">
			<ul class="ts-sidebar-menu">
			
			<li class="ts-label">Main</li>
			<li><a href="profile.php"><i class="fa fa-user"></i> &nbsp;Profile</a>
			</li>
			<li><a href="micromo.php"><i class="fa fa-envelope"></i> &nbsp;Mi cromo</a>
			</li>
			<li><a href="mialbum.php"><i class="fa fa-envelope"></i> &nbsp;Mi Album</a>
			</li>
			<li><a href="abrirsobre.php"><i class="fa fa-envelope"></i> &nbsp;Abrir sobre</a>
			</li>
			<li><a href="ranking.php"><i class="fa fa-envelope"></i> &nbsp;Ranking</a>
			</li>
			<li><a href="https://magicomagico.com/SalleZGamE" target=”_blank”><i class="fa fa-envelope"></i> &nbsp;Jugar</a>
			<li><a href="feedback.php"><i class="fa fa-envelope"></i> &nbsp;Feedback</a>
			</li>
			<li><a href="notification.php"><i class="fa fa-bell"></i> &nbsp;Notificacion<sup style="color:red">*</sup></a>
			</li>
			<li><a href="messages.php"><i class="fa fa-envelope"></i> &nbsp;Mensajes</a>
			</li>
			</ul>
			<p class="text-center" style="color:#ffffff; margin-top: 100px;">© Gilbert</p>
		</nav>

				<div class="content-wrapper">
<h3>Mercado de mi curso (Ofertas)</h3>
<!--Table-->
            <table class="table table-striped w-auto table-bordered">
               <!--Table head-->
               <thead>
                  <tr>
                     <th>Nombre</th>
                     <th>Cromo</th>
                     <th>Postura(estrellas)</th>
                  </tr>
               </thead>
               <!--Table head-->
               <!--Table body-->
               <tbody>
                  <tr class="table-info">
                     <td>zampis z1 z2</td>
                     <td>zampis z1 z2 5/8</td>
                     <td>10</td>
                  </tr>                  
                  <tr class="table-info">
                     <td>zampis z1 z2</td>
                     <td>umberta u1 u2 8/8</td>
                     <td>7</td>
                  </tr>
                  <tr class="table-info">
                     <td>nadia n1 n2</td>
                     <td>Belmonte */8</td>
                     <td>5</td>
                  </tr>
               </tbody>
               <!--Table body-->
            </table>
<h3>Mercado de mi curso (Demandas)</h3>
<!--Table-->
            <table class="table table-striped w-auto table-bordered">
               <!--Table head-->
               <thead>
                  <tr>
                     <th>Nombre</th>
                     <th>Cromo</th>
                     <th>Postura(estrellas)</th>
                  </tr>
               </thead>
               <!--Table head-->
               <!--Table body-->
               <tbody>
                  <tr class="table-info">
                     <td>Belmonte</td>
                     <td>zampis z1 z2 5/8</td>
                     <td>10</td>
                  </tr>                  
                  <tr class="table-info">
                     <td>zampis z1 z2</td>
                     <td>umberta u1 u2 7/8</td>
                     <td>7</td>
                  </tr>
               </tbody>
               <!--Table body-->
            </table>
<h3>Mercado de importación</h3>
<!--Table-->
            <table class="table table-striped w-auto table-bordered">
               <!--Table head-->
               <thead>
                  <tr>
                     <th>Nombre</th>
                     <th>Cromo</th>
                     <th>Postura(estrellas)</th>
                  </tr>
               </thead>
               <!--Table head-->
               <!--Table body-->
               <tbody>
                  <tr class="table-info">
                     <td>Retamas</td>
                     <td>Huguio z1 z2 */10</td>
                     <td>5</td>
                  </tr>                  
               </tbody>
               <!--Table body-->
            </table>
		</div>

	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
	<script type="text/javascript">
				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 3000);
					});
	</script>
	

</body>
</html>
