<?php
 function getColorDay()
 {
 	$sColores = array(
 		"5cb8e7","c150ff","79ff7a","e9ddb3","fea28b",
 		"e7af4e","d4ff55","ff6462","864de8","69e2ff",
 		"ab40e8","ff5f31","5cc7ff","99e897","ffe26d",
 		"f3deff","9e78b3","ebc5ff","a4b265","f4ffc5",
	 	"90fecd","42b282","75ffc3","b1422f","ff8a76",
	 	"bfdbff","6085b2","a4ceff","b29050","ffdfa4",
	 	"8fcfff","21587f","42afff","48687f","348ccc",
	 	"ffecce","7f6741","ffcf83","7f7667","cca668",
	 	"ceffbd","4a7f37","94ff6f","677f5f","78cc5a"
	 	);
 	$mes = date('n'); //1..12
 	$dia = date('j'); //1..31
 	$diaSemana = date('N'); 
 	$nSemana = date('W'); 
 	$nDiasMes = date('t'); 
 	$a1 = $mes % 9;
 	$a2 = $dia % 5;
	$a3 = ($dia+$mes+$diaSemana+$nSemana+$nDiasMes) % 5;

 	$aAux = array();

 	$aAux[]=$sColores[($a1*5)+$a2];
	$aAux[]=$sColores[($a1*5)+$a3];
	//var_export($aAux);
	return $aAux;

 }
?>