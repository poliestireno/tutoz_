<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
    {   
header('location:index.php');
}

?>
<!DOCTYPE html>
<html>
        <head>
                <title>nbhca-technology</title>
                <style>
                        canvas {
                                position: absolute;
                                top: 0px;
                                left: 0px;
                                background: transparent;
                        }
                        #background {
                                z-index: -2;
                        }
                        #main {
                                z-index: -1;
                        }
                        #ship {
                                z-index: 0;
                        }
                        .score {
                                position: absolute;
                                top: 5px;
                                left: 480px;
                                color: #FF7F00;
                                font-family: Helvetica, sans-serif;
                                cursor: default;
                        }
                        .game-over {
                                position: absolute;
                                top: 100px;
                                left: 210px;
                                color: #FF7F00;
                                font-family: Helvetica, sans-serif;
                                font-size: 30px;
                                cursor: default;
                                display: none;
                        }
                        .game-over span {
                                font-size: 20px;
                                cursor: pointer;
                                position: relative;
                                left: 50px;
                        }
                        .game-over span:hover {
                                color: #FFD700;
                        }
                        .loading {
                                position: absolute;
                                top: 100px;
                                left: 210px;
                                color: #FF7F00;
                                font-family: Helvetica, sans-serif;
                                font-size: 30px;
                                cursor: default;
                        }
                </style>
        </head>
        <body>
            <form id="form1" method="post" action="../contadorapertura.php">

</form>
                <!-- The canvas for the panning background -->
                <canvas id="background" width="600" height="360">
                        Your browser does not support canvas. Please try again with a different browser.
                </canvas>
                <!-- The canvas for all enemy ships and bullets -->
                <canvas id="main" width="600" height="360">
                </canvas>
                <!-- The canvas the ship uses (can only move up
         one forth the screen. -->
                <canvas id="ship" width="600" height="360">
                </canvas>
                <div class="score">PUNTOS: <span id="score"></span></div>
    <div class="game-over" id="game-over">GAME OVER<p id="faltan"/><p><span onclick="game.restart()">Pulsa aquí para reintentar</span></p></div>
                <div class="loading" id="loading">Consigue 1000 puntos<p>y tendrás el sobre...</p><p>SUERTE!!!</p></div>
                <script src="space_shooter_part_five.js"></script>
        </body>
</html>