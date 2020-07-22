<?php

function getEstrellasPareja($aF,$nEstrellasPareja)
{
  //var_dump($aF);
   return ((($aF[0]==$aF[1])&&$aF[1]!=-1)||(($aF[0]==$aF[2])&&$aF[2]!=-1)||(($aF[0]==$aF[3])&&$aF[3]!=-1)||(($aF[1]==$aF[2])&&$aF[1]!=-1)||(($aF[1]==$aF[3])&&$aF[1]!=-1)||(($aF[2]==$aF[3])&&$aF[3]!=-1))?$nEstrellasPareja:0;
}
function getEstrellasDoblePareja($aF,$nEstrellasDoblePareja)
{
   return (
   	((($aF[0]==$aF[1])&&$aF[1]!=-1)&&(($aF[2]==$aF[3])&&$aF[2]!=-1))||((($aF[0]==$aF[2])&&$aF[2]!=-1)&&(($aF[1]==$aF[3])&&$aF[1]!=-1))||((($aF[0]==$aF[3])&&$aF[3]!=-1)&&(($aF[1]==$aF[2])&&$aF[1]!=-1)))?$nEstrellasDoblePareja:0;
}
function getEstrellasTrio($aF,$nEstrellasTrio)
{
   return ((($aF[0]==$aF[3])&&$aF[3]!=-1)&&(($aF[3]==$aF[2])&&$aF[3]!=-1)||(($aF[0]==$aF[1])&&$aF[1]!=-1)&&(($aF[1]==$aF[2])&&$aF[1]!=-1)||(($aF[0]==$aF[1])&&$aF[1]!=-1)&&(($aF[1]==$aF[3])&&$aF[1]!=-1)||(($aF[1]==$aF[2])&&$aF[1]!=-1)&&(($aF[2]==$aF[3])&&$aF[2]!=-1))?$nEstrellasTrio:0;
}
function getEstrellasCuarteto($aF,$nEstrellasCuarteto)
{
   return ((($aF[0]==$aF[1])&&$aF[1]!=-1)&&(($aF[1]==$aF[2])&&$aF[1]!=-1)&&(($aF[2]==$aF[3])&&$aF[3]!=-1)&&(($aF[0]==$aF[3])&&$aF[3]!=-1))?$nEstrellasCuarteto:0;
}
function getEstrellasEscalera3($aF,$nEstrellasEscalera3)
{
   return ((($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1)))||(($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1))))?$nEstrellasEscalera3:0;
}
function getEstrellasEscalera4($aF,$nEstrellasEscalera4)
{
   return (($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2	]+1)))?$nEstrellasEscalera4:0;
}


// CALCULO DE NIVELES

function getEstrellasCromos($dbh,$correo)
{
	  	 $totalEstrellasCromos = 0;
		 $vectorCromos = getCromosDeAlbum($dbh,$correo);
		 foreach ($vectorCromos as $croo) 
		 {
		    $totalEstrellasCromos +=$croo['mana_w'];
		 }
		 return $totalEstrellasCromos;
}

function getEstrellasGanas($dbh,$correo)
{

  $totalComportamiento = 0;
  $aToCompor = getEstrellasComportamientoFromCorreo($dbh,$correo);  
  foreach ($aToCompor as $compor) 
  {
    $totalComportamiento += $compor['ESTRELLAS'];
  }
  return $totalComportamiento;
}


function getEstrellasRetos($dbh,$correo)
{

    $totalRetos = 0;
  $aToRetos = getEstrellasRetosFromCorreo($dbh,$correo);
  foreach ($aToRetos as $ret) {
    $totalRetos += $ret['ESTRELLAS_CONSEGUIDAS'];
  }
  return $totalRetos ;
}


function getEstrellasCombinaciones($dbh,$correo)
{
	$ordenCombosDBAnt = getAlumnoFromCorreo($dbh, $correo)['ORDEN_COMBOS'];
$vectorOrdenCombosAnt = explode(",", $ordenCombosDBAnt);

$ordenReferenciasDB = getAlumnoFromCorreo($dbh, $correo)['ORDEN_REFERENCIAS_TOTAL'];
$aReferencias= explode(',', $ordenReferenciasDB);

$ordenCreadoresDB = getAlumnoFromCorreo($dbh, $correo)['ORDEN_CREADORES'];
$aCreators= explode(',', $ordenCreadoresDB);
  $estrellas = 0;
  $sEstrellas = "";
  $mas="";
  $aCombos="";
  $comma="";
  //echo Count($aCreators);
if (Count($aCreators)>1)
{
  for ($i=0; $i < (Count($aCreators)/4); $i++) 
  { 
    $estreAux=0;
    $aAux = array();
    $aAux[]=$aCreators[$i*4];
    $aAux[]=$aCreators[(1)+$i*4];
    $aAux[]=$aCreators[(2)+$i*4];
    $aAux[]=$aCreators[(3)+$i*4];
    $aAuxRef = array();
    if (Count($aReferencias)>((3)+$i*4))
    {
      $aAuxRef[]=$aReferencias[$i*4];
      $aAuxRef[]=$aReferencias[(1)+$i*4];
      $aAuxRef[]=$aReferencias[(2)+$i*4];
      $aAuxRef[]=$aReferencias[(3)+$i*4];
    }
    else
    {
      $aAuxRef[]=-1;
      $aAuxRef[]=-1;
      $aAuxRef[]=-1;
      $aAuxRef[]=-1;     
    }
    $filaI=NULL;
    if (isset($_POST['selfila_'.($i+1)])) 
    {
      $filaI = $_POST['selfila_'.($i+1)];
    }
    if ($filaI==NULL)
    {
      $filaI = $vectorOrdenCombosAnt[$i];
    }
    $aCombos.=$comma.$filaI;
    $comma=",";
 //   var_dump($filaI);
    switch ($filaI) {
      case '1':
        $estreAux=getEstrellasPareja($aAux,getAdminCromos($dbh)['PAREJA']);
        break;
      case '2':
        $estreAux=getEstrellasDoblePareja($aAux,getAdminCromos($dbh)['DOBLEPAREJA']);
        break;
      case '3':
        $estreAux=getEstrellasTrio($aAux,getAdminCromos($dbh)['TRIO']);
        break;
      case '4':
        $estreAux=getEstrellasCuarteto($aAux,getAdminCromos($dbh)['CUARTETO']);
        break;
      case '5':
        $estreAux=getEstrellasEscalera3($aAuxRef,getAdminCromos($dbh)['ESCALERA3']);
        break;
      case '6':
        $estreAux=getEstrellasEscalera4($aAuxRef,getAdminCromos($dbh)['ESCALERA4']);
        break;
      
      default:
        # code...
        break;
    }
    $estrellas+=$estreAux;
    $sEstrellas.=$mas.$estreAux;
    $mas="+";
  }
}
$aRe = array();
$aRe []=$estrellas;
$aRe []=$sEstrellas;
$aRe []=$aCombos;
return $aRe;
}

function calcularEstrellasTotales($dbh,$correo)
{
  $estrellasCromos = getEstrellasCromos($dbh,$correo);
  $aRe = getEstrellasCombinaciones($dbh,$correo);
  $estrellasCombinaciones=$aRe [0];
  $sEstrellas=$aRe [1];
  $totalRetos = getEstrellasRetos($dbh,$correo);
  $totalComportamiento =getEstrellasGanas($dbh,$correo);
  $totalCromos = $estrellasCromos+$estrellasCombinaciones;
  $totalSuerte = 0;
  return $totalRetos+$totalComportamiento+$totalCromos+$totalSuerte;
}


function calcularNivelDeEstrellas($dbh,$estrellasTotales,$correo)
{
  $alumno = getAlumnoFromCorreo($dbh,$correo);
  $asignaturas = getAsignaturasFromCurso($dbh,$alumno['ID_CURSO']);
  // cogemos la primera asignatura del alumno para coger los niveles.
  $niveles = getNivelesFromCategoria($dbh,$asignaturas[0]['CATEGORIA_NIVEL']);
  
  $nivelAux=0;
  // vienen ordenados los niveles
  foreach ($niveles as $nivel) 
  {
    if ($estrellasTotales>=$nivel['ESTRELLAS_DESBLOQUEO'])
    {
      $nivelAux=$nivel['NUMERO'];
    }
    else
    {
      break;
    }
  }
  return $nivelAux;
}

// INI Recalcular nivel alumno, no se hace para un administrador

if (isset($_SESSION['alogin']))
{
  if (!esUsernameAdministrador($dbh,$_SESSION['alogin']))
  {
     $estrellasTotales = calcularEstrellasTotales($dbh,$_SESSION['alogin']);
      //echo "estrellas:".$estrellasTotales;

      $nivelReal = calcularNivelDeEstrellas($dbh,$estrellasTotales,$_SESSION['alogin']);

      //echo 'nivelReal:'.$nivelReal;
      $nivelActual = getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['NUMERO_NIVEL'];
      if ($nivelReal!=$nivelActual)
      {
        cambiarNivelAlumno($dbh,$_SESSION['alogin'],$nivelReal);
        //AplicarRecompesasNivel
        $cromo = getCromo($dbh,$_SESSION['alogin']);
        // se da por hash(algo, data)echo que las estrellas del cromo propio coinciden con el número de nivel del alumno.
        modificarNEstrellasCromo($dbh,$_SESSION['alogin'],$nivelReal);
        //meter aqui las demas recompesas.
        
        
        // notificación al alumno de subida de nivel
        $recompensasAlumno= getNivelFromNumeroNivel($dbh,$_SESSION['alogin'],$nivelReal)['RECOMPENSAS'];
        $mensaje = "Has pasado a nivel ".$nivelReal." obteniendo las siguientes recompensas: ".$recompensasAlumno;
        mandarNotificacion($dbh,'Admin',$_SESSION['alogin'],$mensaje);
      }
  }
}
// FIN Recalcular nivel alumno


function getNumeroSegundosAlumno($dbh,$correo)
{
  $alumno = getAlumnoFromCorreo($dbh,$correo);

  //segundos para niveles altos
  $segundosApertura=3;
  $nivelAlumno = $alumno['NUMERO_NIVEL'];
  switch ($nivelAlumno) {
    case 1:
      $segundosApertura=30;     
      break;
    case 2:
      $segundosApertura=20;     
      break;
    case 3:
      $segundosApertura=10;     
      break;    
  }
  return $segundosApertura;
}
  
function getPropsVisiblesCromo($dbh,$correo)
{
  $aProps = [
      "nombre" => 0,
      "artista" => 0,
      "atributo" => 0,
      "imagen" => 0,
      "descripcion" => 0,
      "color" => 0,
      "firma" => 0
  ];
  $alumno = getAlumnoFromCorreo($dbh,$correo);
  $nivelAlumno = $alumno['NUMERO_NIVEL'];
  if ($nivelAlumno>=1)
  {
    $aProps['nombre']=1;
    $aProps['artista']=1;    
  }
  if ($nivelAlumno>=2)
  {
    $aProps['atributo']=1;   
  }
  if ($nivelAlumno>=3)
  {
    $aProps['imagen']=1;   
  }
  if ($nivelAlumno>=4)
  {
    $aProps['descripcion']=1;   
  }
  if ($nivelAlumno>=5)
  {
    $aProps['color']=1;   
  }
  if ($nivelAlumno>=6)
  {
    $aProps['firma']=1;   
  }

  
  return $aProps;

}
?>