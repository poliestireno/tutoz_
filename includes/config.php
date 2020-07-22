 <?php 

$array_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/sallez.ini');

//print_r($array_ini);


define('DB_HOST',$array_ini['DB_HOST']);define('DB_USER',$array_ini['DB_USER']);define('DB_PASS',$array_ini['DB_PASS']);define('DB_NAME',$array_ini['DB_NAME']);

//define('DB_HOST','localhost');define('DB_USER','u329316246_gilbertSallex');define('DB_PASS','gilbert7');define('DB_NAME','u329316246_sallex');

try
{
	$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
	exit("Error: " . $e->getMessage());
}
?>
