<?php
include('../../includes/config.php');
require_once("../../UTILS/dbutils.php");

$row = 1;
if (($handle = fopen("empresas.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $num = count($data);
    echo "<p> $num fields in line $row: <br /></p>\n";
    $row++;
    for ($c=0; $c < $num; $c++) {
        echo $data[$c] . "<br />\n";
    }
    insertarFCTEmpresa($dbh,$data[0],$data[1],$data[2]);
  }
  fclose($handle);
}
  ?>