<?php
//###############################################################################
// Name:        CloudManager
// Description: A simple cloud based game load and save system for RPG Maker MV
// Version:     0.9.4 (WAMPP/XAMPP Bugfix)
// Author:      Frank A. Grenzel
// License:     CC BY 3.0 (https://creativecommons.org/licenses/by/3.0/)
//###############################################################################
//
// ### Terms of Use ###
// The Cloud Manager is distributed as is under the creative commons license CC BY 3.0
// (Attribution 3.0 Unported) for free.
// You are free to share, copy, redistribute or edit it for any purpose, even commercially
// under the following terms: You must give appropriate credit, provide a link to the
// license, and indicate if changes were made. You may do so in any reasonable manner,
// but not in any way that suggests the licensor endorses you or your use.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
// INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
// PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
// ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
// OTHER DEALINGS IN THE SOFTWARE.
session_start();
include('../includes/config.php');
require_once("../UTILS/dbutils.php");

	if(!isset($_POST["action"])) die("No action found");
	if(!isset($_POST["gameName"])) die("No game name found");
	if(!isset($_POST["gameVersion"])) die("No game version found");

	try {

		$action      = $_POST["action"];
		$gameName    = $_POST["gameName"];
		$gameVersion = $_POST["gameVersion"];
		
		$gameData    = null;
		$userName    = null;
		$password    = null;
		$guid        = null;
		$time        = time();
		
		if (isset($_POST["gameData"])) $gameData = $_POST["gameData"];
		if (isset($_POST["userName"])) $userName = $_POST["userName"];
		if (isset($_POST["password"])) $password = md5($_POST["password"]);
		if (isset($_POST["guid"]))     $guid     = $_POST["guid"];

		$array       = array();
		$result      = "";

			
		$db = new PDO('sqlite:rmmv.sqlite');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		switch ($action) {
				case "getUserSession":	
				// ñapa para probar desde local metiendo en session 
				// un correo especifico
				if ($_SERVER['DOCUMENT_ROOT']=='/opt/lampp/htdocs')
				{
					$_SESSION['alogin']="GE@GE";
				}
				
				$array = array();
				if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
				{	
					$array['_userName'] = "";
					$array['_password'] = "";
				}
				else
				{
					$array['_userName'] = $_SESSION['alogin'];
					$stmt = $dbh->query("SELECT password FROM ALUMNOS WHERE CORREO='".$_SESSION['alogin']."'");
  					$fila = $stmt->fetch(PDO::FETCH_ASSOC);
					$array['_password'] = $fila['password'];
				}	
				
				$result = json_encode($array);
				//$result = "333";
				break;
			case "save":
				
				// Look if guid contains @ -> Use userName and password!
				if (strpos($guid, "@USERGAME@") !== false) {

// borrar todas excepto la ultima
$sql = "SELECT * FROM savegames WHERE gameName='".$gameName."' AND userName='".$userName."@".$password."' ORDER BY id DESC";

		$select = $db->query($sql);
		$rows = $select->fetchAll();
		for ($i=1; $i < Count($rows); $i++) { 
			$sql = "DELETE FROM savegames WHERE id=".$rows[$i]['id'];
			$rowsDelete = $db->exec($sql);			
		}
// insertar la nueva, la idea es que haya 2, la ultima y la anterior de backup.
		$sql = "INSERT INTO savegames (data,gameName,gameVersion,userName,timestamp) VALUES ('".$gameData."','".$gameName."',".$gameVersion.",'".$userName."@".$password."',".$time.")";
				} else {
					$sql = "INSERT INTO savegames (data,gameName,gameVersion,userName,timestamp) VALUES ('".$gameData."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";
				};
				$rows = $db->exec($sql);
				$result = $rows." games saved";

				break;
			case "load":
				// Look if guid contains @ -> Use userName and password!
				if (strpos($guid, "@USERGAME@") !== false) {
					$sql = "SELECT * FROM savegames WHERE gameName='".$gameName."' AND userName='".$userName."@".$password."' ORDER BY id DESC LIMIT 1";				
				} else {
					$sql = "SELECT * FROM savegames WHERE gameName='".$gameName."' ORDER BY id DESC LIMIT 1";
				};

				$select = $db->query($sql);	
				$rows = $select->fetchAll();

				$result = $rows[0]['data'];

				break;
			case "check":
				// Look if guid contains @ -> Use userName and password!
				if (strpos($guid, "@USERGAME@") !== false) {
					$sql = "SELECT * FROM savegames WHERE gameName='".$gameName."' AND userName='".$userName."@".$password."' ORDER BY id DESC LIMIT 1";				
				} else {
					$sql = "SELECT * FROM savegames WHERE gameName='".$gameName."' ORDER BY id DESC LIMIT 1";
				};

				$select = $db->query($sql);
				$rows = $select->fetchAll();

				if (count($rows)>0) {
					if (floatval($gameVersion)<floatval($rows[0]['gameVersion'])) {
						$array['Version'] = 0;
					} else {
						$array['Version'] = 1;
					};
				} else {
					$array['Version'] = -1;
				};

				$result = json_encode($array);

				break;
			case "state":
				// if empty guid (new startetd game), generate a new uid for this session
				if (empty($guid)) {
					if (function_exists('com_create_guid') === true) {
						$guid = com_create_guid();
					} else {
						$guid = uniqid();
					};
				};

				// login or logout?
				switch ($gameData) {
					case "login":
							// Look if guid contains @ -> Use userName and password!
							if (strpos($guid, "@USERGAME@") !== false) {
								// Check if the game has an active session for this user
								$sql = "SELECT * FROM sessiondata WHERE gameName='".$gameName."' AND userName='".$userName."@".$password."' AND action='active'";
							} else {
								// Check if the game has an active session
								$sql = "SELECT * FROM sessiondata WHERE gameName='".$gameName."' AND action='active'";
							};
							
							$select = $db->query($sql);
							$rows = $select->fetchAll();
							
							if (count($rows)>0) {
								// Active session found - write session log for new passive game
								if (strpos($guid, "@USERGAME@") !== false) {
									$sql = "INSERT INTO sessions (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('passive','".$userName."@".$password."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";
								} else {
									$sql = "INSERT INTO sessions (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('passive','".$guid."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";
								};
								
								$rows = $db->exec($sql);
								
								if ($rows>0)  {
									// Write new entry for passive game on sessiondata
									if (strpos($guid, "@USERGAME@") !== false) {
										$sql = "INSERT INTO sessiondata (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('passive','".$userName."@".$password."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";
									} else {
										$sql = "INSERT INTO sessiondata (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('passive','".$guid."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";
									};
									$rows = $db->exec($sql);
								
									if ($rows>0)  {	
										$array['session'] = "Start passive session";
										$array['gameMode'] = 2; // passive
									} else {
										// Can't write game state
										$array['session'] = "Unable to write passive state";
										$array['gameMode'] = 0; // unkown									
									};
								} else {
									// Can't write session log
									$array['session'] = "Unable to write session data";
									$array['gameMode'] = 0; // unkown
								};
								
							} else {
								// NO active session found - write session log for new active game
								if (strpos($guid, "@USERGAME@") !== false) {
									$sql = "INSERT INTO sessions (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('active','".$userName."@".$password."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";
								} else {
									$sql = "INSERT INTO sessions (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('active','".$guid."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";
								};								
								$rows = $db->exec($sql);
								
								if ($rows>0)  {
									// Write new entry for passive game on sessiondata
									if (strpos($guid, "@USERGAME@") !== false) {
										$sql = "INSERT INTO sessiondata (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('active','".$userName."@".$password."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";								
									} else {
										$sql = "INSERT INTO sessiondata (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('active','".$guid."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";								
									};
									$rows = $db->exec($sql);									
									
									if ($rows>0)  {
										$array['session'] = "Start active session";
										$array['gameMode'] = 3; // active
									} else {
										// Can't write game state
										$array['session'] = "Unable to write passive active";
										$array['gameMode'] = 0; // unkown									
									};								
								
								} else {
									// Can't write session log
									$array['session'] = "Unable to write session data";
									$array['gameMode'] = 0; // unkown
								};									
							};

// INI COMPRUEBA EN DBMYSQL DE LOS CROMOS SI EXISTE EL USUARIO Y PASS


$sql ="SELECT CORREO,password FROM ALUMNOS WHERE CORREO=:CORREO and password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':CORREO', $userName, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() == 0)
{
	$array['gameMode'] = -2; // usuario no permitido
}

// FIN COMPRUEBA EN DBMYSQL DE LOS CROMOS SI EXISTE EL USUARIO Y PASS



						break;
					case "logout":
						// Write session log
						if (strpos($guid, "@USERGAME@") !== false) {
							$sql = "INSERT INTO sessions (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('".$gameData."','".$userName."@".$password."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";
						} else {
							$sql = "INSERT INTO sessions (action,guid,gameName,gameVersion,userName,timestamp) VALUES ('".$gameData."','".$guid."','".$gameName."',".$gameVersion.",'".$userName."',".$time.")";
						};
						$rows = $db->exec($sql);
					
						if ($rows>0) {
							// Delete entry from sessiondata
							if (strpos($guid, "@USERGAME@") !== false) {
								$sql = "DELETE FROM sessiondata WHERE guid='".$userName."@".$password."'";
							} else {
								$sql = "DELETE FROM sessiondata WHERE guid='".$guid."'";
							};
							$rows = $db->exec($sql);
							
							$array['session'] = "Session deleted";
							$array['gameMode'] = 1; // on start
						} else {
							// Can't write session log
							$array['session'] = "Unable to write session data";
							$array['gameMode'] = 0; // unkown
						};
							
						break;
					case "isactive":
						// Check if this game is the active session
						if (strpos($guid, "@USERGAME@") !== false) {
							$sql = "SELECT * FROM sessiondata WHERE gameName='".$gameName."' AND guid='".$userName."@".$password."' AND action='active'";
						} else {
							$sql = "SELECT * FROM sessiondata WHERE gameName='".$gameName."' AND action='active'";
						};
							
						$select = $db->query($sql);
						$rows = $select->fetchAll();

						if ($rows>0) {
							if (strpos($guid, "@USERGAME@") !== false) {
								$array['session'] = "Still active session";
								$array['gameMode'] = 3; // active							
							} else {
								if ($rows[0]["guid"]==$guid) {
									$array['session'] = "Still active session";
									$array['gameMode'] = 3; // active
								} else {
									$array['session'] = "Lost active session to ".$rows[0]["guid"];
									$array['gameMode'] = 2; // active
								};
							};
						} else {
							$array['session'] = "No active session found";
							$array['gameMode'] = 2; // active
						};
						
						break;
					default:
						$array['gameMode'] = 0; // unkown
						$array['session'] = "Unknown action";
							
				};		
				
				$array['guid'] = $guid;
				
				$result = json_encode($array);
				
				break;
			default:

				$result = "Unknown action: ".$action;

		};

		$db = NULL;

		echo $result;

	} catch(PDOException $e) {

		echo "Exception: ".$e->getMessage();

	}

?>

	