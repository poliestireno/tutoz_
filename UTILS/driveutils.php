<?php
include_once 'google-api-php-client--PHP7.4/vendor/autoload.php';

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
<!--html>
    <head>
        <title>Subir ficheros a Google drive.</title>
    </head>
    <body>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
            <label for="documento">Documento:</label>
            <input type="file" name="documento" required /><br>
            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" required /><br>
            <input type="submit" name="enviar" value="Subir documento a Google Drive" />
        </form>
    </body>
</html-->