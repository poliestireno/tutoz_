<?php

$array_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/sallez.ini');

//print_r($array_ini);


require_once("logfiles.php");
require_once("phputils.php");
function conectarDB()
{
  try
  {

   // $db= new PDO('mysql:host=localhost;dbname=sallex2;charset=utf8mb4','root','');
  $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

    //$db = new PDO('mysql:host=localhost;dbname=u329316246_sallex;charset=utf8mb4', 'u329316246_gilbertSallex', 'gilbert7');

    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  catch (PDOException $ex)
  {
      echo "Error conectar:".$ex->getMessage();
  }  
}

function mandarNotificacion($db,$remitente,$receptor,$mensaje)
{
$sqlnoti="insert into notification (notiuser,notireciver,notitype,readit) values (:notiuser,:notireciver,:notitype,0)";
    $querynoti = $db->prepare($sqlnoti);
    $querynoti-> bindParam(':notiuser', $remitente, PDO::PARAM_STR);
    $querynoti-> bindParam(':notireciver',$receptor, PDO::PARAM_STR);
    $querynoti-> bindParam(':notitype', $mensaje, PDO::PARAM_STR);
    $querynoti->execute();    
} 

function hayEstrellasAsigDia($db, $IDAsig, $dia)
{
   try 
  {
  $stmt = $db->query("SELECT COUNT(*) TOTAL FROM ESTRELLAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA=".$IDAsig);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila['TOTAL']>0;
 
}


function insertMasivoFaltasAsigDia($db,$nSesiones,$IDAsig, $dia,$grado,$nivel)
{
  $vectorID = getAlumnosGradoNivel($db,$grado,$nivel); // ID por curso
  //var_dump($vectorID);
  
  foreach($vectorID as $alumno){
    insertarFalta($db,$alumno["ID"],$IDAsig,$nSesiones,$dia);
  }
}
function insertMasivoEstrellasAsigDia($db,$nEstrellas,$IDAsig, $dia,$grado,$nivel)
{
  $vectorID = getAlumnosGradoNivel($db,$grado,$nivel); // ID por curso
  //var_dump($vectorID);
  
  foreach($vectorID as $alumno){
    insertarEstrella($db,$alumno["ID"],$IDAsig,$nEstrellas,$dia);
  }
}
function modificarMasivoEstrellasAsigDia($db,$nEstrellas,$IDAsig, $dia,$grado,$nivel)
{
  $vectorID = getAlumnosGradoNivel($db,$grado,$nivel); // ID por curso
  //var_dump($vectorID);
  
  foreach($vectorID as $alumno){
    modificarEstrella($db,$alumno["ID"],$IDAsig,$nEstrellas,$dia);
  }
}
function modificarEstrella($db,$IDAlumno,$IDAsignatura,$nEstrellas,$dia)
{
  try 
  {
    $sql = "UPDATE ESTRELLAS SET ESTRELLAS='".$nEstrellas."' where ID_ASIGNATURA=".$IDAsignatura." AND DIA='".$dia."' AND ID_ALUMNO = ".$IDAlumno;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
}


function insertarAlumnoTarea($db,$idAlumno,$tareaidselect,$estado, $estrellasconseguidas,$fecha)
{
  $sentencia= "INSERT INTO ALUMNOS_TAREAS (ID_ALUMNO, ID_TAREA, ESTADO, ESTRELLAS_CONSEGUIDAS, FECHA)
              VALUES ( :alumno, :tarea, :estado, :estrellas, :fecha)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':alumno',$idAlumno);
  $stmt->bindParam(':tarea',$tareaidselect);
  $stmt->bindParam(':estado',$estado);
  $stmt->bindParam(':estrellas',$estrellasconseguidas);
  $stmt->bindParam(':fecha',$fecha);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    echo "Error inserción estrella:".$ex->getMessage();
}  
}

function insertarEstrella($db,$IDAlumno,$IDAsignatura,$nEstrellas,$dia)
{
  $sentencia= "INSERT INTO ESTRELLAS ( DIA, ID_ALUMNO, ID_ASIGNATURA, ESTRELLAS)
              VALUES ( :dia, :alumno, :asignatura, :estrellas)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':dia',$dia);
  $stmt->bindParam(':alumno',$IDAlumno);
  $stmt->bindParam(':asignatura',$IDAsignatura);
  $stmt->bindParam(':estrellas',$nEstrellas);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    echo "Error inserción estrella:".$ex->getMessage();
}  
}
function insertarReto($db,$asignatura,$name,$totalestrellas,$descrip,$selSitios,$posx,$posy,$linkdocumento,$fechalimite)
{
  $sentencia= "INSERT INTO TAREAS ( ID_ASIGNATURA, NOMBRE, TOTAL_ESTRELLAS, ID_SITIO, POS_X, POS_Y, DESCRIPCION,LINK_DOCUMENTO,FECHA_LIMITE)
              VALUES ( :ID_ASIGNATURA, :NOMBRE, :TOTAL_ESTRELLAS, :ID_SITIO, :POS_X, :POS_Y, :DESCRIPCION, :LINK_DOCUMENTO, :FECHA_LIMITE)";
  try
  {
    $stmt = $db->prepare($sentencia);
    $stmt->bindParam(':ID_ASIGNATURA',$asignatura);
    $stmt->bindParam(':NOMBRE',$name);
    $stmt->bindParam(':TOTAL_ESTRELLAS',$totalestrellas);
    $stmt->bindParam(':ID_SITIO',$selSitios);
    $stmt->bindParam(':POS_X',$posx);
    $stmt->bindParam(':POS_Y',$posy);
    $stmt->bindParam(':DESCRIPCION',$descrip);
    $stmt->bindParam(':LINK_DOCUMENTO',$linkdocumento);
    $stmt->bindParam(':FECHA_LIMITE',$fechalimite);
    $stmt->execute();
  }
  catch (Exception $ex)
  {
      echo "Error insertarReto:".$ex->getMessage();
  }  
}



function insertarCromo($db,$ID_CREADOR,$ID_POSEEDOR,$GENERADO, $setId, $name, $color, $mana_w, $picture, $cardtype, $rarity, $cardtext, $power, $toughness, $artist, $bottom)
{
  $sentencia= "INSERT INTO CROMOS(ID_SET, ID_CREADOR, ID_POSEEDOR, GENERADO, name, color, mana_w, picture, cardtype, rarity, cardtext, power, toughness, artist, bottom) VALUES (:setId, :ID_CREADOR,:ID_POSEEDOR,:GENERADO, :name, :color, :mana_w, :picture, :cardtype, :rarity, :cardtext, :power, :toughness, :artist, :bottom)";
  //echo $sentencia;
  try
  {
      $stmt = $db->prepare($sentencia);
      $stmt->bindParam(':setId',$setId);
      $stmt->bindParam(':ID_CREADOR',$ID_CREADOR);
      $stmt->bindParam(':ID_POSEEDOR',$ID_POSEEDOR);
      $stmt->bindParam(':GENERADO',$GENERADO);
      $stmt->bindParam(':name',$name);
      $stmt->bindParam(':color',$color);
      $stmt->bindParam(':mana_w',$mana_w);
      $stmt->bindParam(':picture',$picture);
      $stmt->bindParam(':cardtype',$cardtype);
      $stmt->bindParam(':rarity',$rarity);
      $stmt->bindParam(':cardtext',$cardtext);
      $stmt->bindParam(':power',$power);
      $stmt->bindParam(':toughness',$toughness);
      $stmt->bindParam(':artist',$artist);
      $stmt->bindParam(':bottom',$bottom);
      $stmt->execute();
    }
    catch (PDOException $ex)
    {
        echo "Error inserción cromo:".$ex->getMessage();
    }  
}

function insertarActor($db,$PV, $PM, $PT, $CALAS)
{
  $sentencia= "INSERT INTO MIACTOR(PV, PM, PT, CALAS) VALUES (:PV, :PM, :PT, :CALAS)";
  //echo $sentencia;
  try
  {
      $stmt = $db->prepare($sentencia);
      $stmt->bindParam(':PV',$PV);
      $stmt->bindParam(':PM',$PM);
      $stmt->bindParam(':PT',$PT);
      $stmt->bindParam(':CALAS',$CALAS);
      $stmt->execute();
    }
    catch (PDOException $ex)
    {
        echo "Error inserción bot:".$ex->getMessage();
    }  
}
function insertarBot($db,$SALUDO,$DESCRIPCION,$PALABRA_CLAVE,$MOVILIDAD,$VELOCIDAD,$ID_MAPA,$FANTASMA,$SALTANDO,$PERSONAJE,$PORCENT_PPT,$POSTURA1,$POSTURA2,$POSTURA3,$LISTA_JUGADAS_HOY_PTT)
{
  $sentencia= "INSERT INTO MIBOT(SALUDO,DESCRIPCION,PALABRA_CLAVE,MOVILIDAD,VELOCIDAD,ID_MAPA,FANTASMA,SALTANDO,PERSONAJE,PORCENT_PPT,POSTURA1,POSTURA2,POSTURA3,LISTA_JUGADAS_HOY_PTT) VALUES (:SALUDO,:DESCRIPCION,:PALABRA_CLAVE,:MOVILIDAD,:VELOCIDAD,:ID_MAPA,:FANTASMA,:SALTANDO,:PERSONAJE,:PORCENT_PPT,:POSTURA1,:POSTURA2,:POSTURA3,:LISTA_JUGADAS_HOY_PTT)";
  //echo $sentencia;
  try
  {
      $stmt = $db->prepare($sentencia);
      $stmt->bindParam(':SALUDO',$SALUDO);
      $stmt->bindParam(':DESCRIPCION',$DESCRIPCION);
      $stmt->bindParam(':PALABRA_CLAVE',$PALABRA_CLAVE);
      $stmt->bindParam(':MOVILIDAD',$MOVILIDAD);
      $stmt->bindParam(':VELOCIDAD',$VELOCIDAD);
      $stmt->bindParam(':ID_MAPA',$ID_MAPA);
      $stmt->bindParam(':FANTASMA',$FANTASMA);
      $stmt->bindParam(':SALTANDO',$SALTANDO);
      $stmt->bindParam(':PERSONAJE',$PERSONAJE);
      $stmt->bindParam(':PORCENT_PPT',$PORCENT_PPT);
      $stmt->bindParam(':POSTURA1',$POSTURA1);
      $stmt->bindParam(':POSTURA2',$POSTURA2);
      $stmt->bindParam(':POSTURA3',$POSTURA3);
      $stmt->bindParam(':LISTA_JUGADAS_HOY_PTT',$LISTA_JUGADAS_HOY_PTT);
      $stmt->execute();
    }
    catch (PDOException $ex)
    {
        echo "Error inserción bot:".$ex->getMessage();
    }  
}

function borrarFaltasAsignaturaDiaAlumno($db,$IDAsignatura,$dia,$alumno)
{
 try 
  {
   $sql = "DELETE FROM FALTAS WHERE ID_ASIGNATURA=".$IDAsignatura." AND DIA='".$dia."' AND ID_ALUMNO =".$alumno;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
}
function borrarFaltasAsignaturaDia($db,$IDAsignatura,$dia)
{
 try 
  {
   $sql = "DELETE FROM FALTAS WHERE ID_ASIGNATURA=".$IDAsignatura." AND DIA='".$dia."' AND ID_ALUMNO <> -1";
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
}
function borrarFaltasAsignaturaDiaYFantasma($db,$IDAsignatura,$dia)
{
 try 
  {
   $sql = "DELETE FROM FALTAS WHERE ID_ASIGNATURA=".$IDAsignatura." AND DIA='".$dia."'";
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
}
function insertarFalta($db,$IDAlumno,$IDAsignatura,$nSesiones,$dia)
{
  $sentencia= "INSERT INTO FALTAS ( DIA, ID_ALUMNO, ID_ASIGNATURA, SESIONES, ELEGIDOS,CONT_ELEG)
              VALUES ( :dia, :alumno, :asignatura, :sesiones, :elegIDos, :cont_eleg)";
  try
  {
  $vacia = "";
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':dia',$dia);
  $stmt->bindParam(':alumno',$IDAlumno);
  $stmt->bindParam(':asignatura',$IDAsignatura);
  $stmt->bindParam(':sesiones',$nSesiones);
  $stmt->bindParam(':elegIDos',$vacia);
  $stmt->bindParam(':cont_eleg',$vacia);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    echo "Error inserción falta:".$ex->getMessage();
}  
}
function insertarAlumno($db,$nombre,$apellIDo1,$apellIDo2,$IDCurso)
{
  $sentencia= "INSERT INTO ALUMNOS ( NOMBRE, APELLIDO1, APELLIDO2, ID_CURSO)
              VALUES ( :nombre, :apellIDo1, :apellIDo2, :IDCurso)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':nombre',$nombre);
  $stmt->bindParam(':apellIDo1',$apellIDo1);
  $stmt->bindParam(':apellIDo2',$apellIDo2);
  $stmt->bindParam(':IDCurso',$IDCurso);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    echo "Error inserción alumno:".$ex->getMessage();
}  
}


function getAlumnosFaltones($db){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT ID_ALUMNO,(SELECT nombre FROM ALUMNOS 
    WHERE FALTAS.ID_ALUMNO =ALUMNOS.ID) AS Nombre,
    (SELECT APELLIDO1 FROM ALUMNOS WHERE FALTAS.ID_ALUMNO =ALUMNOS.ID)
    AS Apellido, SUM(SESIONES) AS NFALTAS FROM FALTAS WHERE FALTAS.ID_ALUMNO <> -1 GROUP BY ID_ALUMNO ORDER BY 
    NFALTAS DESC LIMIT 10");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error inserción alumno:".$ex->getMessage();
  }
  return $vectorTotal;
}
function getAlumnosInforme($db,$IDAsignatura){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT ID_ALUMNO,(SELECT nombre FROM ALUMNOS 
    WHERE FALTAS.ID_ALUMNO =ALUMNOS.ID) AS Nombre,
    (SELECT APELLIDO1 FROM ALUMNOS WHERE FALTAS.ID_ALUMNO =ALUMNOS.ID)
    AS Apellido, SUM(SESIONES) AS NFALTAS FROM FALTAS WHERE FALTAS.ID_ALUMNO <> -1 AND
    FALTAS.ID_ASIGNATURA =".$IDAsignatura." GROUP BY ID_ALUMNO ORDER BY 
    NFALTAS DESC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error getAlumnosInforme:".$ex->getMessage();
  }
  return $vectorTotal;
}
function setNotificationsOff($db,$notireciver)
{
  try 
  {
    $sql = "UPDATE notification SET readit=1  where notireciver='".$notireciver."' AND readit=0";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! cambiarNivelAlumno ".$ex->getMessage();
  }   
}

function getNotificationsGenerales($db,$notireciver,$fechaUltimaLeida){
  $vectorTotal = array();
  try{
    $stmt = $db->query("select * from notification where notireciver='".$notireciver."' AND '".$fechaUltimaLeida."' < time ORDER BY time");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error getNotificationsOff:".$ex->getMessage();
  }
  return $vectorTotal;
}
function getNotificationsOff($db,$notireciver){
  $vectorTotal = array();
  try{
    $stmt = $db->query("select * from notification where notireciver='".$notireciver."' AND readit=0 ORDER BY time");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error getNotificationsOff:".$ex->getMessage();
  }
  return $vectorTotal;
}
function getAlumnosInformeEstrellas($db,$IDAsignatura){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT ID_ALUMNO,(SELECT nombre FROM ALUMNOS 
    WHERE ESTRELLAS.ID_ALUMNO =ALUMNOS.ID) AS Nombre,
    (SELECT APELLIDO1 FROM ALUMNOS WHERE ESTRELLAS.ID_ALUMNO =ALUMNOS.ID)
    AS Apellido, SUM(ESTRELLAS) AS NFALTAS FROM ESTRELLAS WHERE ESTRELLAS.ID_ALUMNO <> -1 AND
    ESTRELLAS.ID_ASIGNATURA =".$IDAsignatura." GROUP BY ID_ALUMNO ORDER BY 
    NFALTAS DESC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error getAlumnosInformeEstrellas:".$ex->getMessage();
  }
  return $vectorTotal;
}

function getCursoFromCursoID($db,$idCurso){
  try 
  {
  $stmt = $db->query("SELECT * FROM CURSOS WHERE ID= ".$idCurso);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila;
}

function getNivelFromNumeroNivel($db,$correo,$numeroNivel){
  try 
  {

  $alumno = getAlumnoFromCorreo($db,$correo);
  $asignaturas = getAsignaturasFromCurso($db,$alumno['ID_CURSO']);
  // cogemos la primera asignatura del alumno para coger los niveles.
  $stmt = $db->query("SELECT * FROM NIVELES WHERE NUMERO= ".$numeroNivel." AND CATEGORIA_NIVEL='".$asignaturas[0]['CATEGORIA_NIVEL']."'");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured in getNivelFromNumeroNivel ".$ex->getMessage();
  } 
  return $fila;
}


function getAlumnosCompanerosCursoFromCorreo($db,$correo)
{
  $idCurso = getAlumnoFromCorreo($db,$correo)['ID_CURSO'];
  $curso = getCursoFromCursoID($db,$idCurso);
  return getAlumnosGradoNivel($db,$curso['GRADO'],$curso['NIVEL']);
}

function getAlumnosFromCursoID($db,$idCurso)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM ALUMNOS WHERE ID <> -1 AND ID_CURSO=".$idCurso);   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getAlumnosFromCursoID:".$ex->getMessage();
  }
    return $vectorTotal;
}
function getAlumnosGradoNivel($db,$grado,$nivel)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM ALUMNOS WHERE ID <> -1 AND ID_CURSO= 
    (SELECT ID FROM CURSOS WHERE GRADO='".$grado."' AND NIVEL=".$nivel.")");   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getAlumnosGradoNivel:".$ex->getMessage();
  }
    return $vectorTotal;
}
function getCromosDeAlbum($db,$correo)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM CROMOS WHERE ID_POSEEDOR =".getAlumnoFromCorreo($db,$correo)['ID']);   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getCromosDeAlbum:".$ex->getMessage();
  }
    return $vectorTotal;
}

function getCursosPersonajesFromCursoID($db,$idCurso)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM CURSOS_PERSONAJES WHERE ID_CURSO_PADRE=".$idCurso);   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getCursosPersonajesFromCursoID:".$ex->getMessage();
  }
    return $vectorTotal;
}

function getAndSetRandomCromo($db,$setId)
{
  $vectorTotal = array();
  try
  {     
    
    $sql="SELECT * FROM CROMOS WHERE GENERADO = 0 AND ID_SET = ".$setId;
    $stmt = $db->query($sql);   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
    if (Count($vectorTotal)==0)
    {
      return NULL;
    }   
    $nRand =  rand(0, sizeof($vectorTotal)-1);
    $cromoElegido = $vectorTotal[$nRand];
    if ($cromoElegido==NULL)
    {
      return NULL;
    }
    modificarGeneradoCromo($db,1, $cromoElegido['ID']);

  }
  catch (PDOException $ex)
  {
    echo "Error en getAndSetRandomCromo:".$ex->getMessage();
  }
  return $cromoElegido;
}

function getNumeroSesionesFromAsignatura($db,$IDAsignatura)
{
  try 
  {
  $stmt = $db->query("SELECT sum(SESIONES) NSES FROM FALTAS WHERE ID_ALUMNO=-1 and ID_ASIGNATURA = ".$IDAsignatura);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila['NSES'];
}
function getNombreCursoFromID($db,$IDCurso)
{
  try 
  {
    $stmt = $db->query("SELECT GRADO,NIVEL FROM CURSOS WHERE ID=".$IDCurso);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila['GRADO'].$fila['NIVEL'];
}
function getAsignaturaFromAsignaturaID($db,$IDAsignatura)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM ASIGNATURAS WHERE ID=".$IDAsignatura);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! getAsignaturaFromAsignaturaID".$ex->getMessage();
  } 
  return $fila;
}
function getDatosAlumnoTarea($db,$correo,$idTarea)
{
  try 
  {
  $sql="SELECT * FROM ALUMNOS_TAREAS WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." AND ID_TAREA = ".$idTarea;
  $stmt = $db->query($sql);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! getDatosAlumnoTarea".$ex->getMessage();
  } 
  return $fila;
}


function getAsignaturasConCurso($db)
{
    $vectorTotal = array();
  try
  {
    $stmt = $db->query("SELECT NOMBRE,ID_CURSO,ID FROM ASIGNATURAS");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $nombreCurso = getNombreCursoFromID($db,$fila['ID_CURSO']);
      $vectorTotal [] = $nombreCurso."*".$fila['NOMBRE']."--".$fila['ID'];
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getAsignaturasConCurso:".$ex->getMessage();
  }
  return $vectorTotal;
}
function getSitios($db)
{
    $vectorTotal = array();
  try
  {
    $stmt = $db->query("SELECT NOMBRE_VISUAL,CODIGO,ID FROM SITIOS");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila['NOMBRE_VISUAL']."*".$fila['CODIGO']."--".$fila['ID'];
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getAsignaturasConCurso:".$ex->getMessage();
  }
  return $vectorTotal;
}

function getAsignaturasFromCurso($db,$id_curso)
{
  $vectorTotal = array();
  try
  {
    $stmt = $db->query("SELECT * FROM ASIGNATURAS WHERE ID_CURSO = ".$id_curso);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getAsignaturasFromCurso:".$ex->getMessage();
  }
  return $vectorTotal;
}

function getNivelesFromCategoria($db,$nombre_categoria)
{
  $vectorTotal = array();
  try
  {
    $stmt = $db->query("SELECT * FROM NIVELES WHERE CATEGORIA_NIVEL= '".$nombre_categoria."' ORDER BY NUMERO");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getNivelesFromCategoria:".$ex->getMessage();
  }
  return $vectorTotal;
}
function getCursosGradoNivel($db)
{
  $vectorTotal = array();
  try
  {
    $stmt = $db->query("SELECT GRADO,NIVEL FROM CURSOS");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getCursosGradoNivel:".$ex->getMessage();
  }
    return $vectorTotal;
  
  //SELECT * FROM ALUMNOS WHERE ID_CURSO= (SELECT ID FROM CURSOS WHERE GRADO='DAM' AND NIVEL=2)
}
function getFaltasAsignaturaClase($db,$diaPasado,$IDPasado)
{
  $vectorTotal = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM FALTAS WHERE DIA = '".$diaPasado."' AND ID_ASIGNATURA = ".$IDPasado." AND ID_ALUMNO <> -1");
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getFaltasAsignaturaClase:".$ex->getMessage();
  }
    return $vectorTotal;  
}

function getAlumnoFromID($db,$IDAlumno)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM ALUMNOS WHERE ID=".$IDAlumno);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila;
}
function getMiBotFromAlumnoID($db,$IDAlumno)
{
  try 
  {   
    $stmt = $db->query("SELECT * FROM MIBOT WHERE ID = (SELECT ID_MIBOT FROM ALUMNOS WHERE ID=".$IDAlumno.") LIMIT 1");
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! getMiBotFromAlumnoID ".$ex->getMessage();
  } 
  return $fila;
}
function getMiActorFromAlumnoID($db,$IDAlumno)
{
  try 
  {   
    $stmt = $db->query("SELECT * FROM MIACTOR WHERE ID = (SELECT ID_MIACTOR FROM ALUMNOS WHERE ID=".$IDAlumno.") LIMIT 1");
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! getMiActorFromAlumnoID ".$ex->getMessage();
  } 
  return $fila;
}
function getAdminCromos($db)
{
  try 
  {
  $stmt = $db->query("SELECT * from ADMIN_CROMOS LIMIT 1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila;
}


function getNumeroSesionesEstrellasFromAsignatura($db,$IDAsignatura){
  try 
  {
  $stmt = $db->query("SELECT COUNT(DISTINCT DIA) NSES FROM ESTRELLAS WHERE ID_ASIGNATURA=".$IDAsignatura);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila['NSES'];
}
function getCursoFromAsignaturaID($db,$IDAsignatura){
  try 
  {
  $stmt = $db->query("SELECT * FROM CURSOS WHERE ID=(SELECT ID_CURSO FROM ASIGNATURAS WHERE ID=".$IDAsignatura.") LIMIT 1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila;
}


function getSetsCromos($db)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM SETS_CROMOS");   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getSetsCromos:".$ex->getMessage();
  }
    return $vectorTotal;
}
function getCursosFromIDSet($db,$idSet)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM CURSOS_SETS WHERE ID_SET=".$idSet);   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en getCursosFromIDSet:".$ex->getMessage();
  }
    return $vectorTotal;
}



function getAlumnoFromCorreo($db,$CORREO)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM ALUMNOS WHERE CORREO='".$CORREO."'");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured getAlumnoFromCorreo ! ".$ex->getMessage();
  } 
  return $fila;

}


function getSetCromoFromIdCromo($db,$idCromo,$correo){
  try 
  {
  $stmt = $db->query("SELECT * FROM CROMOS WHERE ID = ".$idCromo." LIMIT 1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  modificarPoseedorCromo($db, getAlumnoFromCorreo($db,$correo)['ID'], $fila['ID']);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured getCromo ! ".$ex->getMessage();
  } 
  return $fila;
}

function getCromo($db,$CORREO){
  try 
  {
  $stmt = $db->query("SELECT * FROM CROMOS WHERE ID_CREADOR = (SELECT ID FROM ALUMNOS WHERE CORREO='".$CORREO."') LIMIT 1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured getCromo ! ".$ex->getMessage();
  } 
  return $fila;
}
function getBot($db,$CORREO){
  try 
  {
  $stmt = $db->query("SELECT * FROM MIBOT WHERE ID = (SELECT ID_MIBOT FROM ALUMNOS WHERE CORREO='".$CORREO."') LIMIT 1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured getBot ! ".$ex->getMessage();
  } 
  return $fila;
}
function getCromoFromID($db,$idCromo){
  try 
  {
  $stmt = $db->query("SELECT * FROM CROMOS WHERE ID = ".$idCromo);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured getCromoFromID ! ".$ex->getMessage();
  } 
  return $fila;
}
function getSitioFromID($db,$idSitio){
  try 
  {
  $stmt = $db->query("SELECT * FROM SITIOS WHERE ID = ".$idSitio);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured getSitioFromID ! ".$ex->getMessage();
  } 
  return $fila;
}
function getTareasFromAlumno($db,$correo)
{
  $alumno = getAlumnoFromCorreo($db,$correo);
  $asignaturas = getAsignaturasFromCurso($db,$alumno['ID_CURSO']);
  $vectorTotal = array();
  try
  {
  foreach ($asignaturas as $asig) {
    $stmt = $db->query("SELECT * FROM TAREAS WHERE ID_ASIGNATURA = ".$asig['ID']);
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
     
  }
  catch (PDOException $ex)
  {
    echo "Error en getTareasFromAlumno:".$ex->getMessage();
  }
  return $vectorTotal;  
}

function getSetCromosIdFromAlumno($db,$CORREO){
  try 
  {
  $stmt = $db->query("SELECT ID_SET FROM CURSOS_SETS WHERE ID_CURSO = (SELECT ID_CURSO FROM ALUMNOS WHERE CORREO='".$CORREO."') LIMIT 1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured getCromo ! ".$ex->getMessage();
  } 
  return $fila['ID_SET'];
}

function getNumeroTotalAlumnos($db){
  try 
  {
  $stmt = $db->query("SELECT COUNT(*) TOTAL FROM ALUMNOS WHERE ID <> -1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila['TOTAL'];
}
function getNumeroCromosTotalesFromIDSet($db,$idSet){
  try 
  {

  $stmt = $db->query("SELECT COUNT(*) TOTAL FROM CROMOS WHERE ID_SET=".$idSet);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! getNumeroCromosTotalesFromIDSet".$ex->getMessage();
  } 
  return $fila['TOTAL'];
}
function getNumeroCromosAbiertosFromIDSet($db,$idSet){
  try 
  {
  $stmt = $db->query("SELECT COUNT(*) TOTAL FROM CROMOS WHERE ID_POSEEDOR IS NOT NULL AND ID_SET=".$idSet);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! getNumeroCromosAbiertosFromIDSet".$ex->getMessage();
  } 
  return $fila['TOTAL'];
}


function getNumeroCromosDisponiblesFromIDSet($db,$idSet){
  try 
  {
  $stmt = $db->query("SELECT COUNT(*) TOTAL FROM CROMOS WHERE GENERADO=0 AND ID_SET=".$idSet);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! getNumeroCromosSinAbrirFromIDSet".$ex->getMessage();
  } 
  return $fila['TOTAL'];
}
function getNumeroCromosSinAbrirFromIDSet($db,$idSet){
  try 
  {
  $stmt = $db->query("SELECT COUNT(*) TOTAL FROM CROMOS WHERE GENERADO=1 AND ID_POSEEDOR IS NULL AND ID_SET=".$idSet);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! getNumeroCromosSinAbrirFromIDSet".$ex->getMessage();
  } 
  return $fila['TOTAL'];
}
function getNumeroAlumnosFromAsignaturaID($db,$IDAsignatura){
  try 
  {
  $stmt = $db->query("SELECT COUNT(*) TOTAL FROM ALUMNOS WHERE ID_CURSO=(SELECT ID_CURSO FROM ASIGNATURAS WHERE ID=".$IDAsignatura.")");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila['TOTAL'];
}




function esUsernameAdministrador($db,$userName)
{
  $vectorTotal = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM admin WHERE username = '".$userName."'");
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en esUsernameAdministrador:".$ex->getMessage();
  }
    return Count($vectorTotal)>0;  
}
function existeCorreo($db,$CORREO)
{
  $vectorTotal = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM ALUMNOS WHERE CORREO = '".$CORREO."'");
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en existeCorreo:".$ex->getMessage();
  }
    return Count($vectorTotal)>0;  
}
function existeFantasma($db,$IDAsignatura,$dia)
{
  $vectorTotal = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM FALTAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA = ".$IDAsignatura);
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en existeFantasma:".$ex->getMessage();
  }
    return Count($vectorTotal)>0;  
}

function existeFantasmaCreado($db,$IDAsignatura,$dia)
{
  $vectorTotal = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM FALTAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA = ".$IDAsignatura." AND ID_ALUMNO = -1");
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    echo "Error en existeFantasmaCreado:".$ex->getMessage();
  }
    return Count($vectorTotal)>0;  
}

function getFantasma($db,$IDAsignatura,$dia)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM FALTAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA = ".$IDAsignatura." AND ID_ALUMNO = -1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila;
}
   

function setNowUltimaFechaNotiGeneralAlumno($db,$correo)
{
  try 
  {
    $sql = "UPDATE ALUMNOS SET ULTIMA_FECHA_NOTI_GENERAL=now() WHERE CORREO='".$correo."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! setNowUltimaFechaNotiGeneralAlumno ".$ex->getMessage();
  }   
}

function modificarEstadoReto($db,$correo,$idTarea,$estado)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET ESTADO='".$estado."' WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." AND ID_TAREA=".$idTarea;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarEstadoReto ".$ex->getMessage();
  }   
}
function cambiarNivelAlumno($db,$correo, $nivelReal)
{
  try 
  {
    $sql = "UPDATE ALUMNOS SET NUMERO_NIVEL='".$nivelReal."' WHERE CORREO='".$correo."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! cambiarNivelAlumno ".$ex->getMessage();
  }   
}
function modificarListaJugadasPPT($db,$id, $listajugadas)
{
  try 
  {
    $sql = "UPDATE MIBOT SET LISTA_JUGADAS_HOY_PTT='".$listajugadas."' WHERE ID='".$id."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarListaJugadasPPT ".$ex->getMessage();
  }   
}
function modificarOrdenAlbum($db,$correo, $ordentotal)
{
  try 
  {
    $sql = "UPDATE ALUMNOS SET ORDEN_ALBUM='".$ordentotal."' WHERE CORREO='".$correo."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarOrdenAlbum ".$ex->getMessage();
  }   
}

function actualizarNivel($db,$categoria,$numNivel,$value)
{
  try 
  {
    $sql = "UPDATE NIVELES SET ESTRELLAS_DESBLOQUEO=".$value." WHERE CATEGORIA_NIVEL='".$categoria."' AND NUMERO=".$numNivel;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! actualizarNivel ".$ex->getMessage();
  }   
}



function modificarOrdenCombos($db,$correo, $ordenCombos)
{
  try 
  {
    $sql = "UPDATE ALUMNOS SET ORDEN_COMBOS='".$ordenCombos."' WHERE CORREO='".$correo."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarOrdenCombos ".$ex->getMessage();
  }   
}

function modificarOrdenReferenciasTotal($db,$correo, $ordenReferenciasTotal)
{
  try 
  {
    $sql = "UPDATE ALUMNOS SET ORDEN_REFERENCIAS_TOTAL='".$ordenReferenciasTotal."' WHERE CORREO='".$correo."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarOrdenReferenciasTotal ".$ex->getMessage();
  }   
}function modificarOrdenCreadores($db,$correo, $ordenCreadores)
{
  try 
  {
    $sql = "UPDATE ALUMNOS SET ORDEN_CREADORES='".$ordenCreadores."' WHERE CORREO='".$correo."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarOrdenCreadores ".$ex->getMessage();
  }   
}
function modificarGeneradoCromo($db,$gen, $idCromo)
{
  try 
  {
    $sql = "UPDATE CROMOS SET GENERADO=".$gen." WHERE ID=".$idCromo;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarGeneradoCromo ".$ex->getMessage();
  }   
}

function modificarPoseedorCromo($db,$idAlumno, $idCromo)
{
  try 
  {
    $sql = "UPDATE CROMOS SET ID_POSEEDOR=".$idAlumno." WHERE ID=".$idCromo;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarPoseedorCromo ".$ex->getMessage();
  }   
}


function modificarNEstrellasCromo($db,$correo,$nestrellas)
{
  try 
  {
    $sql = "UPDATE CROMOS SET mana_w='".$nestrellas."' WHERE ID_CREADOR = (SELECT ID FROM ALUMNOS WHERE CORREO='".$correo."')";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarNEstrellasCromo ".$ex->getMessage();
  }   
}
function modificarCromo($db,$correo, $nombre,$color,$nestrellas,$atributo,$tipocromo,$descripcion,$artista,$firma,$imagen)
{
  try 
  {
    $sql = "UPDATE CROMOS SET name='".$nombre."',color='".$color."',mana_w='".$nestrellas."'".(($imagen=='')?'':(",picture='".$imagen."'")).",cardtype='".$atributo."',rarity='".$tipocromo."',cardtext='".$descripcion."',artist='".$artista."',bottom='".$firma."' WHERE ID_CREADOR = (SELECT ID FROM ALUMNOS WHERE CORREO='".$correo."')";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarCromo ".$ex->getMessage();
  }   
}

function modificarCalas($db,$correo,$cantidad)
{
  try 
  {
    $sql = "UPDATE MIACTOR SET CALAS= CALAS + (".$cantidad.") WHERE ID = (SELECT ID_MIACTOR FROM ALUMNOS WHERE CORREO='".$correo."')";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarCalas ".$ex->getMessage();
  }   
}
function modificarBot($db,$correo, $saludo,$palabra_clave,$movilidad,$velocidad,$localizacion,$fantasma,$saltando,$personaje,$porcentajesPPT,$postura1,$postura2,$postura3)
{
  try 
  {
  
    $setPersonaje=($personaje!=-1)?(",PERSONAJE=".$personaje):"";

    $sql = "UPDATE MIBOT SET PORCENT_PPT='".$porcentajesPPT."'".$setPersonaje.",POSTURA1=".$postura1.",POSTURA2=".$postura2.",POSTURA3=".$postura3.",FANTASMA=".$fantasma.",SALTANDO=".$saltando.",ID_MAPA=".$localizacion.",VELOCIDAD=".$velocidad.",MOVILIDAD=".$movilidad.",SALUDO='".$saludo."',PALABRA_CLAVE='".$palabra_clave."' WHERE ID = (SELECT ID_MIBOT FROM ALUMNOS WHERE CORREO='".$correo."')";
    //echo $sql;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! modificarBot ".$ex->getMessage();
  }   
}
function modificarCorreoAlumno($db,$IDAlumno,$correo)
{
  try 
  {
    $sql = "UPDATE ALUMNOS SET CORREO='".$correo."' where ID=".$IDAlumno;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  }   
}

function modificarContEleg($db,$IDAsignatura,$dia,$valorInicial)
{
  try 
  {
    $sql = "UPDATE FALTAS SET CONT_ELEG='".$valorInicial."' where ID_ASIGNATURA=".$IDAsignatura." AND DIA='".$dia."' AND ID_ALUMNO = -1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
}
function modificarFantasma($db,$IDAsignatura,$dia,$nSesiones)
{
  try 
  {
    $sql = "UPDATE FALTAS SET SESIONES='".$nSesiones."' where ID_ASIGNATURA=".$IDAsignatura." AND DIA='".$dia."' AND ID_ALUMNO = -1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
}

function insertarElegIDosEnFantasma($db,$IDAsignatura,$dia,$listaElegIDos)
{
  try 
  {
    $sql = "UPDATE FALTAS SET ELEGIDOS='".$listaElegIDos."' where ID_ASIGNATURA=".$IDAsignatura." AND DIA='".$dia."' AND ID_ALUMNO = -1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
}
function getElegIDosFromAsignaturaDia($db,$IDAsignatura,$dia)
{
  try 
  {
  $stmt = $db->query("SELECT ELEGIDOS FROM FALTAS WHERE ID_ASIGNATURA=".$IDAsignatura." AND DIA='".$dia."' AND ID_ALUMNO = -1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return explode(",",$fila['ELEGIDOS']);
}

function getCursosFaltones($db){
  $vectorTotal = array();
  try{
    $stmt = $db->query
    (
      "select curs.nombre,  nfaltas.contador from CURSOS as curs
      join 
      (select asig.ID_curso, falt.contador from
          (select ID_asignatura, sum(sesiones) contador from FALTAS WHERE ID_ALUMNO <> -1 group by ID_asignatura) as falt
          join
          (select ID_curso, ID from ASIGNATURAS) as asig
          on asig.ID = falt.ID_asignatura) 
      as nfaltas
          on curs.ID = nfaltas.ID_curso
          order by nfaltas.contador desc limit 100
      "
    );
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error inserción alumno:".$ex->getMessage();
  }
  return $vectorTotal;
}

function getSesionesAsignaturaFromDiaSemana($db, $asignatura, $dia){
  try{
    $stmt = $db->query("select substr(SESIONES, ".$dia.", 1) from ASIGNATURAS where ID_asignatura = ".$asignatura);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    echo "An Error occured! ".$ex->getMessage();
  }
  return $fila;
}
function getSesionesAsignaturas($db){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("select ID,SESIONES from ASIGNATURAS");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error getSesionesAsignaturas:".$ex->getMessage();
  }
  return $vectorTotal;
}

function getEstrellasComportamientoFromCorreo($db,$correo){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("SELECT ID,ESTRELLAS,DIA, (SELECT NOMBRE FROM ASIGNATURAS WHERE ASIGNATURAS.ID = ESTRELLAS.ID_ASIGNATURA) NOMBRE_ASIGNATURA FROM ESTRELLAS WHERE ID_ALUMNO = ".getAlumnoFromCorreo($db,$correo)['ID']." ORDER BY DIA DESC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error getEstrellasComportamientoFromCorreo:".$ex->getMessage();
  }
  return $vectorTotal;
}
function getEstrellasRetosFromCorreo($db,$correo){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("SELECT ID,ESTRELLAS_CONSEGUIDAS,FECHA, (SELECT NOMBRE FROM TAREAS WHERE TAREAS.ID = ALUMNOS_TAREAS.ID_TAREA) NOMBRE_TAREA , (SELECT TOTAL_ESTRELLAS FROM TAREAS WHERE TAREAS.ID = ALUMNOS_TAREAS.ID_TAREA) TOTAL_ESTRELLAS FROM ALUMNOS_TAREAS WHERE ID_ALUMNO = ".getAlumnoFromCorreo($db,$correo)['ID']." ORDER BY FECHA DESC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error getEstrellasRetosFromCorreo:".$ex->getMessage();
  }
  return $vectorTotal;
}

function buscarAlumnos($db, $filtro){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("select CORREO, ID, ID_CURSO, concat(NOMBRE, ' ', APELLIDO1, ' ', APELLIDO2) nCompleto from ALUMNOS where concat(NOMBRE, ' ', APELLIDO1, ' ', APELLIDO2, (SELECT concat(NOMBRE, ' ', GRADO, '', NIVEL) FROM CURSOS WHERE ID = ID_CURSO)) like ('%".$filtro."%') and ID <> -1");
//    ("select ID, concat(NOMBRE, ' ', APELLIDO1, ' ', APELLIDO2,' ',) nCompleto from ALUMNOS where concat(NOMBRE, ' ', APELLIDO1, ' ', APELLIDO2) like ('%".$filtro."%') and ID <> -1");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     echo "Error buscarAlumnos:".$ex->getMessage();
  }
  return $vectorTotal;
}

function getEstrellasFromAlumnoDiaAsignatura($db, $IDAlumno, $dia, $asignatura)  {
  try{
    //echo "select IFNULL(sum(SESIONES), 0) as nFaltas from FALTAS where ID_ALUMNO = ".$IDAlumno;
    $stmt = $db->query("SELECT ESTRELLAS FROM ESTRELLAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA = ".$asignatura." AND ID_ALUMNO =".$IDAlumno);
    //echo "SELECT IFNULL(sum(SESIONES), 0) as nFaltas FROM FALTAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA = ".$asignatura." AND ID_ALUMNO =".$IDAlumno;
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    echo "An Error occured! ".$ex->getMessage();
  }
  return $fila['ESTRELLAS'];
}
function getFaltasFromAlumnoDiaAsignatura($db, $IDAlumno, $dia, $asignatura)  {
  try{
    //echo "select IFNULL(sum(SESIONES), 0) as nFaltas from FALTAS where ID_ALUMNO = ".$IDAlumno;
    $stmt = $db->query("SELECT IFNULL(sum(SESIONES), 0) as nFaltas FROM FALTAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA = ".$asignatura." AND ID_ALUMNO =".$IDAlumno);
    //echo "SELECT IFNULL(sum(SESIONES), 0) as nFaltas FROM FALTAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA = ".$asignatura." AND ID_ALUMNO =".$IDAlumno;
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    echo "An Error occured! ".$ex->getMessage();
  }
  return $fila['nFaltas'];
}
function getFaltasFromAlumnoID($db, $IDAlumno)  {
  try{
    //echo "select IFNULL(sum(SESIONES), 0) as nFaltas from FALTAS where ID_ALUMNO = ".$IDAlumno;
    $stmt = $db->query("select IFNULL(sum(SESIONES), 0) as nFaltas from FALTAS where ID_ALUMNO = ".$IDAlumno);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    echo "An Error occured! ".$ex->getMessage();
  }
  return $fila['nFaltas'];
}
function getEstrellasFromAlumnoID($db, $IDAlumno)  {
  try{
    //echo "select IFNULL(sum(SESIONES), 0) as nFaltas from FALTAS where ID_ALUMNO = ".$IDAlumno;
    $stmt = $db->query("select IFNULL(sum(ESTRELLAS), 0) as nEs from ESTRELLAS where ID_ALUMNO = ".$IDAlumno);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    echo "An Error occured! ".$ex->getMessage();
  }
  return $fila['nEs'];
}

function getNombreCursoFromAlumno($db,$IDAlumno)
{
  try 
  {
  $stmt = $db->query("SELECT NOMBRE FROM CURSOS WHERE ID=
  (SELECT ID_CURSO FROM ALUMNOS WHERE ID=".$IDAlumno.")");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  return $fila['NOMBRE'];
}
function getNumeroElegIDosPorIDAlumno($db,$IDAlumno){
  $con=0;
  try 
  {
    $vectorTotal = array();
  $stmt = $db->query("SELECT ID_ALUMNO,ELEGIDOS FROM FALTAS WHERE ID_ALUMNO=-1");
     while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila["ELEGIDOS"];
    }
  } catch(PDOException $ex) 
  {    
   echo "An Error occured! ".$ex->getMessage();
  } 
  $arrayElegIDos = array();
  foreach($vectorTotal as $elemento){
    $arrayElegIDos= explode ("," ,$elemento);
    $con+= Count(array_keys($arrayElegIDos,$IDAlumno));
  }
  return $con;
  
}

function getNumElegAlumnos($db,$grado,$nivel){
  //getAlumnosGradoNivel($db,$grado,$nivel)
  $vectorID = getAlumnosGradoNivel($db,$grado,$nivel); // ID por curso
  $vectorTotal = array();
  $vectorAux = array();
  foreach($vectorID as $alumno){
   $vectorAux[] =  $alumno["ID"];
   $vectorTotal[] =  getNumeroElegIDosPorIDAlumno($db,$alumno["ID"]);
  }
  
  for ($i = 0; $i < Count($vectorTotal); $i++){
    for($j = $i; $j < Count($vectorTotal); $j++){
      if ($vectorTotal[$i] < $vectorTotal[$j]){
        $aux1 = $vectorTotal[$j];               $aux2 = $vectorAux[$j];
        $vectorTotal[$j] = $vectorTotal[$i];    $vectorAux[$j] = $vectorAux[$i];
        $vectorTotal[$i] = $aux1;               $vectorAux[$i] = $aux2; 
      }
    }
  }
  return $vectorAux;
}

function getNumElegAlumnos2($db,$grado,$nivel){
  //getAlumnosGradoNivel($db,$grado,$nivel)
  $vectorID = getAlumnosGradoNivel($db,$grado,$nivel); // ID por curso
  $vectorTotal = array();
  $vectorAux = array();
  foreach($vectorID as $alumno){
   $vectorAux[] =  $alumno["ID"];
   $vectorTotal[] =  getNumeroElegIDosPorIDAlumno($db,$alumno["ID"]);
  }
  
  for ($i = 0; $i < Count($vectorTotal); $i++){
    for($j = $i; $j < Count($vectorTotal); $j++){
      if ($vectorTotal[$i] < $vectorTotal[$j]){
        $aux1 = $vectorTotal[$j];               $aux2 = $vectorAux[$j];
        $vectorTotal[$j] = $vectorTotal[$i];    $vectorAux[$j] = $vectorAux[$i];
        $vectorTotal[$i] = $aux1;               $vectorAux[$i] = $aux2; 
      }
    }
  }
  $vectorDeVectores= array();
  $vectorDeVectores[]= $vectorAux;
  $vectorDeVectores[]= $vectorTotal;
  return $vectorDeVectores;
}

function BrioAlgoritmo($alumnos){
  $nPersonas = Count($alumnos);
  $contador = 0;
  $incremento = 1.2;
  $suma = 0.0;
  
  $rangos = array();
  
  $aux = 1;
  for ($i = 0; $i < $nPersonas; $i++){
    $suma += pow($incremento, $i);
    $vAux = array();
    $vAux[] = $aux;
    $vAux[] = $aux *= $incremento;
    $rangos[] = $vAux; 
  }
  $vAux = array();
  $vAux[] = $rangos[$nPersonas-1];
  $vAux[] = $suma;
  $rangos[] = $vAux; 
  $n = rand(1,$suma);
  
  for($i = 0; $i < Count($alumnos); $i++){
    //echo "|".$rangos[$i][0]."|". $rangos[$i][1]."|<br>";
    //echo $n;
    if ($n < 1){
      $n++;  
    } 
    if ($n > $rangos[$i][0] && $n < $rangos[$i][1]){
      return $alumnos[$i];
    }
  }
  return $alumnos[$nPersonas-1];
}

function MarcoAntonioritmo($vIDsOcu){
  $vBolsa= array();
  if (Count($vIDsOcu[1])>0)
  {
    $numeroReferencia= $vIDsOcu[1][0]+1;
    for($i= 0; $i<Count($vIDsOcu[0]);$i++){
      for($j= 0; $j<$numeroReferencia-$vIDsOcu[1][$i];$j++){
        $vBolsa[]= $vIDsOcu[0][$i];
      }
    }
    return $vBolsa[rand(0,Count($vBolsa)-1)];
  }
  else
  {
    return -1;
  }
}









?>