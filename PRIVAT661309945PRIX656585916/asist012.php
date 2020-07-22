<?php
 session_start();
 include('../includes/config.php');
 require_once("../UTILS/dbutils.php");
  $sql = "SELECT username from admin;";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $result=$query->fetch(PDO::FETCH_OBJ);
if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
{ 
  header('location:../admin/index.php');
}
$db=conectarDB();
if (isset($_POST['filtro'])){
  $vectorAlumnos = buscarAlumnos($db, $_POST['filtro']);
}
?>
<html lang="es">
<head>

  <title>Asis-tencia</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../UTILS/mi.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <script>
function htmlEncode (value){
  return $('<div/>').text(value).html();
}
  

  function initQr(cod)
  {
    <?php
    if (isset($_POST['filtro']) && (Count($vectorAlumnos)==1))
    {
      echo '$(".qr-code").attr("src", "https://chart.googleapis.com/chart?cht=qr&chl=" + htmlEncode('.$vectorAlumnos[0]['ID'].') + "&chs=160x160&chld=L|0");';
    }
    ?>
  }
  function genQR(cod)
  {
    $(".qr-code").attr("src", "https://chart.googleapis.com/chart?cht=qr&chl=" + htmlEncode(cod) + "&chs=160x160&chld=L|0");
  }
  function managebuttonP()
  {
    document.getElementById("form1").action="asist00.php";
    document.getElementById("form1").submit(); 
  }   
     function managebuttonB()
  {
    if (document.getElementById("filtro").value!='')
    {
      document.getElementById("form2").action="asist012.php";
      document.getElementById("form2").submit(); 
    }
  }    
  </script>
</head>
<body onload="initQr()">
  <form id="form2" method="post" action="asist012.php">
    <h1 class="text-center">Generador QR</h1>
    <br/>
    <div class="form-row">
    <div class="form-group col-md-2">
      <input type="text" class="form-control" id="filtro" name = "filtro" placeholder="nombre/apellido/curso">
    </div>
    <div class="form-group col-md-4">
      <a onclick="managebuttonB()"  class="btn btn-danger btn-outline btn-wrap-text">Buscar</a>
    </div>
  </div>
    <br/><br/><br/>
    <?php 
      if (isset($_POST['filtro'])){
        //var_dump($vectorAlumnos);
        if (Count($vectorAlumnos)==1)
        {
          echo '<div class="form-row">';
          echo '<div class="form-group col-md-4">';
          echo '<span class="label label-primary">'.$vectorAlumnos[0]['nCompleto'].'</span>';
          echo '<span class="label label-primary">'."Curso: ".getNombreCursoFromAlumno($db, $vectorAlumnos[0]['ID']).'</span>';
          echo '<span class="label label-primary">'."Código: ".$vectorAlumnos[0]['ID'].'</span>';
          echo '</div>';
          echo '</div>';
        }
        else
        {
          foreach ($vectorAlumnos as $alumno)
          {
          echo '<div class="form-row">';
          echo '<div class="form-group col-md-4">';
              echo '<a id="a1" onclick="genQR(\''.$alumno['ID'].'\')" class="label label-danger">Generar QR</a>';
              echo '<span class="label label-primary">'.$alumno['nCompleto'].'</span>';
              echo '<span class="label label-primary">'."Curso: ".getNombreCursoFromAlumno($db, $alumno['ID']).'</span>';
              echo '<span class="label label-primary">'."Código: ".$alumno['ID'].'</span>';
          echo '</div>';
          echo '</div>';
              
          }
        }
      }
    ?>
  </form>
<form id="form1" method="post" action="asis00.php">
  <br/><br/>
    <div class="form-row">
    <div class="form-group col-md-4">
  <a onclick="managebuttonP()"  class="btn btn-danger btn-outline btn-wrap-text">Menu Principal</a> 
      </div>
  </div>
      </form>
  <br/><br/>
  
  <div class="container-fluid">
  <div class="text-center">
    <img
         class="qr-code img-thumbnail img-responsive">
  </div>


</div>


</body>
</html>