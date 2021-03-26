<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
    {   
header('location:index.php');
}
if (!isset($_SESSION['idCromo']))
{
    header('location:../index.php');
}
?>
<html>
    <head>
        <title>
            PACMAN - 15237
        </title>
    </head>
    
    <body>
<form id="form1" method="post" action="../contadorapertura.php">
</form>        
    <canvas id="myCanvas" width="510" height="510">
        </canvas>
        <script type="text/javascript" src="Pacman.js"></script>
        <script type="text/javascript" src="Ghost.js"></script>
        <script type="text/javascript" src="Grid.js"></script>
        <script type="text/javascript" src="Game.js"></script>
    </body>

</html>
