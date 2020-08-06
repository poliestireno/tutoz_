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
function getEstrellasEscaleraSimple3($aF,$nEstrellasEscaleraSimple3)
{
   return ((($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1)))||(($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1))))?$nEstrellasEscaleraSimple3:0;
}
function getEstrellasEscaleraSimple4($aF,$nEstrellasEscaleraSimple4)
{
   return (($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1)))?$nEstrellasEscaleraSimple4:0;
}
function getEstrellasEscalera3($aF,$aFI,$nEstrellasEscalera3)
{
   return (((($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1)))&&((($aFI[0]==$aFI[1])&&$aFI[1]!=-1)&&(($aFI[1]==$aFI[2])&&$aFI[1]!=-1)))
    ||
    ((($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1)))&&((($aFI[1]==$aFI[2])&&$aFI[1]!=-1)&&(($aFI[2]==$aFI[3])&&$aFI[2]!=-1))))?$nEstrellasEscalera3:0;    
}
function getEstrellasEscalera4($aF,$aFI,$nEstrellasEscalera4)
{
   return ((($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2  ]+1)))&&
    (((($aFI[0]==$aFI[1])&&$aFI[1]!=-1)&&(($aFI[1]==$aFI[2])&&$aFI[1]!=-1)&&(($aFI[2]==$aFI[3])&&$aFI[3]!=-1)&&(($aFI[0]==$aFI[3])&&$aFI[3]!=-1)))
  )?$nEstrellasEscalera4:0;
}
function getEstrellasEscaleraEstrellas3($aF,$nEstrellasEscaleraEstrellas3)
{
   return ((($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1)))||(($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1))))?$nEstrellasEscaleraEstrellas3:0;
}
function getEstrellasEscaleraEstrellas4($aF,$nEstrellasEscaleraEstrellas4)
{
   return (($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1)))?$nEstrellasEscaleraEstrellas4:0;
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
  
  $alumnoDB = getAlumnoFromCorreo($dbh, $correo);

  $ordenCombosDBAnt = $alumnoDB['ORDEN_COMBOS'];
$vectorOrdenCombosAnt = explode(",", $ordenCombosDBAnt);

$ordenReferenciasDB = $alumnoDB['ORDEN_REFERENCIAS_TOTAL'];
$aReferencias= explode(',', $ordenReferenciasDB);

$ordenCreadoresDB = $alumnoDB['ORDEN_CREADORES'];
$aCreators= explode(',', $ordenCreadoresDB);

$ordenAlbumDB = $alumnoDB['ORDEN_ALBUM'];
$aCromosAlbum= explode(',', $ordenAlbumDB);

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
    $aAuxCromos = array();
    
$aAuxCromos[]=($aCromosAlbum[$i*4]!=-1)?getCromoFromID($dbh,$aCromosAlbum[$i*4])['mana_w']:-1;
$aAuxCromos[]=($aCromosAlbum[(1)+$i*4]!=-1)?getCromoFromID($dbh,$aCromosAlbum[(1)+$i*4])['mana_w']:-1;
$aAuxCromos[]=($aCromosAlbum[(2)+$i*4]!=-1)?getCromoFromID($dbh,$aCromosAlbum[(2)+$i*4])['mana_w']:-1;
$aAuxCromos[]=($aCromosAlbum[(3)+$i*4]!=-1)?getCromoFromID($dbh,$aCromosAlbum[(3)+$i*4])['mana_w']:-1;
    
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
    $adCromos = getAdminCromos($dbh);
    switch ($filaI) {
      case '1':
        $estreAux=getEstrellasPareja($aAux,$adCromos['PAREJA']);
        break;
      case '2':
        $estreAux=getEstrellasDoblePareja($aAux,$adCromos['DOBLEPAREJA']);
        break;
      case '3':
        $estreAux=getEstrellasTrio($aAux,$adCromos['TRIO']);
        break;
      case '4':
        $estreAux=getEstrellasCuarteto($aAux,$adCromos['CUARTETO']);
        break;
      case '5':
        $estreAux=getEstrellasEscaleraSimple3($aAuxRef,$adCromos['ESCALERASIMPLE3']);
        break;
      case '6':
        $estreAux=getEstrellasEscaleraSimple4($aAuxRef,$adCromos['ESCALERASIMPLE4']);
        break;
      case '7':
        $estreAux=getEstrellasEscalera3($aAuxRef,$aAux,$adCromos['ESCALERA3']);
        break;
      case '8':
        $estreAux=getEstrellasEscalera4($aAuxRef,$aAux,$adCromos['ESCALERA4']);
        break;
      case '9':
        $estreAux=getEstrellasEscaleraEstrellas3($aAuxCromos,$adCromos['ESCALERA3_ESTRELLAS']);
        break;
      case '10':
        $estreAux=getEstrellasEscaleraEstrellas4($aAuxCromos,$adCromos['ESCALERA4_ESTRELLAS']);
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
      //echo 'nivelActual:'.$nivelActual;
      
      if ($nivelReal!=$nivelActual)
      {
        cambiarNivelAlumno($dbh,$_SESSION['alogin'],$nivelReal);
        //AplicarRecompesasNivel
        $cromo = getCromo($dbh,$_SESSION['alogin']);
        // se da por hash(algo, data)echo que las estrellas del cromo propio coinciden con el número de nivel del alumno.
        modificarNEstrellasCromo($dbh,$_SESSION['alogin'],$nivelReal);
        //meter aqui las demas recompesas.
        
        
        // notificación general de subida de nivel a la clase
        $alumnoDB = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);
        $clase = getAsignaturasFromCurso($dbh,$alumnoDB['ID_CURSO'])[0]['NOMBRE'];
        $nombreAlumno = $alumnoDB['NOMBRE']." ".$alumnoDB['APELLIDO1'];

        $recompensasAlumno= getNivelFromNumeroNivel($dbh,$_SESSION['alogin'],$nivelReal)['RECOMPENSAS'];
        $mensaje = $nombreAlumno." ha pasado a nivel ".$nivelReal." obteniendo las siguientes recompensas: ".$recompensasAlumno;
        mandarNotificacion($dbh,'Admin',$clase,$mensaje);
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
  
function iniciarPartidaPPT($dbh,$correoJugador,$correoPNJ)
{
  $yajugada=0;
  $listaJugadasJugador = getBot($dbh,$correoJugador)['LISTA_JUGADAS_HOY_PTT'];
  $aJugadasJugador = explode(",", $listaJugadasJugador);
  $filaPNJ = getAlumnoFromCorreo($dbh,$correoPNJ);
  if ((Count($aJugadasJugador)>0)&&(date('Y-m-d')==$aJugadasJugador[0]))
  {
    //ya jugada?
    if (in_array($filaPNJ['ID'], $aJugadasJugador)) 
    {
      return 1;
    }
    else
    {
      $listaJugadasJugador.=",".$filaPNJ['ID'];
    }   
  }
  else
  {
    $listaJugadasJugador=date('Y-m-d').",".$filaPNJ['ID'];
  }
  modificarListaJugadasPPT($dbh,getBot($dbh,$correoJugador)['ID'], $listaJugadasJugador);
  return $yajugada;
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