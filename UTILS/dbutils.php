<?php

/*

SQLs varias:

CROMOS QUE ESTÁN SIN ABRIR
SELECT ID FROM CROMOS where ID_SET=7 AND GENERADO=1 AND ID_POSEEDORis null

documento 20 monoterminos aleatorios de ASIR1
SELECT PREGUNTA,RESPUESTA FROM PREGUNTAS WHERE ID_SETPREGUNTA=4 ORDER BY RAND() LIMIT 20

Borrar clanes de un curso: 
Primero: 
DELETE FROM ALUMNOS_CLANES WHERE ID_CLAN IN (SELECT ID_CLAN FROM ALUMNOS_CLANES where ID_ALUMNO IN (SELECT ID FROM ALUMNOS WHERE ID_CURSO= )) 
Segundo: 
DELETE FROM CLANES WHERE id NOT IN (SELECT ID_CLAN FROM ALUMNOS_CLANES)

Borrar mercado de una asignatura
DELETE FROM MERCADO WHERE ID_ASIGNATURA=XX


Modificar calas +150 de un curso:
UPDATE MIACTOR SET CALAS = CALAS + 150 WHERE ID in (SELECT ID_MIACTOR FROM ALUMNOS WHERE ID_CURSO=31)

*/

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
      mi_info_log( "Error conectar:".$ex->getMessage());
  }  
}

function mandarNotificacion($db,$remitente,$receptor,$mensaje)
{
 try
  {
$sqlnoti="insert into notification (notiuser,notireciver,notitype,readit) values (:notiuser,:notireciver,:notitype,0)";
    $querynoti = $db->prepare($sqlnoti);
    $querynoti-> bindParam(':notiuser', $remitente, PDO::PARAM_STR);
    $querynoti-> bindParam(':notireciver',$receptor, PDO::PARAM_STR);
    $querynoti-> bindParam(':notitype', $mensaje, PDO::PARAM_STR);
    $querynoti->execute();  
     }
  catch (PDOException $ex)
  {
      mi_info_log( "Error conectar:".$ex->getMessage());
  }  
} 

function hayEstrellasAsigDia($db, $IDAsig, $dia)
{
   try 
  {
  $stmt = $db->query("SELECT COUNT(*) TOTAL FROM ESTRELLAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA=".$IDAsig);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
}


function insertarAlumnoTarea($db,$idAlumno,$tareaidselect,$estado, $estrellasconseguidas,$fecha)
{
  $sentencia= "INSERT INTO ALUMNOS_TAREAS (ID_ALUMNO, ID_TAREA, ESTADO, ESTRELLAS_CONSEGUIDAS, FECHA,ID_ALUMNO_A_CORREGIR,NOTA_CORREGIDA,COMENT_CORRECCION)
              VALUES ( :alumno, :tarea, :estado, :estrellas, :fecha,NULL,0,'')";
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
    mi_info_log( "Error inserción estrella:".$ex->getMessage());
}  
}
function insertarAutoEvaAlumno($db,$idAutoEva,$idAlumno,$nota)
{
  $sentencia= "INSERT INTO AUTOEVALUACION_ALUMNOS (ID_AUTOEVALUACION, ID_ALUMNO, NOTA)
              VALUES (:ID_AUTOEVALUACION, :ID_ALUMNO, :NOTA)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':ID_AUTOEVALUACION',$idAutoEva);
  $stmt->bindParam(':ID_ALUMNO',$idAlumno);
  $stmt->bindParam(':NOTA',$nota);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    mi_info_log( "Error inserción insertarAutoEvaAlumno:".$ex->getMessage());
}  
}
function insertarAutoEvaluacion($db,$idAlumno,$idTarea)
{
  $sentencia= "INSERT INTO AUTOEVALUACION (ID_ALUMNO, ID_TAREA)
              VALUES (:ID_ALUMNO, :ID_TAREA)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':ID_ALUMNO',$idAlumno);
  $stmt->bindParam(':ID_TAREA',$idTarea);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    mi_info_log( "Error inserción insertarAutoEvacion:".$ex->getMessage());
}  
return $db->lastInsertId();
}
function insertarPregunta($db,$idSetPregunta,$sPregunta,$sRespuesta)
{
  $sentencia= "INSERT INTO PREGUNTAS (ID_SETPREGUNTA, PREGUNTA, RESPUESTA)
              VALUES (:ID_SETPREGUNTA, :PREGUNTA, :RESPUESTA)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':ID_SETPREGUNTA',$idSetPregunta);
  $stmt->bindParam(':PREGUNTA',$sPregunta);
  $stmt->bindParam(':RESPUESTA',$sRespuesta);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    mi_info_log( "Error inserción insertarPregunta:".$ex->getMessage());
}  
}
function insertarAlumnosClan($db,$idClan,$aAlumnos)
{
  $sentencia= "INSERT INTO ALUMNOS_CLANES (ID_ALUMNO, ID_CLAN, DESCRIPCION)
              VALUES ( :ID_ALUMNO, :ID_CLAN, :DESCRIPCION)";
  foreach ($aAlumnos as $idAlumno) 
  {
    try
      {
          $des = "id Alumno:".$idAlumno." id Clan".$idClan;
          $stmt = $db->prepare($sentencia);
          $stmt->bindParam(':ID_ALUMNO',$idAlumno);
          $stmt->bindParam(':ID_CLAN',$idClan);
          $stmt->bindParam(':DESCRIPCION',$des);
          $stmt->execute();
        }
    catch (PDOException $ex)
    {
        mi_info_log( "Error insertarAlumnosClan:".$ex->getMessage());
    }  
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
      mi_info_log( "Error inserción estrella:".$ex->getMessage());
  }  
}
function insertarCambio($db,$iDAlumno1,$iDAlumno12,$iDCromo1,$iDCromo2,$dia)
{
  $sentencia= "INSERT INTO CAMBIOS (ID_ALUMNO1, ID_ALUMNO2, ID_CROMO1, ID_CROMO2, DIA)
              VALUES (:ID_ALUMNO1, :ID_ALUMNO2, :ID_CROMO1, :ID_CROMO2, :DIA)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':ID_ALUMNO1',$iDAlumno1);
  $stmt->bindParam(':ID_ALUMNO2',$iDAlumno12);
  $stmt->bindParam(':ID_CROMO1',$iDCromo1);
  $stmt->bindParam(':ID_CROMO2',$iDCromo2);
  $stmt->bindParam(':DIA',$dia);
  $stmt->execute();
    }
  catch (PDOException $ex)
  {
      mi_info_log( "Error insertarCambio:".$ex->getMessage());
  }  
}

function insertJustificante($db,$diadesde,$diahasta,$idAlumno,$comentario,$enlace)
{

  $sentencia= "INSERT INTO JUSTIFICANTES (ID_ALUMNO,DIA_DESDE, DIA_HASTA, COMENTARIO, ENLACE)
              VALUES ( :ID_ALUMNO,:DIA_DESDE, :DIA_HASTA, :COMENTARIO, :ENLACE)";
  try
  {
    $stmt = $db->prepare($sentencia);
    $stmt->bindParam(':ID_ALUMNO',$idAlumno);
    $stmt->bindParam(':DIA_DESDE',$diadesde);
    $stmt->bindParam(':DIA_HASTA',$diahasta);
    $stmt->bindParam(':COMENTARIO',$comentario);
    $stmt->bindParam(':ENLACE',$enlace);
    $stmt->execute();
  }
  catch (Exception $ex)
  {

      mi_info_log( "Error insertJustificante:".$ex->getMessage());
  }  
}  
function insertMonotermino($db,$idPregunta,$idRespuesta,$idCurso,$idAlumno)
{
  $sentencia= "INSERT INTO MONOTERMINOS ( PREGUNTA,  RESPUESTAS, ID_CURSO,ID_ALUMNO)
              VALUES ( :PREGUNTA,  :RESPUESTAS, :ID_CURSO,:ID_ALUMNO)";
  try
  {
    $stmt = $db->prepare($sentencia);
    $stmt->bindParam(':PREGUNTA',$idPregunta);
    $stmt->bindParam(':RESPUESTAS',$idRespuesta);
    $stmt->bindParam(':ID_CURSO',$idCurso);
    $stmt->bindParam(':ID_ALUMNO',$idAlumno);
    $stmt->execute();
  }
  catch (Exception $ex)
  {
      mi_info_log( "Error insertMonotermino:".$ex->getMessage());
  }  
}
function insertarAlumnoJuicio($db,$idJuicio,$idAlumno,$opcionElegida)
{
  $sentencia= "INSERT INTO ALUMNOS_JUICIOS ( ID_ALUMNO,  ID_JUICIO, OPCION,FECHA)
              VALUES ( :ID_ALUMNO, :ID_JUICIO, :OPCION, now())";
  try
  {
    $stmt = $db->prepare($sentencia);
    $stmt->bindParam(':ID_ALUMNO',$idAlumno);
    $stmt->bindParam(':ID_JUICIO',$idJuicio);
    $stmt->bindParam(':OPCION',$opcionElegida);
    $stmt->execute();
  }
  catch (Exception $ex)
  {
      mi_info_log( "Error insertarAlumnoJuicio:".$ex->getMessage());
  }  
}
function insertarAlumnoTest($db,$idTest,$idAlumno,$idPregunta,$respuestaOK)
{
  $sentencia= "INSERT INTO ALUMNOS_FASTESTS ( ID_ALUMNO,  ID_FASTEST, PREGUNTAS, RESPUESTAS, RESULTADO, FECHA)
              VALUES ( :ID_ALUMNO, :ID_FASTEST, :PREGUNTAS, :RESPUESTAS, :RESULTADO, now())";
  try
  {
    
    $resultadoo = ($respuestaOK)?1:0;
    $stmt = $db->prepare($sentencia);
    $stmt->bindParam(':ID_ALUMNO',$idAlumno);
    $stmt->bindParam(':ID_FASTEST',$idTest);
    $stmt->bindParam(':PREGUNTAS',$idPregunta);
    $stmt->bindParam(':RESPUESTAS',$resultadoo);
    $stmt->bindParam(':RESULTADO',$resultadoo);
    $stmt->execute();
  }
  catch (Exception $ex)
  {
      mi_info_log( "Error insertarAlumnoTest:".$ex->getMessage());
  }  
}
function insertarJuicio($db,$asignatura,$name,$opciones,$descrip)
{
  $sentencia= "INSERT INTO JUICIOS ( ID_ASIGNATURA, NOMBRE, OPCIONES, DESCRIPCION, ACTIVO,FECHA)
              VALUES ( :ID_ASIGNATURA, :NOMBRE, :OPCIONES,:DESCRIPCION, :ACTIVO, now())";
  try
  {
    $uno = 1;
    $stmt = $db->prepare($sentencia);
    $stmt->bindParam(':ID_ASIGNATURA',$asignatura);
    $stmt->bindParam(':NOMBRE',$name);
    $stmt->bindParam(':OPCIONES',$opciones);
    $stmt->bindParam(':DESCRIPCION',$descrip);
    $stmt->bindParam(':ACTIVO',$uno);
    $stmt->execute();
  }
  catch (Exception $ex)
  {
      mi_info_log( "Error insertarJuicio:".$ex->getMessage());
  }  
}
function insertarFastest($db,$asignatura,$name,$descrip)
{
  $sentencia= "INSERT INTO FASTESTS ( ID_ASIGNATURA, NOMBRE, DESCRIPCION, ACTIVO,FECHA)
              VALUES ( :ID_ASIGNATURA, :NOMBRE,:DESCRIPCION, :ACTIVO, now())";
  try
  {
    $uno = 1;
    $stmt = $db->prepare($sentencia);
    $stmt->bindParam(':ID_ASIGNATURA',$asignatura);
    $stmt->bindParam(':NOMBRE',$name);
    $stmt->bindParam(':DESCRIPCION',$descrip);
    $stmt->bindParam(':ACTIVO',$uno);
    $stmt->execute();
  }
  catch (Exception $ex)
  {
      mi_info_log( "Error insertarFastest:".$ex->getMessage());
  }  
}
function insertarReto($db,$asignatura,$name,$totalestrellas,$descrip,$selSitios,$posx,$posy,$linkdocumento,$fechalimite,$visible,$examen,$rubrica,$visible_web)
{
  $sentencia= "INSERT INTO TAREAS ( ID_ASIGNATURA, NOMBRE, TOTAL_ESTRELLAS, ID_SITIO, POS_X, POS_Y,VISIBLE,VISIBLE_WEB, DESCRIPCION,LINK_DOCUMENTO,FECHA_CREACION,FECHA_LIMITE,EXAMEN,RUBRICA)
              VALUES ( :ID_ASIGNATURA, :NOMBRE, :TOTAL_ESTRELLAS, :ID_SITIO, :POS_X, :POS_Y,:VISIBLE,:VISIBLE_WEB, :DESCRIPCION, :LINK_DOCUMENTO, now(),:FECHA_LIMITE,:EXAMEN,:RUBRICA)";
  try
  {
    $stmt = $db->prepare($sentencia);
    $stmt->bindParam(':ID_ASIGNATURA',$asignatura);
    $stmt->bindParam(':NOMBRE',$name);
    $stmt->bindParam(':TOTAL_ESTRELLAS',$totalestrellas);
    $stmt->bindParam(':ID_SITIO',$selSitios);
    $stmt->bindParam(':POS_X',$posx);
    $stmt->bindParam(':POS_Y',$posy);
    $stmt->bindParam(':VISIBLE',$visible);
    $stmt->bindParam(':VISIBLE_WEB',$visible_web);
    $stmt->bindParam(':DESCRIPCION',$descrip);
    $stmt->bindParam(':LINK_DOCUMENTO',$linkdocumento);
    $stmt->bindParam(':FECHA_LIMITE',$fechalimite);
    $stmt->bindParam(':EXAMEN',$examen);
    $stmt->bindParam(':RUBRICA',$rubrica);
    $stmt->execute();
  }
  catch (Exception $ex)
  {
      mi_info_log( "Error insertarReto:".$ex->getMessage());
  }  
}



function insertarCromo($db,$ID_CREADOR,$ID_POSEEDOR,$GENERADO, $setId, $name, $color, $mana_w, $picture, $cardtype, $rarity, $cardtext, $power, $toughness, $artist, $bottom)
{
  $sentencia= "INSERT INTO CROMOS(ID_SET, ID_CREADOR, ID_POSEEDOR, GENERADO, name, color, mana_w, picture, cardtype, rarity, cardtext, power, toughness, artist, bottom) VALUES (:setId, :ID_CREADOR,:ID_POSEEDOR,:GENERADO, :name, :color, :mana_w, :picture, :cardtype, :rarity, :cardtext, :power, :toughness, :artist, :bottom)";
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
        mi_info_log( "Error inserción cromo:".$ex->getMessage());
    }  
}

function insertarActor($db,$PV, $PM, $PT, $CALAS)
{
  $sentencia= "INSERT INTO MIACTOR(PV, PM, PT, CALAS) VALUES (:PV, :PM, :PT, :CALAS)";
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
        mi_info_log( "Error inserción bot:".$ex->getMessage());
    }  
}
function insertarBono($db,$idAlu,$idCur,$numeroEstrellas,$nombre)
{
     

$sentencia= "INSERT INTO BONOS (ID_ALUMNO, ID_CURSO, NUM_ESTRELLAS,NOMBRE,FECHA_CREACION) VALUES (:ID_ALUMNO, :ID_CURSO, :NUM_ESTRELLAS, :NOMBRE, now())";
  try
  {
      $stmt = $db->prepare($sentencia);
      $stmt->bindParam(':ID_ALUMNO',$idAlu);
      $stmt->bindParam(':ID_CURSO',$idCur);
      $stmt->bindParam(':NUM_ESTRELLAS',$numeroEstrellas);
      $stmt->bindParam(':NOMBRE',$nombre);
      $stmt->execute();
    }
    catch (PDOException $ex)
    {
        mi_info_log( "Error inserción bot:".$ex->getMessage());
    }  
    mandarNotificacion($db,'Admin',getAlumnoFromID($db,$idAlu)['CORREO'],' Se te ha aplicado el bono {'.$nombre.'} con valor de '.$numeroEstrellas.' estrellas.');
}

function insertarBot($db,$SALUDO,$DESCRIPCION,$PALABRA_CLAVE,$MOVILIDAD,$VELOCIDAD,$FANTASMA,$SALTANDO,$PERSONAJE,$PORCENT_PPT,$POSTURA1,$POSTURA2,$POSTURA3,$LISTA_JUGADAS_HOY_PTT,$ID_MAPA_INICIO,$POS_X_INICIO,$POS_Y_INICIO)
{
  $sentencia= "INSERT INTO MIBOT(SALUDO,DESCRIPCION,PALABRA_CLAVE,MOVILIDAD,VELOCIDAD,FANTASMA,SALTANDO,PERSONAJE,PORCENT_PPT,POSTURA1,POSTURA2,POSTURA3,LISTA_JUGADAS_HOY_PTT,ID_MAPA_INICIO,POS_X_INICIO,POS_Y_INICIO) VALUES (:SALUDO,:DESCRIPCION,:PALABRA_CLAVE,:MOVILIDAD,:VELOCIDAD,:FANTASMA,:SALTANDO,:PERSONAJE,:PORCENT_PPT,:POSTURA1,:POSTURA2,:POSTURA3,:LISTA_JUGADAS_HOY_PTT,:ID_MAPA_INICIO,:POS_X_INICIO,:POS_Y_INICIO)";
  try
  {
      $stmt = $db->prepare($sentencia);
      $stmt->bindParam(':SALUDO',$SALUDO);
      $stmt->bindParam(':DESCRIPCION',$DESCRIPCION);
      $stmt->bindParam(':PALABRA_CLAVE',$PALABRA_CLAVE);
      $stmt->bindParam(':MOVILIDAD',$MOVILIDAD);
      $stmt->bindParam(':VELOCIDAD',$VELOCIDAD);
      $stmt->bindParam(':FANTASMA',$FANTASMA);
      $stmt->bindParam(':SALTANDO',$SALTANDO);
      $stmt->bindParam(':PERSONAJE',$PERSONAJE);
      $stmt->bindParam(':PORCENT_PPT',$PORCENT_PPT);
      $stmt->bindParam(':POSTURA1',$POSTURA1);
      $stmt->bindParam(':POSTURA2',$POSTURA2);
      $stmt->bindParam(':POSTURA3',$POSTURA3);
      $stmt->bindParam(':LISTA_JUGADAS_HOY_PTT',$LISTA_JUGADAS_HOY_PTT);
      $stmt->bindParam(':ID_MAPA_INICIO',$ID_MAPA_INICIO);
      $stmt->bindParam(':POS_X_INICIO',$POS_X_INICIO);
      $stmt->bindParam(':POS_Y_INICIO',$POS_Y_INICIO);
      $stmt->execute();
    }
    catch (PDOException $ex)
    {
        mi_info_log( "Error inserción bot:".$ex->getMessage());
    }  
}

function borrarOfrezcoMercado($db,$IdAsignatura,$IdAlumno)
{
 try 
  {
   $sql = "DELETE FROM MERCADO WHERE ID_ASIGNATURA=".$IdAsignatura. " AND ID_ALUMNO=".$IdAlumno." AND QUIERO_NOMBRE_CROMO_ID IS NULL";
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarOfrezcoMercado".$ex->getMessage());
  } 
}
function borrarQuieroMercado($db,$IdAsignatura,$IdAlumno)
{
 try 
  {
   $sql = "DELETE FROM MERCADO WHERE ID_ASIGNATURA=".$IdAsignatura. " AND ID_ALUMNO=".$IdAlumno." AND OFREZCO_ID_CROMO IS NULL";
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarQuieroMercado".$ex->getMessage());
  } 
}
function borrarBotFromId($db,$idBot)
{
 try 
  {
   $sql = "DELETE FROM MIBOT WHERE ID=".$idBot;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarBotFromId".$ex->getMessage());
  } 
}
function borrarJustificanteFromId($db,$idJus)
{
 try 
  {
   $sql = "DELETE FROM JUSTIFICANTES WHERE ID=".$idJus;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarJustificanteFromId".$ex->getMessage());
  } 
}
function borrarFilaFromId($db,$sql)
{
 try 
  {
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarFilaFromId".$ex->getMessage());
   return 'ERROR: '.$ex->getMessage();
  } 
  return "borrado correctamente...";
}
function borrarAutoevaluacion($db,$idAlumno,$idTarea)
{
 try 
  {
   $sql = "DELETE FROM AUTOEVALUACION WHERE ID_ALUMNO=".$idAlumno." AND ID_TAREA=".$idTarea;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarAutoevaluacion".$ex->getMessage());
  } 
}
function borrarAutoevaluacionAlumnos($db,$idAutoevaluacion)
{
 try 
  {
   $sql = "DELETE FROM AUTOEVALUACION_ALUMNOS WHERE ID_AUTOEVALUACION=".$idAutoevaluacion;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarAutoevaluacionAlumnos".$ex->getMessage());
  } 
}


function borrarCompraFromId($db,$idCompra)
{
 try 
  {
   $sql = "DELETE FROM ARTICULOS_TIENDA WHERE ID=".$idCompra;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarCompraFromId".$ex->getMessage());
  } 
}
function borrarMonoterminoFromId($db,$idMo)
{
 try 
  {
   $sql = "DELETE FROM MONOTERMINOS WHERE ID=".$idMo;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarMonoterminoFromId".$ex->getMessage());
  } 
}
function borrarAlumnoFromClanId($db,$idAlumno,$idClan)
{
 try 
  {
   $sql = "DELETE FROM ALUMNOS_CLANES WHERE ID_CLAN=".$idClan." AND ID_ALUMNO=".$idAlumno;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarAlumnoFromClanId".$ex->getMessage());
  } 
}
function borrarAlumnosClanFromClanId($db,$id)
{
 try 
  {
   $sql = "DELETE FROM ALUMNOS_CLANES WHERE ID_CLAN=".$id;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarAlumnosClanFromClanId".$ex->getMessage());
  } 
}

function borrarClanFromId($db,$id)
{
 try 
  {
   $sql = "DELETE FROM CLANES WHERE ID=".$id;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarClanFromId".$ex->getMessage());
  } 
}
function borrarActorFromId($db,$idActor)
{
 try 
  {
   $sql = "DELETE FROM MIACTOR WHERE ID=".$idActor;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarActorFromId".$ex->getMessage());
  } 
}
function borrarAlumnoFromId($db,$idAlumno)
{
 try 
  {
   $sql = "DELETE FROM ALUMNOS WHERE ID=".$idAlumno;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarAlumnoFromId".$ex->getMessage());
  } 
}

function borrarBonosFromAlumnoId ($db,$idAlumno)
{
 try 
  {
   $sql = "DELETE FROM BONOS WHERE ID_ALUMNO=".$idAlumno;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarBonosFromAlumnoId".$ex->getMessage());
  } 
}
function borrarCromosNoPoseidosFromIdCreador($db,$idAlumno)
{
 try 
  {
   $sql = "DELETE FROM CROMOS WHERE ID_CREADOR=".$idAlumno." AND 
   (ID_POSEEDOR IS NULL OR ID_POSEEDOR=ID_CREADOR)";
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! borrarCromosNoPoseidosFromIdCreador".$ex->getMessage());
  } 
}

function borrarFaltasAlumno($db,$alumno)
{
 try 
  {
   $sql = "DELETE FROM FALTAS WHERE ID_ALUMNO =".$alumno;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
}
function borrarAlumnoTarea($db,$alumno)
{
 try 
  {
   $sql = "DELETE FROM ALUMNOS_TAREAS WHERE ID_ALUMNO =".$alumno;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
}
function borrarNotificacionReceiver($db,$correoReceiver)
{
 try 
  {
   $sql = "DELETE FROM notification WHERE notireciver ='".$correoReceiver."'";
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
}
function borrarEstrellasAlumno($db,$alumno)
{
 try 
  {
   $sql = "DELETE FROM ESTRELLAS WHERE ID_ALUMNO =".$alumno;
   $db->exec($sql);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
    mi_info_log( "Error inserción falta:".$ex->getMessage());
}  
}

function insertarFCTAlumnos($db,$nombre,$apellIDo1,$apellIDo2,$correo,$idCiclo)
{
  $sentencia= "INSERT INTO FCT_ALUMNOS(NOMBRE, APELLIDO1, APELLIDO2, CORREO, DNI, ID_FCT_CICLO, INFO) VALUES  (:NOMBRE, :APELLIDO1, :APELLIDO2, :CORREO, :DNI, :ID_FCT_CICLO, :INFO)";
  try
  {
  $dni = "1";
  $info = $nombre." ".$apellIDo1;
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':NOMBRE',$nombre);
  $stmt->bindParam(':APELLIDO1',$apellIDo1);
  $stmt->bindParam(':APELLIDO2',$apellIDo2);
  $stmt->bindParam(':CORREO',$correo);
  $stmt->bindParam(':DNI',$dni);
  $stmt->bindParam(':ID_FCT_CICLO',$idCiclo);
  $stmt->bindParam(':INFO',$info);
  $stmt->execute();
    }
    catch (PDOException $ex)
    {
        mi_info_log( "Error inserción insertarFCTEmpresa:".$ex->getMessage());
    }  
}
function insertarFCTEmpresa($db,$nConvenio,$sNombre,$fechaRev)
{
  $sentencia= "INSERT INTO FCT_EMPRESAS(N_CONVENIO, NOMBRE, FECHA_REV, ENLACE_CONVENIO, CONTACTO, DIRECCION, INFO) VALUES (:N_CONVENIO, :NOMBRE, :FECHA_REV, :ENLACE_CONVENIO, :CONTACTO, :DIRECCION, :INFO)";
  try
  {
  $vacia = "";
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':N_CONVENIO',$nConvenio);
  $stmt->bindParam(':NOMBRE',$sNombre);
  $stmt->bindParam(':FECHA_REV',$fechaRev);
  $stmt->bindParam(':ENLACE_CONVENIO',$vacia);
  $stmt->bindParam(':CONTACTO',$vacia);
  $stmt->bindParam(':DIRECCION',$vacia);
  $stmt->bindParam(':INFO',$sNombre);
  $stmt->execute();
    }
    catch (PDOException $ex)
    {
        mi_info_log( "Error inserción insertarFCTEmpresa:".$ex->getMessage());
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
    mi_info_log( "Error inserción alumno:".$ex->getMessage());
}  
}
function insertarOfrezcoMercado($db,$IdAsignatura,$IdAlumno,$ofrezcoIdCromo)
{
  $sentencia= "INSERT INTO MERCADO ( ID_ASIGNATURA, ID_ALUMNO, OFREZCO_ID_CROMO)
              VALUES ( :ID_ASIGNATURA, :ID_ALUMNO, :OFREZCO_ID_CROMO)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':ID_ASIGNATURA',$IdAsignatura);
  $stmt->bindParam(':ID_ALUMNO',$IdAlumno);
  $stmt->bindParam(':OFREZCO_ID_CROMO',$ofrezcoIdCromo);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    mi_info_log( "Error insertarOfrezcoMercado:".$ex->getMessage());
}  
}
function insertarQuieroMercado($db,$IdAsignatura,$IdAlumno,$quieroNombreCromoId,$quieroRefCromo,$quieroEstrellasCromo)
{
  $sentencia= "INSERT INTO MERCADO ( ID_ASIGNATURA, ID_ALUMNO, QUIERO_NOMBRE_CROMO_ID,QUIERO_REF_CROMO,QUIERO_ESTRELLAS_CROMO)
              VALUES ( :ID_ASIGNATURA, :ID_ALUMNO, :QUIERO_NOMBRE_CROMO_ID,:QUIERO_REF_CROMO,:QUIERO_ESTRELLAS_CROMO)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':ID_ASIGNATURA',$IdAsignatura);
  $stmt->bindParam(':ID_ALUMNO',$IdAlumno);
  $stmt->bindParam(':QUIERO_NOMBRE_CROMO_ID',$quieroNombreCromoId);
  $stmt->bindParam(':QUIERO_REF_CROMO',$quieroRefCromo);
  $stmt->bindParam(':QUIERO_ESTRELLAS_CROMO',$quieroEstrellasCromo);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    mi_info_log( "Error insertarQuieroMercado:".$ex->getMessage());
}  
}
function insertarClan($db,$nombre,$imagen,$descripcion)
{
  $sentencia= "INSERT INTO CLANES ( NOMBRE, IMAGEN, DESCRIPCION)
              VALUES ( :NOMBRE, :IMAGEN, :DESCRIPCION)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':NOMBRE',$nombre);
  $stmt->bindParam(':IMAGEN',$imagen);
  $stmt->bindParam(':DESCRIPCION',$descripcion);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    mi_info_log( "Error insertarClan:".$ex->getMessage());
}  
return $db->lastInsertId();
}

function ejecutarQuery($db,$sQuery){
  $vectorTotal = array();
  try{
    $stmt = $db->query($sQuery);
    //var_export($sQuery);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error ejecutarQuery:".$ex->getMessage());
  }
  return $vectorTotal;
}

function getJustificantesFromAlumno($db,$idAlumno){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT * FROM JUSTIFICANTES WHERE ID_ALUMNO=".$idAlumno);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getJustificantesFromAlumno:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getColumnasFromTabla($db,$nombreTabla  ){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME ='".$nombreTabla."'");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getColumnasFromTabla:".$ex->getMessage());
  }
  return $vectorTotal;
}

function getAlumnosFromAsignaturaID($db,$IDAsignatura){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT * FROM ALUMNOS WHERE ID_CURSO=(SELECT ID_CURSO FROM ASIGNATURAS WHERE ID=".$IDAsignatura.") order by NOMBRE,APELLIDO1");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getAlumnosFromAsignaturaID:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getAutoEvaAlumnos($db,$idAutoEva){
  $vectorTotal = array();
  if ($idAutoEva=="") return $vectorTotal;
  try{
    $stmt = $db->query("SELECT * FROM AUTOEVALUACION_ALUMNOS WHERE ID_AUTOEVALUACION=".$idAutoEva);

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getAutoEvaAlumnos:".$ex->getMessage());
  }
  return $vectorTotal;
}

function getMonoterminosFromIdCurso($db,$IdCurso){
  $vectorTotal = array();
  try{
  $stmt = $db->query("SELECT * FROM MONOTERMINOS WHERE ID_CURSO=".$IdCurso);

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getMonoterminosFromIdCurso:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getArticulosFromIdTienda($db,$IdTienda){
  $vectorTotal = array();
  try{

  $stmt = $db->query("SELECT * FROM ARTICULOS_TIENDA WHERE ID_TIENDA=".$IdTienda);

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getArticulosPendientesFromIdTienda:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getComprasFromArticuloYEstado($db,$Idarticulo,$estado){
  $vectorTotal = array();
  try{
  $stmt = $db->query("SELECT * FROM ARTICULOS_COMPRADORES WHERE ID_ARTICULO=".$Idarticulo." AND ESTADO='".$estado."'");

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getComprasFromArticulo:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getComprasFromAlumno($db,$IdAlumno){
  $vectorTotal = array();
  try{
  $stmt = $db->query("SELECT * FROM ARTICULOS_COMPRADORES WHERE ID_ALUMNO=".$IdAlumno." ORDER BY FECHA_COMPRA DESC");

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getComprasFromAlumno:".$ex->getMessage());
  }
  return $vectorTotal;
}
function cmpFecha($a, $b)
{
    if ($a['FECHA_COMPRA'] < $b['FECHA_COMPRA']) {
        return 1;
    }
    else
    {
      return -1;
    }  
}
function cmpComprador($a, $b)
{
    if ($a['ID_ALUMNO'] == $b['ID_ALUMNO']) {
        return 0;
    }
    else if ($a['ID_ALUMNO'] < $b['ID_ALUMNO']) {
        return 1;
    }
    else
    {
      return -1;
    }  
}


function getComprasFromArticulosYEstadoOrderBy($db,$aArticulos,$estado,$orderBy)
{
  $vectorTotal = array();
  try{
    $funcionComp = "cmpFecha";
    switch ($orderBy) {
      case 'FECHA_COMPRA':
        $funcionComp = "cmpFecha";
        break;
      case 'COMPRADOR':
        $funcionComp = "cmpComprador";
        break;
    }
  foreach ($aArticulos as $articulo) 
  {
    $aComprasArt = getComprasFromArticuloYEstado($db,$articulo['ID'],$estado);
    $vectorTotal = array_merge($vectorTotal, $aComprasArt);
  }
  }catch(PDOException $ex){
     mi_info_log( "Error getComprasPentientesFromArticulos:".$ex->getMessage());
  }

  usort($vectorTotal, "cmpFecha");
  return $vectorTotal;
}



function getCambiosFromAlumnoIdOrderDate($db,$IdAlumno){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT * FROM CAMBIOS WHERE ID_ALUMNO1=".$IdAlumno." OR ID_ALUMNO2=".$IdAlumno." ORDER BY DIA DESC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getCambiosFromAlumnoIdOrderDate:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getOfrezcoMercado($db,$idAsignatura,$idAlumno){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT * FROM MERCADO WHERE ID_ASIGNATURA=".$idAsignatura." AND ID_ALUMNO=".$idAlumno." AND QUIERO_NOMBRE_CROMO_ID IS NULL");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getOfrezcoMercado:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getQuieroMercado($db,$idAsignatura,$idAlumno){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT * FROM MERCADO WHERE ID_ASIGNATURA=".$idAsignatura." AND ID_ALUMNO=".$idAlumno." AND OFREZCO_ID_CROMO IS NULL");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getQuieroMercado:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getAllAlumnos($db){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT * FROM ALUMNOS  WHERE id >= 201 AND id <= 300");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getAllAlumnos:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getFantasmasFromAsignaturaID($db,$IDAsignatura){
  $vectorTotal = array();

  try{
    $stmt = $db->query("SELECT * FROM FALTAS WHERE ID_ASIGNATURA=".$IDAsignatura." AND ID_ALUMNO=-1 ORDER BY DIA DESC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getFantasmasFromAsignaturaID:".$ex->getMessage());
  }
  return $vectorTotal;
}


function getPreguntasTotal($db){
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT * FROM PREGUNTAS");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getPreguntasTotal  :".$ex->getMessage());
  }
  return $vectorTotal;
}
function getPreguntasFromAsignaturaID($db,$IDAsignatura){
  
  $idSetPregunta = getIdSetPreguntaFromIdAsignatura($db,$IDAsignatura);
  
  $vectorTotal = array();
  try{
    $stmt = $db->query("SELECT * FROM PREGUNTAS WHERE ID_SETPREGUNTA=".$idSetPregunta);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getPreguntasFromAsignaturaID:".$ex->getMessage());
  }
  return $vectorTotal;
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
     mi_info_log( "Error getAlumnosFaltones:".$ex->getMessage());
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
     mi_info_log( "Error getAlumnosInforme:".$ex->getMessage());
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
   mi_info_log( "An Error occured! setNotificationsOff ".$ex->getMessage());
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
     mi_info_log( "Error getNotificationsOff:".$ex->getMessage());
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
     mi_info_log( "Error getNotificationsOff:".$ex->getMessage());
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
     mi_info_log( "Error getAlumnosInformeEstrellas:".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
  return $fila;
}
function getBonoLike($db,$idAlumno,$idCurso,$fecha,$sTextoLike){
  try 
  {
  $stmt = $db->query("SELECT * FROM BONOS WHERE ID_ALUMNO= ".$idAlumno." AND ID_CURSO= ".$idCurso." AND DATE(FECHA_CREACION)= '".$fecha."' AND NOMBRE LIKE '%".$sTextoLike."%'");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! getBonoLike".$ex->getMessage());
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
   mi_info_log( "An Error occured in getNivelFromNumeroNivel ".$ex->getMessage());
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
    mi_info_log( "Error en getAlumnosFromCursoID:".$ex->getMessage());
  }
    return $vectorTotal;
}
function getIdsClanFromIdCurso($db,$idCurso)
{
  $vectorTotal = array();
  try
  {  
   
    $aAlumnos = getAlumnosFromCursoID($db,$idCurso);
    $alumn = array();
    foreach ($aAlumnos as $all) {
      $alumn[]=$all['ID'];
    }
    $stmt = $db->query("SELECT  ID_CLAN FROM ALUMNOS_CLANES WHERE ID_ALUMNO IN (".implode (", ", $alumn).")");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
          $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getIdsClanFromIdCurso:".$ex->getMessage());
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
    mi_info_log( "Error en getAlumnosGradoNivel:".$ex->getMessage());
  }
    return $vectorTotal;
}
function getConfGenerales($db)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM CONF_GENERALES");   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getConfGenerales:".$ex->getMessage());
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
    mi_info_log( "Error en getCromosDeAlbum:".$ex->getMessage());
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
    mi_info_log( "Error en getCursosPersonajesFromCursoID:".$ex->getMessage());
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
    mi_info_log( "Error en getAndSetRandomCromo:".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
  return $fila['GRADO'].$fila['NIVEL'];
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
   mi_info_log( "An Error occured! getDatosAlumnoTarea".$ex->getMessage());
  } 
  return $fila;
}
function getCorrectoresAlumnoTarea($db,$idAlumno,$idTarea)
{
 $vectorTotal = array();
  try 
  {
  $sql="SELECT * FROM ALUMNOS_TAREAS WHERE ID_ALUMNO_A_CORREGIR=".$idAlumno." AND ID_TAREA = ".$idTarea;
  $stmt = $db->query($sql);
  while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! getCorrectoresAlumnoTarea".$ex->getMessage());
  } 
  return $vectorTotal;
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
    mi_info_log( "Error en getAsignaturasConCurso:".$ex->getMessage());
  }
  return $vectorTotal;
}

function getAllAsignaturas($db)
{
    $vectorTotal = array();
  try
  {
    $stmt = $db->query("SELECT * FROM ASIGNATURAS");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getAllAsignaturas:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getSitiosVisibles($db)
{
    $vectorTotal = array();
  try
  {
    $stmt = $db->query("SELECT NOMBRE_VISUAL,CODIGO,ID FROM SITIOS WHERE VISIBLE=1");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila['NOMBRE_VISUAL']."*".$fila['CODIGO']."--".$fila['ID'];
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getSitiosVisibles:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getAllSitiosVisibles($db)
{
    $vectorTotal = array();
  try
  {
    $stmt = $db->query("SELECT * FROM SITIOS WHERE VISIBLE=1");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getAllSitiosVisibles:".$ex->getMessage());
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
    mi_info_log( "Error en getAsignaturasFromCurso:".$ex->getMessage());
     //$okEnvio = enviarCorreo("afsanchez@lasalleinstitucion.es","Error en getAsignaturasFromCurso",$ex->getMessage());
      //if (!$okEnvio)
      //{
      //  mi_info_log( "Error al enviar correo en getAsignaturasFromCurso:".$ex->getMessage());
      //}
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
    mi_info_log( "Error en getNivelesFromCategoria:".$ex->getMessage());
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
    mi_info_log( "Error en getCursosGradoNivel:".$ex->getMessage());
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
    mi_info_log( "Error en getFaltasAsignaturaClase:".$ex->getMessage());
  }
    return $vectorTotal;  
}
function existeTablaEnDB($db,$tabla)
{
  $fila="";
  try 
  {
  $stmt = $db->query("select * from information_schema.tables where table_name ='".$tabla."'");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! existeTablaEnDB ".$ex->getMessage());
  } 
  return $fila;
}

function existeColumnaEnTabla($db,$nombreCol, $tabla)
{
  $fila="";
  try 
  {
  if (!existeTablaEnDB($db,$tabla))
  {
    return false;
  }
  $stmt = $db->query("SHOW COLUMNS FROM ".$tabla." LIKE '".$nombreCol."'");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! existeColumnaEnTabla ".$ex->getMessage());
  } 
  return $fila;
}

function getJuicioActivo($db,$IdAsignatura)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM JUICIOS WHERE ACTIVO = 1 AND ID_ASIGNATURA=".$IdAsignatura);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
  return $fila;
}
function getFastestActivo($db,$IdAsignatura)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM FASTESTS WHERE ACTIVO = 1 AND ID_ASIGNATURA=".$IdAsignatura);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
  return $fila;
}
function getJustificanteFromId($db,$idJus)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM JUSTIFICANTES WHERE ID=".$idJus);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured on getJustificanteFromId! ".$ex->getMessage());
  } 
  return $fila;
}
function getJuicioFromId($db,$idJuicio)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM JUICIOS WHERE ID=".$idJuicio);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured on getJuicioFromId ! ".$ex->getMessage());
  } 
  return $fila;
}

function getFastestFromId($db,$idFT)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM FASTESTS WHERE ID=".$idFT);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getFastestFromId ! ".$ex->getMessage());
  } 
  return $fila;
}
function getTestAlumno($db,$idFT,$idAlumno)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM ALUMNOS_FASTESTS WHERE ID_FASTEST=".$idFT." AND ID_ALUMNO=".$idAlumno);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getTestAlumno ! ".$ex->getMessage());
  } 
  return $fila;
}
function getAlumnoFromID($db,$IDAlumno)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM ALUMNOS WHERE ID=".$IDAlumno);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
  return $fila;
}
function getCompraFromId($db,$compraId)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM ARTICULOS_COMPRADORES WHERE ID=".$compraId);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error getCompraFromId! ".$ex->getMessage());
  } 
  return $fila;
}

function getPreguntaFromID($db,$idPre)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM PREGUNTAS WHERE ID=".$idPre);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! getPreguntaFromID".$ex->getMessage());
  } 
  return $fila;
}
function getClanFromClanId($db,$clanId)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM CLANES WHERE ID=".$clanId);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! getClanFromClanId ".$ex->getMessage());
  } 
  return $fila;
}
function getClanIdFromAlumnoId($db,$IDAlumno)
{
  $fila="";
  try 
  {
  $stmt = $db->query("SELECT * FROM ALUMNOS_CLANES WHERE ID_ALUMNO=".$IDAlumno);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! getClanIdFromAlumnoId".$ex->getMessage());
  } 
  return $fila["ID_CLAN"];
}
function getConfAsignaturaFromID($db,$Id)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM CONF_ASIGNATURAS WHERE ID=".$Id);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! getConfAsignaturaFromID".$ex->getMessage());
  } 
  return $fila;
}
function getEventoFromID($db,$IdE)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM EVENTOS WHERE ID=".$IdE);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! getEventoFromID".$ex->getMessage());
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
   mi_info_log( "An Error occured! getMiBotFromAlumnoID ".$ex->getMessage());
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
   mi_info_log( "An Error occured! getMiActorFromAlumnoID ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
  return $fila;
}


 
function getAlumnosfromFastestId($db,$idFT)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM ALUMNOS_FASTESTS WHERE ID_FASTEST=".$idFT." ORDER BY RESULTADO DESC");   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getAlumnosfromFastestId:".$ex->getMessage());
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
    mi_info_log( "Error en getCursosFromIDSet:".$ex->getMessage());
  }
    return $vectorTotal;
}
function getVotacionesAlumnosJuicio($db,$idJuicio)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM ALUMNOS_JUICIOS WHERE ID_JUICIO=".$idJuicio);   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getVotacionesAlumnosJuicio:".$ex->getMessage());
  }
    return $vectorTotal;
}
function getJuiciosFromAsignatura($db,$IdAsignatura)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM JUICIOS WHERE ID_ASIGNATURA=".$IdAsignatura. " ORDER BY FECHA DESC");   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getJuiciosFromAsignatura:".$ex->getMessage());
  }
    return $vectorTotal;
}
function getFastTestsFromAsignatura($db,$IdAsignatura)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM FASTESTS WHERE ID_ASIGNATURA=".$IdAsignatura. " ORDER BY FECHA DESC");   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getFastTestsFromAsignatura:".$ex->getMessage());
  }
    return $vectorTotal;
}
function getAlumnosIdFromClanId($db,$idClan)
{
  $vectorTotal = array();
  try
  {     
    $stmt = $db->query("SELECT * FROM ALUMNOS_CLANES WHERE ID_CLAN=".$idClan);   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getAlumnosIdFromClanId:".$ex->getMessage());
  }
    return $vectorTotal;
}

function utilizaCromosCurso($db,$curso)
{
  try 
  {
  // cogemos la primera asignatura del curso, en principio solo puede haber
      // una asignatura por curso(clase).
    $asignatura = getAsignaturasFromCurso($db,$curso)[0];
    
    $listaOpcionesMenu = explode(",", getConfAsignaturaFromID($db,$asignatura['ID_CONF_ASIGNATURAS'])['OPCIONES_MENU']);
    if ((Count($listaOpcionesMenu)==0)||($listaOpcionesMenu[0]=='TODAS'))
    {
      return true;
    }
    else
    {
      for ($i=0; $i < Count($listaOpcionesMenu); $i++) 
      { 
        if ($listaOpcionesMenu[$i]=="Mi cromo")
        {
          return true;
        }
      }
    }
  } catch(PDOException $ex) 
  {    
    mi_info_log( "An Error occured utilizaCromosCurso ! ".$ex->getMessage());
  } 
  return false;

}
function opcionMenuOk($db,$CORREO,$opcion)
{
  try 
  {
  // cogemos la primera asignatura del curso, en principio solo puede haber
      // una asignatura por curso(clase).
    $aa = getAsignaturasFromCurso($db,getAlumnoFromCorreo($db,$CORREO)['ID_CURSO']);
    if (Count($aa)>0  )
    {
    $asignatura = $aa[0];
    
    $listaOpcionesMenu = explode(",", getConfAsignaturaFromID($db,$asignatura['ID_CONF_ASIGNATURAS'])['OPCIONES_MENU']);
    if ((Count($listaOpcionesMenu)==0)||($listaOpcionesMenu[0]=='TODAS'))
    {
      return true;
    }
    else
    {
      for ($i=0; $i < Count($listaOpcionesMenu); $i++) 
      { 
        if ($listaOpcionesMenu[$i]==$opcion)
        {
          return true;
        }
      }
    }
  }
  else
  {
    return false;
  }
  } catch(PDOException $ex) 
  {    
    mi_info_log( "An Error occured opcionMenuOk ! ".$ex->getMessage());
  } 
  return false;

}


function getAutoevaluacion($db,$id_tarea,$id_alumno)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM AUTOEVALUACION WHERE ID_ALUMNO=".$id_alumno." AND ID_TAREA=".$id_tarea);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getAutoevaluacion ! ".$ex->getMessage());
  } 
  return $fila;
}
function getClanFromCorreo($db,$CORREO)
{
  $fila = NULL;
  try 
  {

    $idAlumno = getAlumnoFromCorreo($db,$CORREO)['ID'];
    $stmt = $db->query("SELECT ID_CLAN FROM ALUMNOS_CLANES WHERE ID_ALUMNO=".$idAlumno." LIMIT 1");
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fila=='')
    {
      return NULL;
    }
    $idClan = $fila['ID_CLAN'];
    $stmt = $db->query("SELECT * FROM CLANES WHERE ID=".$idClan." LIMIT 1");
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getClanFromCorreo ! ".$ex->getMessage());
  } 

  return $fila;
}
function getAlumnoFromCorreo($db,$CORREO)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM ALUMNOS WHERE CORREO='".$CORREO."'");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getAlumnoFromCorreo ! ".$ex->getMessage());
  } 
  return $fila;
}
function getTiendaFromAsignatura($db,$idAsignatura)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM TIENDAS WHERE ID_ASIGNATURA=".$idAsignatura);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getTiendaFromCorreo ! ".$ex->getMessage());
  } 
  return $fila;
}
function getArticuloFromId($db,$IdArticulo)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM ARTICULOS_TIENDA WHERE ID=".$IdArticulo);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getArticuloFromId ! ".$ex->getMessage());
  } 
  return $fila;
}
function getIdSetPreguntaFromIdAsignatura($db,$IdAsignatura)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM ASIGNATURAS_SETSPREGUNTAS WHERE ID_ASIGNATURA= ".$IdAsignatura);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getIdSetPreguntaFromIdAsignatura ! ".$ex->getMessage());
  } 
  return $fila['ID_SETPREGUNTA'];
}


function getSetCromoFromIdCromo($db,$idCromo,$correo){
  try 
  {
  $stmt = $db->query("SELECT * FROM CROMOS WHERE ID = ".$idCromo." LIMIT 1");
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  modificarPoseedorCromo($db, getAlumnoFromCorreo($db,$correo)['ID'], $fila['ID']);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getSetCromoFromIdCromo ! ".$ex->getMessage());
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
   mi_info_log( "An Error occured getCromo ! ".$ex->getMessage());
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
   mi_info_log( "An Error occured getBot ! ".$ex->getMessage());
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
   mi_info_log( "An Error occured getCromoFromID ! ".$ex->getMessage());
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
   mi_info_log( "An Error occured getSitioFromID ! ".$ex->getMessage());
  } 
  return $fila;
}
function getSitioFromMapID($db,$idMap){
  try 
  {
  $stmt = $db->query("SELECT * FROM SITIOS WHERE ID_MAP = ".$idMap);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured getSitioFromMapID ! ".$ex->getMessage());
  } 
  return $fila;
}

function getEstrellasBonosCampActual($db,$CORREO,$fechaInicioCamp)
{
  $vectorTotal = array();
  try
  {
    $alum = getAlumnoFromCorreo($db,$CORREO);
    $stmt = $db->query
    ("SELECT * FROM BONOS WHERE ID_ALUMNO=".$alum['ID']." AND ID_CURSO = ".$alum['ID_CURSO']." AND FECHA_CREACION > '".$fechaInicioCamp."'");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
     
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getEstrellasBonosCampActual:".$ex->getMessage());
  }
  $totalEstrellas = 0;
  foreach ($vectorTotal as $bono) {
    $totalEstrellas+= $bono['NUM_ESTRELLAS'];
  }
  return $totalEstrellas;  
}
function getEstrellasBonos($db,$CORREO)
{
  $vectorTotal = array();
  try
  {
    $alum = getAlumnoFromCorreo($db,$CORREO);
    $stmt = $db->query
    ("SELECT * FROM BONOS WHERE ID_ALUMNO=".$alum['ID']." AND ID_CURSO = ".$alum['ID_CURSO']);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
     
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getEstrellasBonos:".$ex->getMessage());
  }
  $totalEstrellas = 0;
  foreach ($vectorTotal as $bono) {
    $totalEstrellas+= $bono['NUM_ESTRELLAS'];
  }
  return $totalEstrellas;  
}

function getDatosAlumnoTareaEntregados($db,$idTarea)
{
  $vectorTotal = array();
  try
  {

    $stmt = $db->query
    ("SELECT * FROM ALUMNOS_TAREAS WHERE ID_TAREA=".$idTarea." AND FECHA IS NOT NULL");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
     
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getDatosAlumnoTareaEntregados:".$ex->getMessage());
  }
  return $vectorTotal;  
}
function getAlumnosTareasFromTarea($db,$idTarea)
{
  $vectorTotal = array();
  try
  {

    $stmt = $db->query
    ("SELECT * FROM ALUMNOS_TAREAS WHERE ID_TAREA=".$idTarea);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
     
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getAlumnosTareasFromTarea:".$ex->getMessage());
  }
  return $vectorTotal;  
}
function getTareasFromAlumnoEstado($db,$correo,$estado)
{
  $vectorTotal = array();
  try
  {

    $stmt = $db->query
    ("SELECT * FROM ALUMNOS_TAREAS WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." AND ESTADO = '".$estado."'");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
     
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getTareasFromAlumnoEstado:".$ex->getMessage());
  }
  return $vectorTotal;  
}
function getBonusFromCorreo($db,$correo)
{
  $vectorTotal = array();
  try
  {

    $alum = getAlumnoFromCorreo($db,$correo);
    $stmt = $db->query
    ("SELECT * FROM BONOS WHERE ID_ALUMNO=".$alum['ID']." AND ID_CURSO = ".$alum['ID_CURSO']);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
     
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getBonusFromCorreo:".$ex->getMessage());
  }
  return $vectorTotal;  
}
function getTareasFromAlumno($db,$correo)
{
  $vectorTotal = array();
  try
  {

    $stmt = $db->query
    ("SELECT * FROM ALUMNOS_TAREAS WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." ORDER BY ID DESC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
     
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getTareasFromAlumnoEstado:".$ex->getMessage());
  }
  return $vectorTotal;  
}


function getTareasTotalesFromCurso($db,$idCurso)
{
  $asignaturas = getAsignaturasFromCurso($db,$idCurso);
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
    mi_info_log( "Error en getTareasTotalesFromCurso:".$ex->getMessage());
  }
  return $vectorTotal;  
}
function getTareasTotalesFromAlumno($db,$correo,$examen)
{
  $alumno = getAlumnoFromCorreo($db,$correo);
  $asignaturas = getAsignaturasFromCurso($db,$alumno['ID_CURSO']);
  $vectorTotal = array();
  try
  {
  foreach ($asignaturas as $asig) {
    $stmt = $db->query("SELECT * FROM TAREAS WHERE ID_ASIGNATURA = ".$asig['ID']." AND EXAMEN=".$examen);
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }    
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getTareasTotalesFromAlumno:".$ex->getMessage());
  }
  return $vectorTotal;  
}

function yaGeneradoLugarParaHoy($db,$idAlumno,$listaGenAleatoriosHoy)
{
  $yaJugada=0;
  $aGenAleatoriosHoy = explode(",", $listaGenAleatoriosHoy);
  
  if ((Count($aGenAleatoriosHoy)>0)&&(date('Y-m-d')==$aGenAleatoriosHoy[0]))
  {
    //ya jugada?
    for ($i=1; $i < Count($aGenAleatoriosHoy) ; $i++) 
    {
        $aAux = explode(":", $aGenAleatoriosHoy[$i]);
        if ($idAlumno==$aAux[0])
        {
          return 2;
        }
    }
  }
  else
  {
    $yaJugada=1;
  }
  return $yaJugada;
}

function getEventosGeneralesFromAlumno($db,$correo)
{
  $alumno = getAlumnoFromCorreo($db,$correo);
  $asignaturas = getAsignaturasFromCurso($db,$alumno['ID_CURSO']);
  $vectorTotal = array();
  try
  {
  foreach ($asignaturas as $asig) {
    $stmt = $db->query("SELECT * FROM EVENTOS WHERE ID_ASIGNATURA = ".$asig['ID']);
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $idAlumno = getAlumnoFromCorreo($db,$correo)['ID'];
      // se genera un lugar aleatorio entre todos los sitios visibles
      if ($fila['ID_SITIO']==NULL)
      {
        $yaGenerado = yaGeneradoLugarParaHoy($db,$idAlumno,$fila['LISTA_GEN_ALETORIAS_HOY']);
        $listaGenerada=$fila['LISTA_GEN_ALETORIAS_HOY'];
        // no existe lista para hoy
        if ($yaGenerado==1)
        {
          $listaGenerada="".date('Y-m-d');
        }
        // no está generado para hoy
        if ($yaGenerado<=1)
        {

        $aallSitios = getAllSitiosVisibles($db);
        $sitioElegido = $aallSitios[rand(0,Count($aallSitios)-1)];
        $idMap = $sitioElegido['ID_MAP'];
        $posx = rand($sitioElegido['INI_X'],$sitioElegido['MAX_X']);
        $posy = rand($sitioElegido['INI_Y'],$sitioElegido['MAX_Y']);
        $cont = 1000;
        while (existeLugar($db,$sitioElegido['ID'],$posx,$posy)) 
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
        $listaGenerada.=",".$idAlumno.":".$idMap."|".$posx."|".$posy;
        modificarListaGenAleatoriasHoy($db,$fila['ID'], $listaGenerada);

        }
        $eventoModificado = getEventoFromID($db,$fila['ID']);
        
        $aGenAleatoriosHoy = explode(",", $eventoModificado['LISTA_GEN_ALETORIAS_HOY']);
  
        for ($i=1; $i < Count($aGenAleatoriosHoy) ; $i++) 
        {
            $aAux = explode(":", $aGenAleatoriosHoy[$i]);
            if ($idAlumno==$aAux[0])
            {
              $aAux2 = explode("|", $aAux[1]);
              $idMap=$aAux2[0];
              $posx=$aAux2[1];
              $posy=$aAux2[2];
            }
        }

        $fila['ID_MAP'] =$idMap;
        $fila['POS_X'] = $posx;
        $fila['POS_Y'] = $posy;
        

      }
      else
      {
        $sitio = getSitioFromID($db,$fila['ID_SITIO']);
        $fila['ID_MAP'] = $sitio['ID_MAP'];        
      }
      $vectorTotal [] = $fila;
    }
  }
     
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en getEventosGeneralesFromAlumno:".$ex->getMessage());
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
   mi_info_log( "An Error occured getCromo ! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! getNumeroCromosTotalesFromIDSet".$ex->getMessage());
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
   mi_info_log( "An Error occured! getNumeroCromosAbiertosFromIDSet".$ex->getMessage());
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
   mi_info_log( "An Error occured! getNumeroCromosSinAbrirFromIDSet".$ex->getMessage());
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
   mi_info_log( "An Error occured! getNumeroCromosSinAbrirFromIDSet".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
    mi_info_log( "Error en esUsernameAdministrador:".$ex->getMessage());
  }
    return Count($vectorTotal)>0;  
}



function existeLugar($db,$filaSitio,$posx,$posy)
{
  $vectorTotal = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM TAREAS WHERE ID_SITIO = ".$filaSitio." AND POS_X=".$posx." AND POS_Y=".$posy);
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en existeLugar:".$ex->getMessage());
  }
  $vectorTotal2 = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM EVENTOS WHERE ID_SITIO = ".$filaSitio." AND POS_X=".$posx." AND POS_Y=".$posy);
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal2 [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en existeLugar:".$ex->getMessage());
  }
  $vectorTotal3 = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM MIBOT WHERE ID_MAPA_INICIO = ".$filaSitio." AND POS_X_INICIO=".$posx." AND POS_Y_INICIO=".$posy);
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal3 [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en existeLugar:".$ex->getMessage());
  }
    return (Count($vectorTotal)+Count($vectorTotal2)+Count($vectorTotal3))>0;  
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
    mi_info_log( "Error en existeCorreo:".$ex->getMessage());
  }
    return Count($vectorTotal)>0;  
}
function existeAlumnoJuicio($db,$idJuicio,$idAlumno)
{
  $vectorTotal = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM ALUMNOS_JUICIOS WHERE ID_ALUMNO = ".$idAlumno." AND ID_JUICIO = ".$idJuicio);
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en existeAlumnoJuicio:".$ex->getMessage());
  }
    return Count($vectorTotal)>0;  
}

function existeAlumnoId($db,$id)
{
  $vectorTotal = array();
  try
  {
     
    $stmt = $db->query("SELECT * FROM ALUMNOS WHERE ID = ".$id);
   
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }
  catch (PDOException $ex)
  {
    mi_info_log( "Error en existeAlumnoId:".$ex->getMessage());
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
    mi_info_log( "Error en existeFantasma:".$ex->getMessage());
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
    mi_info_log( "Error en existeFantasmaCreado:".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
  return $fila;
}
function getTareaFromID($db,$idTarea)
{
  try 
  {
  $stmt = $db->query("SELECT * FROM TAREAS WHERE ID = ".$idTarea);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! getTareaFromID".$ex->getMessage());
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
   mi_info_log( "An Error occured! setNowUltimaFechaNotiGeneralAlumno ".$ex->getMessage());
  }   
}
function modificarIdGanadorTesoro($db,$correo)
{
  try 
  {
    $alumno = getAlumnoFromCorreo($db,$correo);
  $asignaturas = getAsignaturasFromCurso($db,$alumno['ID_CURSO']);

    $sql = "UPDATE ASIGNATURAS SET ID_GANADOR_TESORO=".$alumno['ID']." WHERE ID=".$asignaturas[0]['ID']." AND ID_GANADOR_TESORO IS NULL";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarIdGanadorTesoro ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}

function modificarVisibleWebFromRetoId($db,$idReto,$value)
{
  try 
  {
    $sql = "UPDATE TAREAS SET VISIBLE_WEB='".$value."' WHERE ID=".$idReto;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarVisibleWebFromRetoId ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarDiaCromDiarioAsignatura($db,$IdAsignatura,$dia)
{
  try 
  {
    $sql = "UPDATE ASIGNATURAS SET DIA_CROM_DIARIO='".$dia."' WHERE ID=".$IdAsignatura;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarDiaCromDiarioAsignatura ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarEstadoCompraFromCompraId($db,$IdCompra,$estado)
{
  try 
  {
    $sql = "UPDATE ARTICULOS_COMPRADORES SET ESTADO='".$estado."' WHERE ID=".$IdCompra;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarEstadoCompraArticuloAlumno ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarComentarioCompraFromCompraId($db,$IdCompra,$coment)
{
  try 
  {
    $sql = "UPDATE ARTICULOS_COMPRADORES SET COMENTARIO='".$coment."' WHERE ID=".$IdCompra;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarComentarioCompraFromCompraId ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarDesactivarJuicios($db,$IdAsignatura)
{
  try 
  {
    $sql = "UPDATE JUICIOS SET ACTIVO=0 WHERE ID_ASIGNATURA=".$IdAsignatura;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarDesactivarJuicios ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarAutoEvaAlumno($db,$idAutoEva,$idAlumno,$nota)
{
  try 
  {
    $sql = "UPDATE AUTOEVALUACION_ALUMNOS SET NOTA=".$nota." WHERE ID_AUTOEVALUACION=".$idAutoEva." AND ID_ALUMNO=".$idAlumno;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarAutoEvaAlumno ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarDesactivarFastests($db,$IdAsignatura)
{
  try 
  {
    $sql = "UPDATE FASTESTS SET ACTIVO=0 WHERE ID_ASIGNATURA=".$IdAsignatura;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarDesactivarFastests ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarAlumnoFastest($db,$aTestAlumno,$idPregunta,$respuestaOK)
{
  try 
  {
    $valorResultado = ($respuestaOK)?1:0;
    $preguntas = $aTestAlumno['PREGUNTAS'];
    $respuestas = $aTestAlumno['RESPUESTAS'];
    $resultado = $aTestAlumno['RESULTADO'];
    $preguntas=$preguntas.",".$idPregunta;
    $respuestas=$respuestas.",".$valorResultado;
    $resultado=$resultado+$valorResultado;

    $sql = "UPDATE ALUMNOS_FASTESTS SET PREGUNTAS='".$preguntas."',RESPUESTAS='".$respuestas."',RESULTADO='".$resultado."' WHERE ID_FASTEST=".$aTestAlumno['ID_FASTEST']. " AND ID_ALUMNO =".$aTestAlumno['ID_ALUMNO'] ;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarAlumnoFastest ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}

function modificarActivarJuicio($db,$idJuicio)
{
  try 
  {
    $sql = "UPDATE JUICIOS SET ACTIVO=1 WHERE ID=".$idJuicio;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarActivarJuicio ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarQuery($db,$sql)
{
  try 
  {
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
    mi_info_log( "An Error occured! modificarQuery ".$ex->getMessage());
    return 'ERROR: '.$ex->getMessage();
  } 
  return "modificado correctamente...";
}

function modificarActivarFastest($db,$idFT)
{
  try 
  {
    $sql = "UPDATE FASTESTS SET ACTIVO=1 WHERE ID=".$idFT;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarActivarFastest ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarComentarioReto($db,$correo,$idTarea,$comentario)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET COMENTARIO='".$comentario."' WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." AND ID_TAREA=".$idTarea;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarComentarioReto ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarAlumnosTareasIncognitoANull($db,$idTarea)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET ID_ALUMNO_A_CORREGIR=NULL,NOTA_CORREGIDA=0,COMENT_CORRECCION='' WHERE ID_TAREA=".$idTarea;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarAlumnosTareasIncognitoANull ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}

function modificarAlumnoTareaIncognito($db,$idTarea,$alumId,$alumIdAcorregir)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET ID_ALUMNO_A_CORREGIR=".$alumIdAcorregir." WHERE ID_TAREA=".$idTarea." AND ID_ALUMNO=".$alumId;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarAlumnoTareaIncognito ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarAlumnoTareaNotaCorregida($db,$idTarea,$alumId,$nota)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET NOTA_CORREGIDA=".$nota." WHERE ID_TAREA=".$idTarea." AND ID_ALUMNO=".$alumId;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarAlumnoTareaNotaCorregida ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarAlumnoTareaComentCorreccion($db,$idTarea,$alumId,$coment)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET COMENT_CORRECCION='".$coment."' WHERE ID_TAREA=".$idTarea." AND ID_ALUMNO=".$alumId;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarAlumnoTareaComentCorreccion ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}

function modificarNumeroEntregasReto($db,$correo,$idTarea,$numeroEntregas)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET NUMERO_ENTREGAS=".$numeroEntregas."  WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." AND ID_TAREA=".$idTarea;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarNumeroEntregasReto ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarOtrosReto($db,$correo,$idTarea,$otros)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET OTROS='".$otros."' WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." AND ID_TAREA=".$idTarea;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarOtrosReto ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}

function modificarFechaUltimoLoginAlumno($db,$correo,$tsmp)
{
  try 
  {
    $sql = "UPDATE ALUMNOS SET ULTIMA_FECHA_LOGIN='".$tsmp."' WHERE ID=".getAlumnoFromCorreo($db,$correo)['ID'];
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarFechaUltimoLoginAlumno ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarConfGeneral($db,$clave,$valor)
{
  try 
  {
    $sql = "UPDATE CONF_GENERALES SET VALOR='".$valor."' WHERE CLAVE='".$clave."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarConfGeneral ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarClan($db,$idClan,$nombre,$imagen,$descripcion)
{
  try 
  {
    $sql = "UPDATE CLANES SET NOMBRE='".$nombre."',IMAGEN='".$imagen."', DESCRIPCION='".$descripcion."' WHERE ID=".$idClan;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarClan ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarFechaEntregadoReto($db,$correo,$idTarea,$tsmp)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET FECHA='".$tsmp."' WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." AND ID_TAREA=".$idTarea;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarFechaEntregadoReto ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}
function modificarEstadoReto($db,$correo,$idTarea,$estado)
{
  try 
  {

    $datosAT = getDatosAlumnoTarea($db,$correo,$idTarea);
    if (($estado=='activado') && ($datosAT['ESTADO']!='no activado'))
    {
      return 0;
    }
    $sql = "UPDATE ALUMNOS_TAREAS SET ESTADO='".$estado."' WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." AND ID_TAREA=".$idTarea;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarEstadoReto ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}


function modificarAlumnoJuicio($db,$idJuicio,$idAlumno,$opcionSeleccionada)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_JUICIOS SET OPCION='".$opcionSeleccionada."',FECHA = now() WHERE ID_ALUMNO=".$idAlumno." AND ID_JUICIO=".$idJuicio;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarEstrellasConseguidasReto ".$ex->getMessage());
  } 
  return $stmt->rowCount();
}

function modificarEstrellasConseguidasReto($db,$correo,$idTarea,$estrellasConseguidas)
{
  try 
  {
    $sql = "UPDATE ALUMNOS_TAREAS SET ESTRELLAS_CONSEGUIDAS=".$estrellasConseguidas." WHERE ID_ALUMNO=".getAlumnoFromCorreo($db,$correo)['ID']." AND ID_TAREA=".$idTarea;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarEstrellasConseguidasReto ".$ex->getMessage());
  } 
  return $stmt->rowCount();
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
   mi_info_log( "An Error occured! cambiarNivelAlumno ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarListaJugadasPPT ".$ex->getMessage());
  }   
}

function modificarVersionAvisada($db,$correo,$nuevaVersion)
{
  try 
  {
    $sql = "UPDATE MIBOT SET VERSION_AVISADA=".$nuevaVersion." WHERE ID = (SELECT ID_MIBOT FROM ALUMNOS WHERE CORREO='".$correo."')";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarVersionAvisada ".$ex->getMessage());
  }   
}
function modificarLocalizacionBot($db,$correo, $mapaInicio, $posX, $posY)
{
  try 
  {
    $sql = "UPDATE MIBOT SET ID_MAPA_INICIO=".$mapaInicio.",POS_X_INICIO=".$posX.",POS_Y_INICIO=".$posY." WHERE ID = (SELECT ID_MIBOT FROM ALUMNOS WHERE CORREO='".$correo."')";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarLocalizacionBot ".$ex->getMessage());
  }   
}
function modificarListaUtilizacionesEvento($db,$id, $nListaUtils)
{
  try 
  {
    $sql = "UPDATE EVENTOS SET LISTA_UTILIZACIONES_HOY='".$nListaUtils."' WHERE ID='".$id."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarUtilizacionesEvento ".$ex->getMessage());
  }   
}
function modificarListaGenAleatoriasHoy($db,$id, $listajugadas)
{
  try 
  {
    $sql = "UPDATE EVENTOS SET LISTA_GEN_ALETORIAS_HOY='".$listajugadas."' WHERE ID='".$id."'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarListaGenAleatoriasHoy ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarOrdenAlbum ".$ex->getMessage());
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
   mi_info_log( "An Error occured! actualizarNivel ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarOrdenCombos ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarOrdenReferenciasTotal ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarOrdenCreadores ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarGeneradoCromo ".$ex->getMessage());
  }   
}
function modificarMaxReferenciaCromoFromSetId($db,$maxReferencia, $idSet)
{
  try 
  {
    $sql = "UPDATE CROMOS SET toughness=".$maxReferencia." WHERE ID_SET=".$idSet;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarGeneradoCromo ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarPoseedorCromo ".$ex->getMessage());
  }   
}


function modificarAtributoAuxCromo($db,$correo,$atributoAux)
{
  try 
  {
    $sql = "UPDATE CROMOS SET ATRIBUTO_AUX='".$atributoAux."' WHERE ID_CREADOR = (SELECT ID FROM ALUMNOS WHERE CORREO='".$correo."')";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarAtributoAuxCromo ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarNEstrellasCromo ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarCromo ".$ex->getMessage());
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
   mi_info_log( "An Error occured! modificarCalas ".$ex->getMessage());
  }   
}
/* por ahora no se utiliza
function modificarEstadoArticulo($db,$IdArticulo,$estado)
{
  try 
  {
    $sql = "UPDATE ARTICULOS_TIENDA SET ESTADO='".$estado."' WHERE ID =".$IdArticulo;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarEstadoArticulo ".$ex->getMessage());
  }   
}
*/
function modificarCantidadArticulo($db,$IdArticulo,$cantidad)
{
  try 
  {
    $sql = "UPDATE ARTICULOS_TIENDA SET CANTIDAD= CANTIDAD + (".$cantidad.")  WHERE ID =".$IdArticulo;
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarCantidadArticulo ".$ex->getMessage());
  }   
}
function modificarBot($db,$correo, $saludo,$palabra_clave,$movilidad,$velocidad,$fantasma,$saltando,$personaje,$porcentajesPPT,$postura1,$postura2,$postura3,$ID_MAPA_INICIO,$POS_X_INICIO,$POS_Y_INICIO)
{
  try 
  {
  
    $setPersonaje=($personaje!=-1)?(",PERSONAJE=".$personaje):"";


    $sql = "UPDATE MIBOT SET PORCENT_PPT='".$porcentajesPPT."'".$setPersonaje.",POSTURA1=".$postura1.",POSTURA2=".$postura2.",POSTURA3=".$postura3.",FANTASMA=".$fantasma.",SALTANDO=".$saltando.",VELOCIDAD=".$velocidad.",MOVILIDAD=".$movilidad.",ID_MAPA_INICIO=".$ID_MAPA_INICIO.",POS_X_INICIO=".$POS_X_INICIO.",POS_Y_INICIO=".$POS_Y_INICIO.",SALUDO='".$saludo."',PALABRA_CLAVE='".$palabra_clave."' WHERE ID = (SELECT ID_MIBOT FROM ALUMNOS WHERE CORREO='".$correo."')";
    $stmt = $db->prepare($sql);
    $stmt->execute();
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! modificarBot ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
  } 
}

function insertarQuery($db,$sql)
{
 try
  {
    $count = $db->exec($sql);
  }
  catch (PDOException $ex)
  {
    return 'ERROR: '.$ex->getMessage();
  } 
  return "insertado correctamente..."; 
}

function insertarArticuloAlumno($db,$idProducto,$alumnoID,$estado,$descripcion,$comentario)
{
    $sentencia= "INSERT INTO ARTICULOS_COMPRADORES(ID_ALUMNO, ID_ARTICULO, ESTADO, DESCRIPCION,COMENTARIO) VALUES (:ID_ALUMNO, :ID_ARTICULO, :ESTADO, :DESCRIPCION, :COMENTARIO)";
  try
  {
  $stmt = $db->prepare($sentencia);
  $stmt->bindParam(':ID_ALUMNO',$alumnoID);
  $stmt->bindParam(':ID_ARTICULO',$idProducto);
  $stmt->bindParam(':ESTADO',$estado);
  $stmt->bindParam(':DESCRIPCION',$descripcion);
  $stmt->bindParam(':COMENTARIO',$comentario);
  $stmt->execute();
    }
catch (PDOException $ex)
{
    mi_info_log( "Error insertarArticuloAlumno:".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
     mi_info_log( "Error inserción alumno:".$ex->getMessage());
  }
  return $vectorTotal;
}

function getSesionesAsignaturaFromDiaSemana($db, $asignatura, $dia){
  try{
    $stmt = $db->query("select substr(SESIONES, ".$dia.", 1) from ASIGNATURAS where ID_asignatura = ".$asignatura);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    mi_info_log( "An Error occured! ".$ex->getMessage());
  }
  return $fila;
}

function getJuiciosComoClase($db){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("select * from JUICIOS WHERE NOMBRE LIKE 'JUICIO_CLASE_%'");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getJuiciosComoClase:".$ex->getMessage());
  }
  return $vectorTotal;
}

function getArticulosFromTienda($db,$id_tienda){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("select * from ARTICULOS_TIENDA WHERE ID_TIENDA=".$id_tienda);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getArticulosFromTienda:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getFastestsComoClase($db){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("select * from FASTESTS WHERE NOMBRE LIKE 'FASTEST_CLASE_%'");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getFastestsComoClase:".$ex->getMessage());
  }
  return $vectorTotal;
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
     mi_info_log( "Error getSesionesAsignaturas:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getAlumnosClan($db,$clanId){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("select * from ALUMNOS_CLANES where ID_CLAN=".$clanId);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getAlumnosClan:".$ex->getMessage());
  }
  return $vectorTotal;
}
function existeAlgunAlumnoFueraDeIdClan($db,$idClan,$aAlumnos)
{
    
  try{
    $vectorTotal = array();

    
    $stmt = $db->query
    ("select * from ALUMNOS_CLANES where ID_CLAN<>".$idClan." AND ID_ALUMNO IN (".implode (", ", $aAlumnos).")");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error existeAlgunAlumnoFueraDeIdClan:".$ex->getMessage());
  }
  return (Count($vectorTotal)==0)?NULL:implode (", ", $aAlumnos);
}

function getEstrellasComportamientoFromCorreo($db,$correo){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("SELECT ID,ESTRELLAS,DIA, (SELECT NOMBRE FROM ASIGNATURAS WHERE ASIGNATURAS.ID = ESTRELLAS.ID_ASIGNATURA) NOMBRE_ASIGNATURA FROM ESTRELLAS WHERE ID_ALUMNO = ".getAlumnoFromCorreo($db,$correo)['ID']." ORDER BY DIA ASC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getEstrellasComportamientoFromCorreo:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getEstrellasMayorQueDiaFromCorreo($db,$correo,$mayorQueDia){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("SELECT ID,ESTRELLAS,DIA, (SELECT NOMBRE FROM ASIGNATURAS WHERE ASIGNATURAS.ID = ESTRELLAS.ID_ASIGNATURA) NOMBRE_ASIGNATURA FROM ESTRELLAS WHERE ID_ALUMNO = ".getAlumnoFromCorreo($db,$correo)['ID']." AND DIA > '".$mayorQueDia."'" );
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $vectorTotal [] = $fila;
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getEstrellasDesdeDiaFromCorreo:".$ex->getMessage());
  }
  return $vectorTotal;
}

function getEstrellasRetosCampActualFromCorreo($db,$correo,$examen,$fechaInicioCamp){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("SELECT ID,ID_TAREA,ESTRELLAS_CONSEGUIDAS,FECHA, (SELECT NOMBRE FROM TAREAS WHERE TAREAS.ID = ALUMNOS_TAREAS.ID_TAREA) NOMBRE_TAREA , (SELECT TOTAL_ESTRELLAS FROM TAREAS WHERE (TAREAS.ID = ALUMNOS_TAREAS.ID_TAREA)) TOTAL_ESTRELLAS, (SELECT FECHA_CREACION FROM TAREAS WHERE (TAREAS.ID = ALUMNOS_TAREAS.ID_TAREA)) FECHA_CREACION FROM ALUMNOS_TAREAS WHERE ID_ALUMNO = ".getAlumnoFromCorreo($db,$correo)['ID']." ORDER BY FECHA DESC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $tarea = getTareaFromID($db,$fila['ID_TAREA']);
      if ($tarea['EXAMEN']==$examen)
      {
        $vectorTotal [] = $fila;
      }
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getEstrellasRetosCampActualFromCorreo:".$ex->getMessage());
  }
  return $vectorTotal;
}
function getEstrellasRetosFromCorreo($db,$correo,$examen){
  try{
    $vectorTotal = array();
    $stmt = $db->query
    ("SELECT ID,ID_TAREA,ESTRELLAS_CONSEGUIDAS,FECHA, (SELECT NOMBRE FROM TAREAS WHERE TAREAS.ID = ALUMNOS_TAREAS.ID_TAREA) NOMBRE_TAREA , (SELECT TOTAL_ESTRELLAS FROM TAREAS WHERE (TAREAS.ID = ALUMNOS_TAREAS.ID_TAREA)) TOTAL_ESTRELLAS FROM ALUMNOS_TAREAS WHERE ID_ALUMNO = ".getAlumnoFromCorreo($db,$correo)['ID']." ORDER BY FECHA DESC");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $tarea = getTareaFromID($db,$fila['ID_TAREA']);
      if ($tarea['EXAMEN']==$examen)
      {
        $vectorTotal [] = $fila;
      }
    }
  }catch(PDOException $ex){
     mi_info_log( "Error getEstrellasRetosFromCorreo:".$ex->getMessage());
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
     mi_info_log( "Error buscarAlumnos:".$ex->getMessage());
  }
  return $vectorTotal;
}

function getEstrellasFromAlumnoDiaAsignatura($db, $IDAlumno, $dia, $asignatura)  {
  try{
    $stmt = $db->query("SELECT ESTRELLAS FROM ESTRELLAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA = ".$asignatura." AND ID_ALUMNO =".$IDAlumno);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    mi_info_log( "An Error occured! ".$ex->getMessage());
  }
  return $fila['ESTRELLAS'];
}
function getFaltasFromAlumnoDiaAsignatura($db, $IDAlumno, $dia, $asignatura)  {
  try{
    $stmt = $db->query("SELECT IFNULL(sum(SESIONES), 0) as nFaltas FROM FALTAS WHERE DIA = '".$dia."' AND ID_ASIGNATURA = ".$asignatura." AND ID_ALUMNO =".$IDAlumno);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    mi_info_log( "An Error occured! ".$ex->getMessage());
  }
  return $fila['nFaltas'];
}
function getFaltasFromAlumnoID($db, $IDAlumno)  {
  try{
    $stmt = $db->query("select IFNULL(sum(SESIONES), 0) as nFaltas from FALTAS where ID_ALUMNO = ".$IDAlumno);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    mi_info_log( "An Error occured! ".$ex->getMessage());
  }
  return $fila['nFaltas'];
}
function getEstrellasFromAlumnoID($db, $IDAlumno)  {
  try{
    $stmt = $db->query("select IFNULL(sum(ESTRELLAS), 0) as nEs from ESTRELLAS where ID_ALUMNO = ".$IDAlumno);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    mi_info_log( "An Error occured! ".$ex->getMessage());
  }
  return $fila['nEs'];
}
function getConfGeneral($db, $clave)  {
  try{
    $stmt = $db->query("select VALOR from CONF_GENERALES where CLAVE= '".$clave."'");
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  catch(PDOException $ex){
    mi_info_log( "An Error occured! ".$ex->getMessage());
  }
  return $fila['VALOR'];
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
   mi_info_log( "An Error occured! ".$ex->getMessage());
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
    mi_info_log( "Error en getSetsCromos:".$ex->getMessage());
  }
    return $vectorTotal;
}

function getAsignaturaFromAsignaturaID($db,$IDAsignatura)
{
  $fila=NULL;
  try 
  {
  $stmt = $db->query("SELECT * FROM ASIGNATURAS WHERE ID=".$IDAsignatura);
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch(PDOException $ex) 
  {    
   mi_info_log( "An Error occured! getAsignaturaFromAsignaturaID".$ex->getMessage());
  } 
  return $fila;
}




?>