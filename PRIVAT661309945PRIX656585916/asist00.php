
<?php
session_start();
include('../includes/config.php');
require_once("../UTILS/dbutils.php");

$db=conectarDB();
 
$_SESSION['contador']=0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <?php
  if (isset($_GET['supu']))
  {
    $_SESSION['alogin']='ADMIN';
  }
  $sql = "SELECT username from admin;";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $result=$query->fetch(PDO::FETCH_OBJ);
if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
{ 
  header('location:../admin/index.php');
}
  ?>
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
var fff = "";
function HashTable(obj)
{
    this.length = 0;
    this.items = {};
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            this.items[p] = obj[p];
            this.length++;
        }
    }

    this.setItem = function(key, value)
    {
        var previous = undefined;
        if (this.hasItem(key)) {
            previous = this.items[key];
        }
        else {
            this.length++;
        }
        this.items[key] = value;
        return previous;
    }

    this.getItem = function(key) {
        return this.hasItem(key) ? this.items[key] : undefined;
    }

    this.hasItem = function(key)
    {
        return this.items.hasOwnProperty(key);
    }
   
    this.removeItem = function(key)
    {
        if (this.hasItem(key)) {
            previous = this.items[key];
            this.length--;
            delete this.items[key];
            return previous;
        }
        else {
            return undefined;
        }
    }
    

    this.keys = function()
    {
        var keys = [];
        for (var k in this.items) {
            if (this.hasItem(k)) {
                keys.push(k);
            }
        }
        return keys;
    }

    this.values = function()
    {
        var values = [];
        for (var k in this.items) {
            if (this.hasItem(k)) {
                values.push(this.items[k]);
            }
        }
        return values;
    }

    this.each = function(fn) {
        for (var k in this.items) {
            if (this.hasItem(k)) {
                fn(k, this.items[k]);
            }
        }
    }

    this.clear = function()
    {
        this.items = {}
        this.length = 0;
    }
}
<?php
$vAsignaturasSesiones = getSesionesAsignaturas($db);
 echo "var h = new HashTable({"; 
  $comma="";
  foreach ($vAsignaturasSesiones as $ase)
        {
            echo $comma.$ase["ID"].': "'.$ase["SESIONES"].'"';   
            $comma=",";
        } 
    echo "});";
       ?>

 //      alert('original length: ' + h.length);
//alert('value of key "one": ' + h.getItem('3'));

/*alert('has key "foo"? ' + h.hasItem('foo'));
alert('previous value of key "foo": ' + h.setItem('foo', 'bar'));
alert('length after setItem: ' + h.length);
alert('value of key "foo": ' + h.getItem('foo'));
alert('value of key "im no 4": ' + h.getItem("im no 4"));
h.clear();
alert('length after clear: ' + h.length); 
*/

function managebuttonReset() 
{         
  Swal.fire({
  title: 'RESETEAR FALTAS',
  text: "¿Resetear las faltas del dia "+document.getElementById("datepicker").value+
    " y la asignatura "+document.getElementById("sel11").options[document.getElementById("sel11").selectedIndex ].text+"?",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Continuar'
  }).then((result) => {
  if (result.value) {
    document.getElementById("form2").action="asist010.php";
    document.getElementById("form2").submit();
  }
  });
}  
    
  function managebuttonDash()
  { 
    document.getElementById("form2").action="../admin/dashboard.php";
      document.getElementById("form2").submit();
  }
  function managebutton()
   { 
     var error1 ='';
     var vTieneErrorClase = ['1','2','4','3','5','7','8','10','11','14'];
     var vTieneErrorSesiones = ['1','4','11'];
     sSel11Value = document.getElementById('sel11').value;
     sacc = document.getElementById('sacciones').value;
     
     if (vTieneErrorClase.includes(sacc) && (sSel11Value==''))
     {
       error1 ='Selecciona una clase';
     }
     else if  (vTieneErrorSesiones.includes(sacc) && (document.getElementById("ssesiones").options[document.getElementById("ssesiones").selectedIndex].text=='0'))
     {
       error1 ='Selecciona un número de sesiones mayor que cero';
     }
     
     //alert( 'error1:'+error1);
     //alert( 'sSel11Value:'+error1);
     
     if (error1=='')
     {
     var height = window.innerHeight
    || document.documentElement.clientHeight
    || document.body.clientHeight;
      document.getElementById('altura').value=height;
     if(document.getElementById('sacciones').value==10){
       managebuttonReset();
     }else{
       document.getElementById("form2").action="asist0"+document.getElementById('sacciones').value+".php";
      document.getElementById("form2").submit();
     }
     }
     else
     {
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: error1
        })
      }
     }


$(function(){
    $("#datepicker").datepicker({
        dateFormat: "yy-mm-dd",
              changeMonth: true,
        changeYear: true,
          onSelect: function(dateText){
        var seldate = $(this).datepicker('getDate');
        seldate = seldate.toDateString();
        seldate = seldate.split(' ');
        var weekday=new Array();
            weekday['Mon']=0;
            weekday['Tue']=1;
            weekday['Wed']=2;
            weekday['Thu']=3;
            weekday['Fri']=4;
            weekday['Sat']=5;
            weekday['Sun']=6;
            fff = weekday[seldate[0]];
    }
    });
});
function cargaInicial()
{   
    $("#datepicker").datepicker().datepicker("setDate", new Date());
  var seldate = $("#datepicker").datepicker('getDate');
        seldate = seldate.toDateString();
        seldate = seldate.split(' ');
        var weekday=new Array();
            weekday['Mon']=0;
            weekday['Tue']=1;
            weekday['Wed']=2;
            weekday['Thu']=3;
            weekday['Fri']=4;
            weekday['Sat']=5;
            weekday['Sun']=6;
            fff = weekday[seldate[0]];
}
       
function setSesiones()
{
  d = document.getElementById("sel11").value;
  //alert(d);
  var idAs = d.substr(d.indexOf('--')+2);
  //alert(idAs);
 
  var totalSes = h.getItem(idAs);
  //alert(totalSes);
  var res = totalSes.charAt(fff);
  //alert(res);
  document.getElementById("ssesiones").value = res;
 
  
}
  </script>

</head>
<body onload="cargaInicial()"> 
<form id="form2" method="post" action="asist01.php">
 <input type='hidden' name='altura' id='altura'/>
  <span class="label label-primary">Selecciona Clase</span>
      <select onchange="setSesiones()" class="form-control" id="sel11" name="sel11">
        <option selected='selected'></option>
        <?php
        $listaCursos= getAsignaturasConCurso($db);
        foreach ($listaCursos as $curso)
        {
           $pos = strrpos($curso, "--");
           $idAs=substr($curso,$pos+2,strlen($curso));
           $nombre = substr($curso,0,$pos);
          $posAs= strrpos($nombre,"*");
           $clase = substr($nombre,$posAs+1,strlen($nombre));
            echo "<option value='".$curso."'>".$nombre."</option>";
        }
        ?>
    </select>
  <div class="input-group">
    <div class="input-group-append">
      <span class="label label-primary">Selecciona número de elegidos</span>
      <select class="form-control" id="selelegido" name="selelegido">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3" selected="selected">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
      </select>

      <span class="input-group-text"><span class="label label-primary">¿marc-algoritm?</span></span>

      <input type="checkbox" class="form-check-input" id="brioAl" name="brioAl">
     </div>  </div>
   <span class="label label-primary">Selecciona número de sesiones</span>
      <select class="form-control" id="ssesiones" name="ssesiones">
        <option value="0">0</option>
        <option value="1" selected="selected">1</option>
        <option value="2">2</option>
        <option value="3" >3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
      </select>
  <span class="label label-primary">Selecciona acción</span>
      <select class="form-control" id="sacciones" name="sacciones">
        <option value="1" selected="selected">Pasar lista</option>
        <option value="3">Modificar lista</option>
        <option value="10" >Reset lista</option>
        <option value="5">Generar aleatorio</option>
        <option value="6">Hacer votación</option>
        <option value="7">Generar informes</option>
        <option value="8">Estrellas</option>
        <option value="9">Trivial</option>
        <option value="11">Scan lista</option>
        <option value="12">Gen QR</option>
        <option value="13">Gen código online</option>
        <option value="14">Reset correos alumnos</option>
      </select>
    <p> <span class="label label-primary">Día</span></p>
    <p><input type="text" name="datepicker" id="datepicker"></p>
  <div class="form-group">
  <a onclick="managebutton()"  class="btn btn-danger btn-outline btn-wrap-text">Go!</a>
  </div>
 <span class="label label-danger">NÚMERO ALUMNOS</span><br/>
  <span class="label label-primary">TOTAL</span>
  <span class="label label-warning"><?php echo getNumeroTotalAlumnos($db);?></span><br/>
  <?php  foreach ($listaCursos as $curso)
        {
           $pos = strrpos($curso, "--");
           $idAs=substr($curso,$pos+2,strlen($curso));
           $nombre = substr($curso,0,$pos);
          //$posAs= strrpos($nombre,"*");
           //$clase = substr($nombre,$posAs+1,strlen($nombre));
            echo '<span class="label label-primary">'.$nombre.'</span>';
  echo '<span class="label label-success">'.getNumeroAlumnosFromAsignaturaId($db,$idAs).'</span>';
  echo '<br/>';

       
        } 
  ?>
   <span class="label label-danger">ALUMNOS FALTONES</span>
  <br>
  <?php 
    $arrayAlumnosFaltones = getAlumnosFaltones($db);
  
   
  
    for($i = 0; $i< Count($arrayAlumnosFaltones);$i++){
     
      $fila = $arrayAlumnosFaltones[$i];
      echo '<span class="label label-primary">'.($i+1).' '.$fila["Nombre"].' '.$fila["Apellido"].'</span>';
      echo '<span class="label label-success">'.$fila["NFALTAS"].'</span>';
      echo '<br/>';
      
    }
  
    $arrayAlumnosFaltones = getCursosFaltones($db);
  
   echo '<span class="label label-danger">CURSOS FALTONES</span>
    <br>';
    for($i = 0; $i< Count($arrayAlumnosFaltones);$i++){
     
      $fila = $arrayAlumnosFaltones[$i];
      echo '<span class="label label-primary">'.($i+1).' '.$fila["nombre"].'</span>';
      echo '<span class="label label-success">'.$fila["contador"].'</span>';
      echo '<br/>';
      
    }
    
                                       
  
  
  ?>
 
  <?php 

  $aAllAsignaturas = getAllAsignaturas($db);
  $arrayTotalElegidos = array();
  foreach ($aAllAsignaturas as $asignatura) 
  {
     $arrayFantamas = getFantasmasFromAsignaturaID($db,$asignatura['ID']);

    
    foreach ($arrayFantamas as $fantasma) 
    {    
      //echo '<p><span class="label label-warning">'.$fantasma['DIA'].'</span>';
      $vectorElegidos = explode(",", $fantasma['ELEGIDOS']);
      if ($vectorElegidos[0]!='')
      {
        //$cont2=1;
        for ($i=0; (($i < Count($vectorElegidos))&&($i<3)); $i++) 
        { 
          if (!array_key_exists($vectorElegidos[$i], $arrayTotalElegidos)) 
          {
            $arrayTotalElegidos[$vectorElegidos[$i]]=0;
          }
          $arrayTotalElegidos[$vectorElegidos[$i]]=$arrayTotalElegidos[$vectorElegidos[$i]]+1;
          //$alumno = getAlumnoFromID($db,$vectorElegidos[$i]);
          //var_export($alumno);
        //echo '<span class="label label-primary">'.$cont2.' '.$alumno['NOMBRE'].' '.$alumno['APELLIDO1'].'</span>';
        //$cont2=$cont2+1;
        }
      }
    //echo '</p>';
    }
  }


  
  $groups = array();
  foreach ($arrayTotalElegidos as $k => $v) {
    $groups[$v][] = $k;
  }
  krsort($groups);
  $sorted = array();
  foreach ($groups as $value => $group) {
    foreach ($group as $key) {
        $sorted[$key] = $value;
    }
  }
  echo '<br/><p><span class="label label-danger">RANKING TOTAL ELEGIDOS</span>
  </p>';
  $cont=1;
  $contIAnt=0;

  $totalConPorcentajes = array ();

  foreach ($sorted as $alumId => $contI ) 
  {
    if ($contIAnt>$contI)
    {
      $cont=$cont+1;     
    }
    if ($cont>5)
    {
      break;
    }
    $contIAnt=$contI;
    $fila_asignatura = getAsignaturasFromCurso($dbh,getAlumnoFromID($db,$alumId)['ID_CURSO'])[0];
    $arrayFantamas = getFantasmasFromAsignaturaID($db,$fila_asignatura['ID']);
    $nFant = Count($arrayFantamas);
    $alumno = getAlumnoFromID($db,$alumId);

    echo '<span class="label label-primary">'.$cont.'º.- '.$alumno["NOMBRE"].' '.$alumno["APELLIDO1"].'</span>';
   echo '<span class="label label-info">'.$fila_asignatura['NOMBRE'].'</span>';
    echo '<span class="label label-success">'.$contI.(($nFant>0)?' de '.$nFant:'').'</span>';    
    
    if ($nFant>0)
    {
      $valorPor = number_format(($contI*100)/$nFant,2);
      echo '<span class="label label-warning">'.$valorPor.'%</span>';
      if (array_key_exists($valorPor, $totalConPorcentajes)) 
      {
$totalConPorcentajes[$valorPor]=$totalConPorcentajes[$valorPor].", ".$alumno["NOMBRE"].' '.$alumno["APELLIDO1"]."(".$fila_asignatura['NOMBRE'].")[".$contI.(($nFant>0)?' de '.$nFant:'')."]";
      }
      else
      {
        $totalConPorcentajes[$valorPor]=$alumno["NOMBRE"].' '.$alumno["APELLIDO1"]."(".$fila_asignatura['NOMBRE'].")[".$contI.(($nFant>0)?' de '.$nFant:'')."]";
      }
      
    }

    echo '<br/>';
  }

echo '<br/><span class="label label-danger">RANKING PORCENTAJES ELEGIDOS</span>
  <br/>';
  krsort($totalConPorcentajes);
  //var_export($totalConPorcentajes);

  $cont=1;
  $contIAnt=0;

  foreach ($totalConPorcentajes as $porcen => $data ) 
  {

    echo '<span class="label label-primary">'.$cont.'º.-'.$porcen.'%</span>';
    echo '<span class="label label-success">'.$data.'</span>';    
    echo '<br/>';
    $cont++;
  }

 
  ?>
  </form>






   

<br/><a onclick="managebuttonDash()"  class="btn btn-danger btn-outline btn-wrap-text">Dashboard</a><br/>
</body>
</html>



