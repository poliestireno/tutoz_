<?php
session_start();
require_once("../UTILS/dbutils.php");
//$db=conectarDB();
 
$_SESSION['contador']=0;
$textoCodigo="";
if  (isset($_POST['a13']))
{
  $pos = strrpos($_POST["sel11"], "*");
  $grado=substr($_POST["sel11"],0,$pos-1);
  $nivel=substr($_POST["sel11"],$pos-1,1);
  $posGuion = strrpos($_POST["sel11"], "--");
  $clase=substr($_POST["sel11"],0,$posGuion);
  $idAs=substr($_POST["sel11"],$posGuion+2,strlen($_POST["sel11"]));
  $textoCodigo= "Código generado para ".$clase. ", número sesiones ".$_POST["ssesiones"]." y día ".$_POST["datepicker"];
}

?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <title>Asis-tencia</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../UTILS/mi.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>


    <script>


      function managebutton() {
          var height = window.innerHeight ||
            document.documentElement.clientHeight ||
            document.body.clientHeight;
          document.getElementById('altura').value = height;
          document.getElementById("form2").action = "parrillaOnline.php";
          document.getElementById("form2").submit();
      }


    </script>

  </head>

  <body>
    <form id="form2" method="post" action="parrillaOnline.php">
      <input type='hidden' name='altura' id='altura' />
      <h3><span class="label label-primary">Introduce el código de la sesión de clase</span></h3>
      <div class="form-group">
        <c><?php echo $textoCodigo?></c>
         <input type="text" class="form-control" id="cod" name = "cod">
      </div>
      <div class="form-group">
        <a onclick="managebutton()" class="btn btn-danger btn-outline btn-wrap-text">Go!</a>
      </div>
    </form>
  </body>

  </html>