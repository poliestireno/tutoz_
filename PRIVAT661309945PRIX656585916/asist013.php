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

$_SESSION['contador']=0;
$codigoEncriptado="";
$codigoEncriptadoUrl="";
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
  $codigoEncriptado = openssl_encrypt ($_POST["sel11"].",".$_POST["ssesiones"].",".$_POST["datepicker"],"AES-128-ECB","kgYYBOihH8/(ggG/)gKGB8/biLJLDJOIUD/(%&/UG(DF(/F%&(IGDF%(F)HFG=FD:_V:F_VBLVP?F=F)FKIF)))");
  $codigoEncriptadoUrl="https://magicomagico.com/tutoz/PUBLIC656585916LOP661309945/parrillaOnline.php?cod=".urlencode($codigoEncriptado);
  if (!existeFantasma($db,$idAs,$_POST["datepicker"]))
  {
    //borrarFaltasAsignaturaDiaYFantasma($db,$idAs,$_POST["datepicker"]);
    insertarFalta($db,-1,$idAs,$_POST["ssesiones"],$_POST["datepicker"]);
    insertMasivoFaltasAsigDia($db,$_POST["ssesiones"],$idAs, $_POST["datepicker"],$grado,$nivel);     
  }
 
    $listaAlumnos = getAlumnosGradoNivel($db,$grado,$nivel);
    $auxLista = array();  
    foreach ($listaAlumnos as $alumno)
    {
      $auxLista[] = $alumno['NOMBRE']." ". $alumno['APELLIDO1']."--".$alumno['ID'];
    }
    $_SESSION['vAlumnos'] = $auxLista ;
    sort($_SESSION['vAlumnos']);
    $alumnos1=$_SESSION['vAlumnos'] ;
    $_SESSION['vAlumnos2'] = array ();
    $_SESSION['vElegidos'] = array ();
    if(!isset($_POST['brioAl'])){
      if (sizeof($alumnos1)>4)
      {
        for ($i=0;$i<$_POST["selelegido"];$i++){
          $nRand =  rand(1, sizeof($alumnos1)-3);
          while (in_array($nRand, $_SESSION['vElegidos'])){
            $nRand =  rand(0, sizeof($alumnos1)-3);
          }
          $_SESSION['vElegidos'][]=$nRand;    
        }
      }
    }
  else{
      //$alumnos = getNumElegAlumnos($db,$grado,$nivel);
      $vPares = getNumElegAlumnos2($db, $grado,$nivel);
      $vEleAux = array(); 
      for ($i=0;$i<$_POST["selelegido"];$i++){
        $indice = 0;
        //var_dump($alumnos1);     
        
        $idMarc = MarcoAntonioritmo($vPares);
        while (in_array($idMarc, $vEleAux))
        {
          $idMarc = MarcoAntonioritmo($vPares);
        }        
        
        for($j = 0;$j<sizeof($alumnos1);$j++){

        $posGuion = strrpos($alumnos1[$j], "--");
        $idAs=substr($alumnos1[$j],$posGuion+2,strlen($alumnos1[$j]));

        //echo "ELE" ;
        //  var_dump($_SESSION['vElegidos']);
        //echo "idDFuera" ;var_dump($idMarc);
         // echo "idAS:";
         // var_dump($idAs);
         // echo "idMarc:";
         // var_dump($idMarc);
          if($idAs == $idMarc){
            $indice = $j;
            $vEleAux[] =$idMarc;
            break;
          }
        }
        //echo "Calculando brio algoritmo: " . $indice;
        $_SESSION['vElegidos'][]=$indice;    
      } 
    }
    //var_dump($_SESSION['vElegidos']);
    $valorInicial="0,";
    $comma="";
    foreach ($_SESSION['vElegidos'] as $ase)
    {
      $valorInicial.= $comma.$ase;   
      $comma=",";
    } 
  
    
  
//    $valorInicial.="-";
    modificarContEleg($db,$idAs,$_POST["datepicker"],$valorInicial);
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
      
      function managebuttonP()
      {
        document.getElementById("form14").action="asist00.php";
        document.getElementById("form14").submit(); 
      } 
      
      var fff = "";

      function HashTable(obj) {
        this.length = 0;
        this.items = {};
        for (var p in obj) {
          if (obj.hasOwnProperty(p)) {
            this.items[p] = obj[p];
            this.length++;
          }
        }

        this.setItem = function(key, value) {
          var previous = undefined;
          if (this.hasItem(key)) {
            previous = this.items[key];
          } else {
            this.length++;
          }
          this.items[key] = value;
          return previous;
        }

        this.getItem = function(key) {
          return this.hasItem(key) ? this.items[key] : undefined;
        }

        this.hasItem = function(key) {
          return this.items.hasOwnProperty(key);
        }

        this.removeItem = function(key) {
          if (this.hasItem(key)) {
            previous = this.items[key];
            this.length--;
            delete this.items[key];
            return previous;
          } else {
            return undefined;
          }
        }


        this.keys = function() {
          var keys = [];
          for (var k in this.items) {
            if (this.hasItem(k)) {
              keys.push(k);
            }
          }
          return keys;
        }

        this.values = function() {
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

        this.clear = function() {
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


      function managebutton() {
        
     var error1 ='';
     sSel11Value = document.getElementById('sel11').value;
     
     if ((sSel11Value==''))
     {
       error1 ='Selecciona una clase';
     }
     else if  ( (document.getElementById("ssesiones").options[document.getElementById("ssesiones").selectedIndex].text=='0'))
     {
       error1 ='Selecciona un número de sesiones mayor que cero';
     }
     if (error1=='')
     {
          var height = window.innerHeight ||
            document.documentElement.clientHeight ||
            document.body.clientHeight;
          document.getElementById('altura').value = height;
          document.getElementById('a13').value = 1;
          document.getElementById("form2").action = "asist013.php";
          document.getElementById("form2").submit();

        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: error1
          })
        }
      }

      $(function() {
        $("#datepicker").datepicker({
          dateFormat: "yy-mm-dd",
          changeMonth: true,
          changeYear: true,
          onSelect: function(dateText) {
            var seldate = $(this).datepicker('getDate');
            seldate = seldate.toDateString();
            seldate = seldate.split(' ');
            var weekday = new Array();
            weekday['Mon'] = 0;
            weekday['Tue'] = 1;
            weekday['Wed'] = 2;
            weekday['Thu'] = 3;
            weekday['Fri'] = 4;
            weekday['Sat'] = 5;
            weekday['Sun'] = 6;
            fff = weekday[seldate[0]];
          }
        });
      });

      function cargaInicial() {
        $("#datepicker").datepicker().datepicker("setDate", new Date());
        var seldate = $("#datepicker").datepicker('getDate');
        seldate = seldate.toDateString();
        seldate = seldate.split(' ');
        var weekday = new Array();
        weekday['Mon'] = 0;
        weekday['Tue'] = 1;
        weekday['Wed'] = 2;
        weekday['Thu'] = 3;
        weekday['Fri'] = 4;
        weekday['Sat'] = 5;
        weekday['Sun'] = 6;
        fff = weekday[seldate[0]];
      }

      function setSesiones() {
        d = document.getElementById("sel11").value;
        //alert(d);
        var idAs = d.substr(d.indexOf('--') + 2);
        //alert(idAs);

        var totalSes = h.getItem(idAs);
        //alert(totalSes);
        var res = totalSes.charAt(fff);
        //alert(res);
        document.getElementById("ssesiones").value = res;


      }
    function copyToClipboard(e) {
    var tempItem = document.createElement('input');

    tempItem.setAttribute('type','text');
    tempItem.setAttribute('display','none');
    
    let content = e;
    if (e instanceof HTMLElement) {
    		content = e.innerHTML;
    }
    
    tempItem.setAttribute('value',content);
    document.body.appendChild(tempItem);
    
    tempItem.select();
    document.execCommand('Copy');

    tempItem.parentElement.removeChild(tempItem);
}
    </script>

  </head>

  <body onload="cargaInicial()">
    <form id="form2" method="post" action="asist01.php">
      <input type='hidden' name='altura' id='altura' />
      <input type='hidden' name='a13' id='a13' />
      <input type='hidden' name='selelegido' id='selelegido' value ="<?php echo $_POST['selelegido']?>" />
      
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
      <p> <span class="label label-primary">Día</span></p>
      <p><input type="text" name="datepicker" id="datepicker"></p>
      <div class="form-group">
        <a onclick="managebutton()" class="btn btn-danger btn-outline btn-wrap-text">Generar código online</a>
      </div>
      <div class="form-group">
        <c><?php echo $textoCodigo?></c>
         <input type="text" class="form-control" id="filtro" name = "filtro" value="<?php echo $codigoEncriptado?>">
         <input type="text" class="form-control" id="filtroUrl" name = "filtroUrl" value="<?php echo $codigoEncriptadoUrl?>">
      </div>
      <div class="form-group">
        <a onclick="copyToClipboard(document.getElementById('filtro').value)" class="btn btn-danger btn-outline btn-wrap-text">Copiar código</a>
        <a onclick="copyToClipboard(document.getElementById('filtroUrl').value)" class="btn btn-danger btn-outline btn-wrap-text">Copiar URL</a>
      </div>
    </form>
    <form id="form14" method="post" action="asis00.php">
  <br/><br/>
    <div class="form-row">
    <div class="form-group">
  <a onclick="managebuttonP()"  class="btn btn-danger btn-outline btn-wrap-text">Menu Principal</a> 
      </div>
  </div>
      </form>
  </body>

  </html>