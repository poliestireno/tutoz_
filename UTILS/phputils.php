<?php

function modificarParesIncognito($dbh,$tareaId,$aNull)
{
  if ($aNull)
  {
    modificarAlumnosTareasIncognitoANull($dbh,$tareaId);
  }
  else
  {
    // se recalculan todos los pares
    $aAlumRetos = getAlumnosTareasFromTarea($dbh,$tareaId);
    shuffle($aAlumRetos);
    for ($i=0; $i < count($aAlumRetos); $i++) 
    { 
      $alumId=$aAlumRetos[$i]['ID_ALUMNO'];
      $alumIdAcorregir=$aAlumRetos[($i+1)%count($aAlumRetos)]['ID_ALUMNO'];
      modificarAlumnoTareaIncognito($dbh,$tareaId,$alumId,$alumIdAcorregir);
    }
  }
}

function getMediaFromRetoIdClanId($dbh,$idTarea,$clanId)
{
  $aAlumnosIdClan = getAlumnosIdFromClanId($dbh,$clanId);
  $nTotalNotas = 0;
  foreach ($aAlumnosIdClan as $acAux) 
  {
    $alum = getAlumnoFromId($dbh,$acAux['ID_ALUMNO']);
    $dataAT = getDatosAlumnoTarea($dbh,$alum['CORREO'],$idTarea);
    $nTotalNotas += $dataAT['ESTRELLAS_CONSEGUIDAS'];
  }
  return $nTotalNotas/((count($aAlumnosIdClan)>0)?count($aAlumnosIdClan):1);
}

function modificarParesIncognitoAClanes($dbh,$tareaId,$aNull)
{
  if ($aNull)
  {
    modificarAlumnosTareasIncognitoANull($dbh,$tareaId);
  }
  else
  {
    // se recalculan todos los pares y se asignan al propio
    $aAlumRetos = getAlumnosTareasFromTarea($dbh,$tareaId);

    $aClanesIntroducidos = array ();
    foreach ($aAlumRetos as $alumnoR) 
    {
      $arrayAlumno=array();
      $clanId = getClanIdFromAlumnoId($dbh,$alumnoR['ID_ALUMNO']);
      if (($clanId!="")&&(!in_array($clanId, $aClanesIntroducidos)))
      {
        $aClanesIntroducidos[] = $clanId;
      }
    }
    shuffle($aClanesIntroducidos);
    for ($i=0; $i < count($aClanesIntroducidos); $i++) 
    { 
      $clanIdNext=$aClanesIntroducidos[($i+1)%count($aClanesIntroducidos)];
      $aAlumClanesNext = getAlumnosIdFromClanId($dbh,$clanIdNext);
      $idAlumnoConComent = -1;
      foreach ($aAlumClanesNext as $alclNext) 
      {
        $alTa = getDatosAlumnoTarea($dbh,getAlumnoFromId($dbh,$alclNext['ID_ALUMNO'])['CORREO'],$tareaId);
        if ($alTa['COMENTARIO']!=NULL AND $alTa['COMENTARIO']!='-')
        {
          $idAlumnoConComent = $alclNext['ID_ALUMNO'];
          break;
        }
      }
      if ($idAlumnoConComent == -1)
      {
        $idAlumnoConComent = $aAlumClanesNext[0]['ID_ALUMNO'];
      }
      $clanId=$aClanesIntroducidos[$i];
      $aAlumClanes = getAlumnosIdFromClanId($dbh  ,$clanId);
      foreach ($aAlumClanes as $alcl) 
      {
        modificarAlumnoTareaIncognito($dbh,$tareaId,$alcl['ID_ALUMNO'],$idAlumnoConComent);
      }
      
    }

  }
}                                                                                                                            
function modificarParesIncognitoAPropio($dbh,$tareaId,$aNull)
{
  if ($aNull)
  {
    modificarAlumnosTareasIncognitoANull($dbh,$tareaId);
  }
  else
  {
    // se recalculan todos los pares y se asignan al propio
    $aAlumRetos = getAlumnosTareasFromTarea($dbh,$tareaId);
    for ($i=0; $i < count($aAlumRetos); $i++) 
    { 
      $alumId=$aAlumRetos[$i]['ID_ALUMNO'];
      modificarAlumnoTareaIncognito($dbh,$tareaId,$alumId,$alumId);
    }
  }
}

function enviarCorreo($in_to,$in_subject,$in_message)
{
// enviar correo
  $success = false;
  try
  {
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    //$from = "no-reply@enhorabuenadelabuena.com";
    $from = "afsanchez@lasalleinstitucion.es";
    $to = $in_to;
    $subject = $in_subject;
    $message = $in_message;
    //echo "to:".$to;
    //echo "subject:".$subject;
    //echo "message:".$message;
    $headers = "From:" . $from;
    $success = mail($to,$subject,$message, $headers);
    if (!$success) {
      $errorMessage = error_get_last()['message'];
      echo 'e:'.$errorMessage;
    }
  }
  catch (Exception $ex)
  {
      echo "Error enviarCorreo:".$ex->getMessage();
      mi_info_log( "Error enviarCorreo:".$ex->getMessage());

  } 
  return $success;
}


function pOk($aF,$i,$j)
{
  //var_dump($aF);
  return ((($aF[$i]==$aF[$j])&&$aF[$j]!=-1)||
          (($aF[$i]==-2) || ($aF[$j]==-2)));
}

function getEstrellasPareja($aF,$nEstrellasPareja)
{

   return (pOk($aF,0,1)||pOk($aF,0,2)||pOk($aF,0,3)||pOk($aF,1,2)||pOk($aF,1,3)||pOk($aF,2,3))?$nEstrellasPareja:0;
}
function getEstrellasDoblePareja($aF,$nEstrellasDoblePareja)
{
   return (
    (pOk($aF,0,1)&&pOk($aF,2,3))||(pOk($aF,0,2)&&pOk($aF,1,3))||(pOk($aF,0,3)&&pOk($aF,1,2)))?$nEstrellasDoblePareja:0;
}
function getEstrellasTrio($aF,$nEstrellasTrio)
{
   return (pOk($aF,0,3)&&pOk($aF,2,3)&&pOk($aF,0,2)||pOk($aF,0,1)&&pOk($aF,1,2)&&pOk($aF,0,2)||pOk($aF,0,1)&&pOk($aF,1,3)&&pOk($aF,0,3)||pOk($aF,1,2)&&pOk($aF,2,3)&&pOk($aF,1,3))?$nEstrellasTrio:0;
}
function getEstrellasCuarteto($aF,$nEstrellasCuarteto)
{
   return (pOk($aF,0,1)&&pOk($aF,0,2)&&pOk($aF,1,3)&&pOk($aF,1,2)&&pOk($aF,2,3)&&pOk($aF,0,3))?$nEstrellasCuarteto:0;
}
function getEstrellasEscaleraSimple3($aF,$nEstrellasEscaleraSimple3,$fraccionImpe)
{
      $totalEstrellas = 0;
   if (($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1)))
   {
    $totalEstrellas=$nEstrellasEscaleraSimple3;
    if ($aF[0]==1)
    {
     $totalEstrellas=$totalEstrellas+round(($nEstrellasEscaleraSimple3/$fraccionImpe), 0);
    }
   }
   else if (($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1)))
   {
    $totalEstrellas=$nEstrellasEscaleraSimple3;
    if ($aF[1]==1)
    {
      $totalEstrellas=$totalEstrellas+round(($nEstrellasEscaleraSimple3/$fraccionImpe), 0);
    }   
   }
   return $totalEstrellas;
}
function getEstrellasEscaleraSimple4($aF,$nEstrellasEscaleraSimple4,$fraccionImpe)
{
   $totalEstrellas = 0;
   if (($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1)))
   {
    $totalEstrellas=$nEstrellasEscaleraSimple4;
    if ($aF[0]==1)
    {
      $totalEstrellas=$totalEstrellas+round(($nEstrellasEscaleraSimple4/$fraccionImpe), 0);
    }
   }
    return $totalEstrellas;
}
function getEstrellasEscalera3($aF,$aFI,$nEstrellasEscalera3,$fraccionImpe)
{

   $totalEstrellas = 0;
   if ((($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1)))&&(pOk($aFI,0,1)&&pOk($aFI,1,2)&&pOk($aFI,0,2)))
   {
    $totalEstrellas=$nEstrellasEscalera3;
    if ($aF[0]==1)
    {
      $totalEstrellas=$totalEstrellas+round(($nEstrellasEscalera3/$fraccionImpe), 0);
    }
   }
   else if ((($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1)))&&(pOk($aFI,1,2)&&pOk($aFI,2,3)&&pOk($aFI,1,3)))
   {
    $totalEstrellas=$nEstrellasEscalera3;
    if ($aF[1]==1)
    {
      $totalEstrellas=$totalEstrellas+round(($nEstrellasEscalera3/$fraccionImpe), 0);
    }

   
   }
    return $totalEstrellas;
}
function getEstrellasEscalera4($aF,$aFI,$nEstrellasEscalera4,$fraccionImpe)
{
   $totalEstrellas = 0;
   if ((($aF[1]==($aF[0]+1))&&($aF[2]==($aF[1]+1))&&($aF[3]==($aF[2]+1)))&&
    (pOk($aFI,0,1)&&pOk($aFI,0,2)&&pOk($aFI,1,3)&&pOk($aFI,1,2)&&pOk($aFI,2,3)&&pOk($aFI,0,3)))
   {
    $totalEstrellas=$nEstrellasEscalera4;
    if ($aF[0]==1)
    {
      $totalEstrellas=$totalEstrellas+round(($nEstrellasEscalera4/$fraccionImpe), 0);
    }
   }
  return $totalEstrellas;
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
function getEstrellasGanasCampActual($dbh,$correo,$fechaInicioCamp)
{
  $fechaInicioCamp = strtotime($fechaInicioCamp);
  $diaInicioCamp = date('Y-m-d', $fechaInicioCamp);
  $totalComportamiento = 0;
  $aToCompor = getEstrellasComportamientoFromCorreo($dbh,$correo);  
  foreach ($aToCompor as $compor) 
  {
    if ($compor['DIA']>$diaInicioCamp)
    {
      $totalComportamiento += $compor['ESTRELLAS'];
    }   
  }
  return $totalComportamiento;
}


function getEstrellasRetos($dbh,$correo,$examen)
{

    $totalRetos = 0;
  $aToRetos = getEstrellasRetosFromCorreo($dbh,$correo,$examen);
  foreach ($aToRetos as $ret) {
    $totalRetos += $ret['ESTRELLAS_CONSEGUIDAS'];
  }
  return $totalRetos ;
}
function getEstrellasRetosCampActual($dbh,$correo,$examen,$fechaInicioCamp)
{
  $totalRetos = 0;
  $aToRetos = getEstrellasRetosCampActualFromCorreo($dbh,$correo,$examen,$fechaInicioCamp);
  foreach ($aToRetos as $ret) {
    if ($ret['FECHA_CREACION']>$fechaInicioCamp)
    {
      $totalRetos += $ret['ESTRELLAS_CONSEGUIDAS'];
    }    
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
  $num_slotss = getAdminCromos($dbh)['NUM_SLOTS'];
  for ($i=0; $i < $num_slotss; $i++) 
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
$cromoi4 = getCromoFromID($dbh,$aCromosAlbum[$i*4]); 
$aAuxCromos[]=($aCromosAlbum[$i*4]!=-1)?$cromoi4['mana_w']:-1;
$cromoi41 = getCromoFromID($dbh,$aCromosAlbum[(1)+$i*4]);
$aAuxCromos[]=($aCromosAlbum[(1)+$i*4]!=-1)?$cromoi41['mana_w']:-1;
$cromoi42 = getCromoFromID($dbh,$aCromosAlbum[(2)+$i*4]);
$aAuxCromos[]=($aCromosAlbum[(2)+$i*4]!=-1)?$cromoi42['mana_w']:-1;
$cromoi43 = getCromoFromID($dbh,$aCromosAlbum[(3)+$i*4]);
$aAuxCromos[]=($aCromosAlbum[(3)+$i*4]!=-1)?$cromoi43['mana_w']:-1;

// si hay algún comodín se mete en id creador -2
if ($cromoi4['rarity']=="Mythic")
{
  $aAux[0]=-2;
}
if ($cromoi41['rarity']=="Mythic")
{
  $aAux[1]=-2;
}
if ($cromoi42['rarity']=="Mythic")
{
  $aAux[2]=-2;
}
if ($cromoi43['rarity']=="Mythic")
{
  $aAux[3]=-2;
}

    
    $filaI=NULL;
    if (isset($_POST['selfila_'.($i+1)])) 
    {
      $filaI = $_POST['selfila_'.($i+1)];
    }
    if (($filaI==NULL)&& (Count($vectorOrdenCombosAnt)>$i))
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
        $estreAux=getEstrellasEscaleraSimple3($aAuxRef,$adCromos['ESCALERASIMPLE3'],$adCromos['FRACCION_ESCA_IMPERIAL']);
        break;
      case '6':
        $estreAux=getEstrellasEscaleraSimple4($aAuxRef,$adCromos['ESCALERASIMPLE4'],$adCromos['FRACCION_ESCA_IMPERIAL']);
        break;
      case '7':
        $estreAux=getEstrellasEscalera3($aAuxRef,$aAux,$adCromos['ESCALERA3'],$adCromos['FRACCION_ESCA_IMPERIAL']);
        break;
      case '8':
        $estreAux=getEstrellasEscalera4($aAuxRef,$aAux,$adCromos['ESCALERA4'],$adCromos['FRACCION_ESCA_IMPERIAL']);
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
  $totalRetos = getEstrellasRetos($dbh,$correo,0);
  $totalConcursos = getEstrellasRetos($dbh,$correo,1);

  $totalComportamiento =getEstrellasGanas($dbh,$correo);
  $totalCromos = $estrellasCromos+$estrellasCombinaciones;
  $totalSuerte = getEstrellasBonos($dbh,$correo);
  return $totalRetos+$totalConcursos+$totalComportamiento+$totalCromos+$totalSuerte;
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


// Sanitizar el $_POST

//var_export($_POST);
// sanitiza el post de textos  de xss y comillas

foreach($_POST as $key => $value)
{
  $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  if(!is_array($_POST[$key]))
  {
    // el salto de linea se mantiene
    $_POST[$key]=str_replace("&lt;br/&gt;", "<br/>", $value);
  }
}
//echo "DESPUESSSSSSSSSSSS";
//var_export($_POST);





// INI Recalcular nivel alumno, no se hace para un administrador




if (isset($_SESSION['alogin']))
{
  if (!esUsernameAdministrador($dbh,$_SESSION['alogin']))
  {
    if (opcionMenuOk($dbh,$_SESSION['alogin'],"Jugar"))
    {
      $asignaturaAux=getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID_CURSO'])[0];
      $diaHoy = DateTime::createFromFormat('Y-m-d',date('Y-m-d'))->format('Y-m-d');
      if ($asignaturaAux['DIA_CROM_DIARIO']!=$diaHoy)
      {
      // para todos los de su clase genera un sitio aleatorio
      // si no tienen movilidad, una vez al día.
      $aAlumnosCurso = getAlumnosCompanerosCursoFromCorreo($dbh,$_SESSION['alogin']);
      foreach ($aAlumnosCurso as $alumno) 
      {  
        $getPropsAlummo =  getPropsVisiblesbot($dbh,$alumno['CORREO']);
        if ($getPropsAlummo['movilidad']!=1)
        {
          $aallSitios = getAllSitiosVisibles($dbh);
          $sitioElegido = $aallSitios[rand(0,Count($aallSitios)-1)];
          $posx = rand($sitioElegido['INI_X'],$sitioElegido['MAX_X']);
          $posy = rand($sitioElegido['INI_Y'],$sitioElegido['MAX_Y']);
          $cont = 1000;
          while (existeLugar($dbh,$sitioElegido['ID'],$posx,$posy)) 
          {
              $posx = rand($sitioElegido['INI_X'],$sitioElegido['MAX_X']);
              $posy = rand($sitioElegido['INI_Y'],$sitioElegido['MAX_Y']);
              $cont--;
              if ($cont==0)
              {
                $cont = 1000;
                $sitioElegido = $aallSitios[rand(0,Count($aallSitios)-1)];
              }
          };
          modificarLocalizacionBot($dbh,$alumno['CORREO'], $sitioElegido['ID_MAP'], $posx, $posy);
        }
      }
      
modificarDiaCromDiarioAsignatura($dbh,$asignaturaAux['ID'],$diaHoy);
     }
    }



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
        // solo subir el nivel de estrellas hasta nivel 6
        if ($nivelReal<=6)
        {
          modificarNEstrellasCromo($dbh,$_SESSION['alogin'],$nivelReal);
        }
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

function getValorAtributo($dbh,$CORREO)
{

// si el atributoAux del cromo es diferente de NULL, coge ese valor si no
// utiliza el siguiente código,por ejemplo las ganas desde la semana pasada
$cromoI = getCromo($dbh,$CORREO);
if ($cromoI['ATRIBUTO_AUX']!=NULL)
{
  return (($cromoI['ATRIBUTO_AUX']>=0)?"+":"").$cromoI['ATRIBUTO_AUX']; 
}
else
{

$hoy = date('Y-m-d');
$date = DateTime::createFromFormat('Y-m-d',$hoy);
$date->modify('-7 day'); // una semana atrás
$mayorQueDia = $date->format('Y-m-d');
$aFilasEstrellas = getEstrellasMayorQueDiaFromCorreo($dbh,$CORREO,$mayorQueDia);
if (Count($aFilasEstrellas)>0)
{


  $nEstrellas = 0;  
  foreach ($aFilasEstrellas as $filaEst) 
  {
    $nEstrellas =$nEstrellas + $filaEst['ESTRELLAS'];
  }
  $totalSesiones = (Count($aFilasEstrellas)*4);
  $porcentaje = $nEstrellas/$totalSesiones;
  $valorAtributo = 3;
  if ($porcentaje<0.2)
  {
    $valorAtributo = -2;
  }
  else if ($porcentaje<=0.3)
  {
    $valorAtributo = -1;
  }
  else if ($porcentaje<0.5)
  {
    $valorAtributo = 0;
  }
  else if ($porcentaje<0.6)
  {
    $valorAtributo = 1;
  }
  else if ($porcentaje<0.8)
  {
    $valorAtributo = 2;
  }
  else if ($porcentaje==1)
  {
    $valorAtributo = 4;
  }
  return (($valorAtributo>=0)?"+":"").$valorAtributo; 
}
else
{
  return "";
}

}

}
function getValorAtributoDesdeTest($posI,$total,$minAtri,$maxAtri)
{

return intval(($total-$posI)/($total/($maxAtri-$minAtri+1)))+$minAtri;

}
 
function manageUtilizacionEvento($dbh,$correoJugador,$idEvento)
{
  $esJugable=1;
  $idAlumno = getAlumnoFromCorreo($dbh,$correoJugador)['ID'];
  $evento= getEventoFromID($dbh,$idEvento);
  $listaUtilizacionesHoyDB= $evento['LISTA_UTILIZACIONES_HOY'];
  $listaUtilizacionesHoy = explode(",", $listaUtilizacionesHoyDB);
  if ((Count($listaUtilizacionesHoy)>0)&&(date('Y-m-d')==$listaUtilizacionesHoy[0]))
  {
    //ya jugada?
    $contUtils= 0;
    for ($i=1; $i < Count($listaUtilizacionesHoy) ; $i++) 
    {
        if ($idAlumno==$listaUtilizacionesHoy[$i])
        {
          $contUtils++;
        }
    }
    // no se permite más utilizaciones y no hace falta modificar.
    if ($contUtils>=$evento['N_UTILIZACIONES_DIA'])
    {
      $esJugable=0;
    }
    else
    { 
      modificarListaUtilizacionesEvento($dbh,$idEvento,$listaUtilizacionesHoyDB.",".$idAlumno);
    }
        
  }
  else if ($evento['N_UTILIZACIONES_DIA']>0)
  {
    modificarListaUtilizacionesEvento($dbh,$idEvento,date('Y-m-d').",".$idAlumno);
  }
  else
  {
    $esJugable=0;
  }
  return $esJugable;
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
function getPropsVisiblesBot($dbh,$correo)
{
  $aProps = [
      "saludo" => 0,
      "palabra_clave" => 0,
      "movilidad" => 0,
      "velocidad_1" => 0,
      "velocidad_2" => 0,
      "saltando" => 0,
      "fantasma" => 0,
      "localizacion" => 0,
      "personajes" => 0,
      "ppt1" => 0
  ];
  $alumno = getAlumnoFromCorreo($dbh,$correo);
  $nivelAlumno = $alumno['NUMERO_NIVEL'];
  if ($nivelAlumno>=1)
  {
    $aProps['saludo']=1;
    $aProps['palabra_clave']=1;    
  }
  if ($nivelAlumno>=4)
  {
    $aProps['movilidad']=1;   
  }
  if ($nivelAlumno>=5)
  {
    $aProps['velocidad_1']=1;   
    $aProps['personajes']=1;   
  }
  if ($nivelAlumno>=6)
  {
    $aProps['saltando']=1;   
    $aProps['ppt1']=1;   
  }
  if ($nivelAlumno>=7)
  {
    $aProps['localizacion']=1;   
  }
  if ($nivelAlumno>=8)
  {
    $aProps['fantasma']=1;   
    $aProps['velocidad_2']=1;   
  }

  
  return $aProps;

}
function remove_accents($string) {
    $string = preg_replace('/\s+/', '', $string);

    if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

    $chars = array(
    // Decompositions for Latin-1 Supplement
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );

    $string = strtr($string, $chars);
    
    return $string;
}


function validateCromos($db, $sCorreo, $sOrdenTotal) 
{
  // validar número cromos
  $aCromosDB = getCromosDeAlbum($db,$sCorreo);
  $aCromosAlbum = explode(",", $sOrdenTotal);
  $nCromosAlb= 0;
  foreach ($aCromosAlbum as $idCromo) 
  {
    if ($idCromo!=-1)
    {
      $nCromosAlb++;
    }
  }
  if (count($aCromosDB)!=$nCromosAlb)
  {
    return false;
  }
  // validar que son exactemente los de base de datos
  foreach ($aCromosAlbum as $idCromo) 
  {
    if ($idCromo!=-1)
    {
        foreach ($aCromosDB as $key => $cromoI) 
        {
            if ($cromoI['ID']==$idCromo)
            {
              unset($aCromosDB[$key]);
              break;
            }
        }
    }
  }

  return (count($aCromosDB)==0);


}
?>