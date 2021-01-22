<?php
 function getColorDay()
 {
 	/*$sColores = array(
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
	*/
	$diaSemana = date('N'); 
	$sPO= array("264653","2a9d8f","e76f51");
	$sPC= array("e9c46a","f4a261");

//echo "diaSemana:".$diaSemana.":";

	switch ($diaSemana) {
		case 1:
	$sPO= array("e63946","457b9d","1d3557");
	$sPC= array("f1faee","a8dadc");
			break;
		case 2:
	$sPO= array("003049","d62828","f77f00");
	$sPC= array("fcbf49","eae2b7");
			break;
		case 3:
	$sPO= array("000000","14213d","fca311");
	$sPC= array("e5e5e5","ffffff");
			break;
		case 4:
	$sPO= array("22223b","4a4e69","9a8c98");
	$sPC= array("c9ada7","f2e9e4");
			break;
		case 5:
	$sPO= array("0081a7","00afb9","f07167");
	$sPC= array("fdfcdc","fed9b7");
			break;
		case 6:
	$sPO= array("335c67","9e2a2b","540b0e");
	$sPC= array("fff3b0","e09f3e");
			break;
		case 7:
	$sPO= array("264653","2a9d8f","e76f51");
	$sPC= array("e9c46a","f4a261");
			break;
	}

 	$mes = date('n'); //1..12
 	$dia = date('j'); //1..31
 	
 	$nSemana = date('W'); 
 	$nDiasMes = date('t'); 
 	$a1 = $mes;
 	$a2 = $dia;
	$a3 = $dia+$mes+$diaSemana+$nSemana+$nDiasMes;

 	$aAux = array();

 	$aAux[]=$sPO[($a1+$a2) % 3];
	$aAux[]=$sPC[($a1+$a3) % 2];
	//var_export($aAux);
	return $aAux;

 }
?>