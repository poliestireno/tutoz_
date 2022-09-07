<?php
  session_start();
  //error_reporting(0);
  include('includes/config.php');
  require_once("UTILS/dbutils.php");
  $msg="";
  if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
  	{	
  header('location:index.php');
  }
  else{
   

  if(isset($_POST['submit']))
  {	
   
    // Carga los datos del cromo
    $CORREO = $_SESSION['alogin'];
    $cromo = getCromo($dbh,$CORREO);
    $imagen = $cromo['picture'] ;
    
  	if (isset($_FILES['uploadimage']))
  	{
  	  try {
        // Si esta solicitud entra en algun error, se trata como no válida, invalidando completamente la subida de archivos que no sean imagenes.
        if (
          !isset($_FILES['uploadimage']['error']) ||
          is_array($_FILES['uploadimage']['error'])
        ) {
          throw new RuntimeException('Parámetro no válido.');
        }
        // Comprueba el valor de $_FILES['uploadimage']['error'].
        switch ($_FILES['uploadimage']['error']) {
          case UPLOAD_ERR_OK:
            break;
          case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No se ha enviado ningún archivo.');
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Se ha superado el límite del tamaño de los archivo.');
          default:
            throw new RuntimeException('Error desconocido.');
        }
      
        // Aquí comprueba el tamaño de los archivos.
        if ($_FILES['uploadimage']['size'] > 1000000) {
          throw new RuntimeException('Se ha superado el límite del tamaño del archivo.');
        }
      
        // ¡¡¡No confíes en el valor de $_FILES['uploadimage']['mime']!!!
        // Compruebe usted mismo el tipo MIME. Porque puede ser una imagen JPEG que se sube como text/plain. y se podria bypassear este filtro
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
          $finfo->file($_FILES['uploadimage']['tmp_name']),
          array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
          ),
          true
        )) {
          throw new RuntimeException('Formato del archivo no válido.');
        }
      
        // El valor de la imagen hasheado se guarda en la variable $file_loc.
        // Porque el archivo temporal se borra despues de subirse, por lo que primero se hashea el archivo para tener el valor y luego se sube a la carpeta de imagenes.
        $file_loc=sprintf('%s.%s',
        sha1_file($_FILES['uploadimage']['tmp_name']),$ext);
    
        // Aqui genera una string hasheada del archivo temporal y la sube a la carpeta /imagesCromos/.
        // Gracias a esto conseguimos que el nombre del archivo sea unico y no se repita.
        // Con 'sprintf' creamos una cadena con el nombre del archivo y la extensión.
        // No cambiar a $_FILES['uploadimage']['name'] dado a que perderia completamente la seguridad de la subida de archivos.
        if (!move_uploaded_file(
          $_FILES['uploadimage']['tmp_name'],
          sprintf('./imagesCromos/%s.%s',
            sha1_file($_FILES['uploadimage']['tmp_name']),
            $ext
          )
        )) {
          throw new RuntimeException('No se ha podido subir el archivo.');	
        }
        $imagen=$file_loc;
        } catch (RuntimeException $e) {
          echo $e->getMessage();
        }       

  	} 

    modificarCromo($dbh,$_SESSION['alogin'],
		(!isset($_POST['nombre']))?"":$_POST['nombre'],
		(!isset($_POST['color']))?"White":$_POST['color'],
		(!isset($_POST['nestrellas']))?"":$_POST['nestrellas'],
		(!isset($_POST['atributo']))?"":$_POST['atributo'],
		'Common',
		(!isset($_POST['descripcion']))?"":$_POST['descripcion'],
		(!isset($_POST['artista']))?"":$_POST['artista'],
		(!isset($_POST['firma']))?$_POST['firmaini']:$_POST['firma'],
    (!isset($_POST['imagen'])?:$imagen));
	$msg=" Información actualizada correctamente";
  }    
  ?>
<!doctype html>
<html lang="en" class="no-js">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#3e454c">
    <title>Editar Mi cromo</title>
    <!-- Font awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Sandstone Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Bootstrap Datatables -->
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <!-- Bootstrap social button library -->
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <!-- Bootstrap select -->
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <!-- Bootstrap file input -->
    <link rel="stylesheet" href="css/fileinput.min.css">
    <!-- Awesome Bootstrap checkbox -->
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <!-- Admin Stye -->
    <link rel="stylesheet" href="css/style.css">
    <style>
      .errorWrap {
      padding: 10px;
      margin: 0 0 20px 0;
      background: #dd3d36;
      color:#fff;
      -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
      box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
      }
      .succWrap{
      padding: 10px;
      margin: 0 0 20px 0;
      background: #5cb85c;
      color:#fff;
      -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
      box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
      }
    </style>
  </head>
  <body>
    <?php
       $CORREO = $_SESSION['alogin'];
       $cromo = getCromo($dbh,$CORREO);
       $getPropsAlummo =  getPropsVisiblesCromo($dbh,$_SESSION['alogin']);
       ?>
    <?php include('includes/header.php');?>
    <div class="ts-main-content">
      <?php include('includes/leftbar.php');?>
      <div class="content-wrapper">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-12">
                  <div class="panel panel-default">
                    <div class="panel-heading">MI CROMO</div>
                    <?php if($msg){?>
                    <div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div>
                    <?php }
                      function url(){
                        return sprintf(
                          "%s://%s%s",
                          isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
                          $_SERVER['SERVER_NAME'],
                          $_SERVER['REQUEST_URI']
                        );
                      }?>
  
                    <div class="panel-body">
                      <form method="post" class="form-horizontal" enctype="multipart/form-data">
                        <input type="hidden" name="nestrellas" class="form-control" value="<?php echo htmlentities($cromo['mana_w']);?>">
                        <input type="hidden" name="firmaini" class="form-control" value="<?php echo htmlentities($cromo['bottom']);?>">
                        <div class="form-group">
                          <div class="col-sm-4">
                          </div>
                          <div class="col-sm-4 text-center">
                            <img src="https://www.mtgcardmaker.com/mcmaker/createcard.php?name=<?php echo $cromo['name'];?>&color=<?php echo $cromo['color'];?>&mana_w=<?php echo $cromo['mana_w'];?>&picture=<?php echo htmlentities(substr(url(),0,strrpos(url(), '/')).'/imagesCromos/'.$cromo['picture'])?>&cardtype=<?php echo 
                              (($cromo['cardtype']!='')?(
                              ((getValorAtributo($dbh,$CORREO)>=0)?'%2B':'').getValorAtributo($dbh,$CORREO).'  '
                              ):'').$cromo['cardtype'];?>&rarity=<?php echo $cromo['rarity'];?>&cardtext=<?php echo $cromo['cardtext'];?>&power=&toughness=<?php echo $cromo['toughness'];?>&artist=<?php echo $cromo['artist'];?>&bottom=<?php echo $cromo['bottom'];?>" style="width:250px; border-radius:5%; margin:10px;">
                          </div>
                          <div class="col-sm-4">
                          </div>
                        </div>
 
                        <div class="form-group">
                          <?php if ($getPropsAlummo['nombre']==1) {?>
                          <label class="col-sm-2 control-label">Nombre</label>
                          <div class="col-sm-4">
                            <input type="text" maxlength = "20" name="nombre" class="form-control"  value="<?php echo htmlentities($cromo['name']);?>">
                          </div>
                          <?php } if ($getPropsAlummo['artista']==1) {?>
                          <label class="col-sm-2 control-label">Artista</label>
                          <div class="col-sm-4">
                            <input type="text" name="artista" maxlength = "40" class="form-control"  value="<?php echo htmlentities($cromo['artist']);?>">
                          </div>
                          <?php } ?>
                        </div>
                        <div class="form-group">
                          <?php if ($getPropsAlummo['atributo']==1) {?>
                          <label class="col-sm-2 control-label">Atributo<span style="font-size: 200%;"><?php echo ((($cromo['cardtype']!=''))?(" (".getValorAtributo($dbh,$CORREO).")"):"")?></span></label>
                          <div class="col-sm-4">
                            <input type="text" maxlength = "20" name="atributo" class="form-control"  value="<?php echo htmlentities($cromo['cardtype']);?>">
                          </div>
                          <?php } if ($getPropsAlummo['descripcion']==1) {?>
                          <label class="col-sm-2 control-label">Descripción</label>
                          <div class="col-sm-4">
                            <input type="text" name="descripcion" maxlength = "299" class="form-control"  value="<?php echo htmlentities($cromo['cardtext']);?>">
                          </div>
                          <?php } ?>
                        </div>
                        <div class="form-group">
                          <?php if ($getPropsAlummo['color']==1) {?>
                          <label class="col-sm-2 control-label">Color</label>
                          <div class="col-sm-4">
                            <!--input type="text" name="color" class="form-control"  value="<?php echo htmlentities($cromo['color']);?>"-->
                            <select name="color" class="form-control">
                              <option value="White" <?php echo (($cromo['color']=="White")?" selected='selected' ":"")?>>
                                Blanco
                              </option>
                              <option value="Blue"<?php echo (($cromo['color']=="Blue")?" selected='selected' ":"")?>>
                                Azul
                              </option>
                              <option value="Black"<?php echo (($cromo['color']=="Black")?" selected='selected' ":"")?>>
                                Negro
                              </option>
                              <option value="Red"<?php echo (($cromo['color']=="Red")?" selected='selected' ":"")?>>
                                Rojo
                              </option>
                              <option value="Green"<?php echo (($cromo['color']=="Green")?" selected='selected' ":"")?>>
                                Verde
                              </option>
                              <option value="Gold"<?php echo (($cromo['color']=="Gold")?" selected='selected' ":"")?>>
                                Oro
                              </option>
                            </select>
                          </div>
                          <?php } if ($getPropsAlummo['firma']==1) {?>
                          <label class="col-sm-2 control-label">Firma</label>
                          <div class="col-sm-4">
                            <input type="text" maxlength = "50" name="firma" class="form-control"  value="<?php echo htmlentities($cromo['bottom']);?>">
                          </div>
                          <?php } ?>
                        </div>
                        <!--div class="form-group">
                          <label class="col-sm-2 control-label">Orden</label>
                          <div class="col-sm-4">
                          <input type="text" readonly="readonly" name="orden" class="form-control"  value="<?php echo htmlentities($cromo['power']);?>">
                          </div>
                          <label class="col-sm-2 control-label">Nº Cromos totales</label>
                          <div class="col-sm-4">
                          <input type="text" readonly="readonly" name="ncrto" class="form-control"  value="<?php echo htmlentities($cromo['toughness']);?>">
                          </div>
                          </div-->
                        <div class="form-group">
                          <?php if ($getPropsAlummo['imagen']==1) {?>
                          <label class="col-sm-2 control-label">imagen<span style="color:red"></span></label>
                          <div class="col-sm-4 text-center">
                            <img src="<?php echo htmlentities(substr(url(),0,strrpos(url(), '/')).'/imagesCromos/'.$cromo['picture']);?>" style="width:200px;height: 100px; border-radius:3%; margin:10px;">
                            <input type="file" name="uploadimage" class="form-control">
                            <input type="hidden" name="imagen" class="form-control" value="<?php echo htmlentities($cromo['picture']);?>">
                          </div>
                          <?php  }?>
                          <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" name="submit" type="submit">Guardar cambios</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Loading Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/fileinput.js"></script>
    <script src="js/chartData.js"></script>
    <script src="js/main.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {          
      setTimeout(function() {
      	$('.succWrap').slideUp("slow");
      }, 3000);
      });
    </script>
  </body>
</html>
<?php } ?>