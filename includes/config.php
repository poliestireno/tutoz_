 <?php 
 
//Pongo 60 minutos para que expire la sesión en inactivo.
$expireAfter = 60;

//var_export($_SESSION);

if ((isset($_SESSION['alogin']))&&($_SESSION['alogin']=='ADMIN'))
{
    $expireAfter = 2880;// 2 días en administrador
}

//  var_export($expireAfter);


$fin = false;
//Check to see if our "last action" session
//variable has been set.
if(isset($_SESSION['last_action'])){
    
    //Figure out how many seconds have passed
    //since the user was last active.
    $secondsInactive = time() - $_SESSION['last_action'];
    
    //Convert our minutes into seconds.
    $expireAfterSeconds = $expireAfter * 60;
    
    //Check to see if they have been inactive for too long.
    if($secondsInactive >= $expireAfterSeconds){
        //User has been inactive for too long.
        //Kill their session.
        session_unset();
        session_destroy();
        $fin = true;
    }
    
}
 
//Assign the current timestamp as the user's
//latest activity
$_SESSION['last_action'] = time();



if (!$fin)
{
	$array_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/sallez.ini');

	//print_r($array_ini);


	define('DB_HOST',$array_ini['DB_HOST']);define('DB_USER',$array_ini['DB_USER']);define('DB_PASS',$array_ini['DB_PASS']);define('DB_NAME',$array_ini['DB_NAME']);

	//define('DB_HOST','localhost');define('DB_USER','u329316246_gilbertSallex');define('DB_PASS','gilbert7');define('DB_NAME','u329316246_sallex');

	try
	{
		$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (PDOException $e)
	{
		exit("Error: " . $e->getMessage());
	}
}
//var_export($_SESSION);
?>
