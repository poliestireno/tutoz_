<?php
include_once 'google-api-php-client--PHP7.4/vendor/autoload.php';

function listarEventosCalendar()
{
    $pathJSON = 'luminous-smithy-337021-6e7d38f3f7db.json';
    global $results;
    //configurar variable de entorno
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.dirname(__FILE__)."/".$pathJSON);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope(Google_Service_Calendar::CALENDAR);
    try{  

      $service = new Google_Service_Calendar($client);
      $optParams=array();
$optParams['singleEvents'] = true;
$optParams['timeMin'] = date("c", strtotime(date('Y-m-d H:i:s').'-0 days'));
$optParams['timeMax'] = date("c", strtotime(date('Y-m-d H:i:s').'+7 days'));
// calendario de la salle general  $results = $service->events->listEvents('lasalleinstitucion.es_943bfgn19d6nl8ud67np4bghlc@group.calendar.google.com', $optParams);

//$results = $service->events->listEvents('lasalleinstitucion.es_933u0kscb27fig9ah40mdq89nk@group.calendar.google.com', $optParams);
//MI_SALLE $results = $service->events->listEvents('c_0qao0gq06u9qk0957pk3lm93hs@group.calendar.google.com', $optParams);
$results = $service->events->listEvents('lasalleinstitucion.es_943bfgn19d6nl8ud67np4bghlc@group.calendar.google.com', $optParams);

//var_export($results);
//$results = $service->events->listEvents('afsanchez@lasalleinstitucion.es', $optParams);
//$results = $service->events->listEvents('afsanchez@lasalleinstitucion.es', array());
//$results =  $service->calendarList->listCalendarList();



    }catch(Google_Service_Exception $gs){
        $m=json_decode($gs->getMessage());
        echo $m->error->message;
    }catch(Exception $e){
        echo $e->getMessage();
      
    }
}

function addEvent($calendarId, $summary, $description, $location, $dataTimeStart, $dataTimeEnd, $email, $accept)
 {
     $event = new Google_Service_Calendar_Event();
     $event->setSummary($summary);
     $event->setLocation($location);
     $event->setDescription($description);
     $event->setVisibility('public');
     $start = new Google_Service_Calendar_EventDateTime();
     $start->setDateTime($dataTimeStart);
     $start->setTimeZone('America/Bogota');
     $event->setStart($start);
     $end = new Google_Service_Calendar_EventDateTime();
     $end->setDateTime($dataTimeEnd);
     $end->setTimeZone('America/Bogota');
     $event->setEnd($end);
     $reminder1 = new Google_Service_Calendar_EventReminder();
     $reminder1->setMethod('email');
     $reminder1->setMinutes('55');
     $reminder2 = new Google_Service_Calendar_EventReminder();
     $reminder2->setMethod('email');
     $reminder2->setMinutes('15');
     $reminder = new Google_Service_Calendar_EventReminders();
     $reminder->setUseDefault('false');
     $reminder->setOverrides(array($reminder1, $reminder2));
     $event->setReminders($reminder);
     //$event->setRecurrence(array('RRULE:FREQ=WEEKLY;UNTIL=20110701T170000Z'));
     $attendee1 = new Google_Service_Calendar_EventAttendee();
     $attendee1->setEmail($email);
     if ($accept == "true") {
         $attendee1->setResponseStatus('accepted');
     }
     $attendees = array($attendee1);
     $event->attendees = $attendees;
     $optParams = array('sendNotifications' => true, 'maxAttendees' => 1000);
     /*$creator = new Google_Service_Calendar_EventCreator();
             $creator->setDisplayName("UNAD Calendar");
             $creator->setEmail("106295480288-s6a44jaogn7pembonh8mudn4gutbn28n@developer.gserviceaccount.com");
     
             $event->setCreator($creator);*/
     $nEvent = $this->service->events->insert($calendarId, $event, $optParams);
     return $nEvent;
 }


function crearCarpetaDrive($nombreCarpeta,$descripcion,$idFolder){
    //$claveJSON = '1jU6GD0c_H33gM_TFjgdmSUHRRE_iGlbV';
    $pathJSON = 'luminous-smithy-337021-6e7d38f3f7db.json';

    //configurar variable de entorno
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.dirname(__FILE__)."/".$pathJSON);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/drive.file']);
    try{        
        //instanciamos el servicio
        $service = new Google_Service_Drive($client);

        $folder = new Google_Service_Drive_DriveFile();

        $folder->setName($nombreCarpeta);
        $folder->setMimeType('application/vnd.google-apps.folder');
        //id de la carpeta donde hemos dado el permiso a la cuenta de servicio 
        $folder->setParents(array($idFolder));
        $folder->setDescription($descripcion);
        $results4 = $service->files->listFiles();
        $crear=true;
        $resultado =1;
        foreach ($results4 as $element) 
        {
            if ($element['name']==$nombreCarpeta)
            {
                $crear=false;
                $resultado=$element['id'];
            }
        }        
        if ($crear)
        {
            $result = $service->files->create($folder);
            $resultado=$result['id'];
        }
        return $resultado;

    }catch(Google_Service_Exception $gs){
        $m=json_decode($gs->getMessage());
        echo $m->error->message;
    }catch(Exception $e){
        echo $e->getMessage();
      
    }
}
function subirDocumentoWordDrive($documento,$nombre,$descripcion,$idFolder){
    // Variables de credenciales.
    //$claveJSON = '1jU6GD0c_H33gM_TFjgdmSUHRRE_iGlbV';
    $pathJSON = 'luminous-smithy-337021-6e7d38f3f7db.json';

    //configurar variable de entorno
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.dirname(__FILE__)."/".$pathJSON);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/drive.file']);
    try{        
        //instanciamos el servicio
        $service = new Google_Service_Drive($client);

        //instacia de archivo
        $file = new Google_Service_Drive_DriveFile();
        $file->setName($nombre);

        //obtenemos el mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE); 
        $mime_type="application/msword";
        //id de la carpeta donde hemos dado el permiso a la cuenta de servicio 
        $file->setParents(array($idFolder));
        $file->setDescription($descripcion);
        $file->setMimeType($mime_type);

        $result = $service->files->create(
          $file,
          array(
            'data' => file_get_contents($documento),
            'mimeType' => $mime_type,
            'uploadType' => 'media',
          )
        );

    }catch(Google_Service_Exception $gs){
        $m=json_decode($gs->getMessage());
        echo $m->error->message;
    }catch(Exception $e){
        echo $e->getMessage();
      
    }
}
/*if($_SERVER["REQUEST_METHOD"] == "POST"){
    $documento = htmlspecialchars($_FILES['documento']['name']);
    var_export($documento);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    var_export($_FILES['documento']);
    // Subimos el documento a nuestro servidor.
    if(move_uploaded_file($_FILES['documento']['tmp_name'], $documento)){
        echo "1.- Fichero subido al servidor. ";
        $idCarpetaRaiz = '1jU6GD0c_H33gM_TFjgdmSUHRRE_iGlbV';
        //$idFolder = crearCarpetaDrive("AMBAR","Setas suaves",$idCarpetaRaiz);
        subirDocumentoDrive($documento,"koko.docx",$descripcion,$idCarpetaRaiz);
        
        if (unlink($documento)){
            echo "3.- Fichero eliminado del servidor";
        }else{
            echo 'Error: No se ha podido eliminar el documento "'.$documento.'" en el servidor.';
        }       
    }else{
        echo "Error: Se ha producido un error, intentelo de nuevo.";
    }
    
}
*/
?>