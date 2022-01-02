<?php
require_once 'bootstrap.php';
$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('plantillas/anexo21.docx');

// alumno
$templateProcessor->setValue('NOMBRE_ALUMNO', 'Miguel de los Mozos');
$templateProcessor->setValue('DNI_ALUMNO', '44555645J');

// tutor colegio
$templateProcessor->setValue('NOMBRE_COMPLETO_TUTOR_COLEGIO', 'ALBERTO FERNÁNDEZ SÁNCHEZ');
$templateProcessor->setValue('NIF_TUTOR_COLEGIO', '3468875K');

//CICLOS de FCT

$templateProcessor->setValue('CLAVE_CICLO', 'IFCB01');
$templateProcessor->setValue('NOMBRE_CICLO', 'FP BÁSICA INFORMÁTICA Y COMUNICACIONES');

//FCT Periodo
$templateProcessor->setValue('CURSO_ACADEMICO', '2020-2021');
$templateProcessor->setValue('FECHA_INICIO', '12/04/2021');
$templateProcessor->setValue('FECHA_TERMINACION', '18/05/2021');
$templateProcessor->setValue('HORAS_DIA', '6');
$templateProcessor->setValue('TOTAL_HORAS', '160');
$templateProcessor->setValue('FECHA_FIRMA', '18 de MARZO de 2021');

//FCT Prácticas


$templateProcessor->setValue('NOMBRE_TUTOR_EMPRESA', 'GILBERT SOS POS');
$templateProcessor->setValue('CONTACTO_TUTOR_EMPRESA', 'EmpreSAS S.A.');



//Empresa
$templateProcessor->setValue('NCONVENIO', '345');
$templateProcessor->setValue('NOMBRE_EMPRESA', 'NUBES DE COLORES EDUCACIÓN SL');
$templateProcessor->setValue('FECHA_CONVENIO', '22 de  ENERO de  2021');
$templateProcessor->setValue('LOCALIDAD_EMPRESA', 'MADRID');
$templateProcessor->setValue('DIRECCION_EMPRESA', 'Marqués de Mondéjar, 32');
$templateProcessor->setValue('NOMBRE_REPRESENTANTE_EMPRESA', 'José Manuel Sauras Villanova');

$templateProcessor->saveAs('../docsFCT/anexo21_34.docx');
?>