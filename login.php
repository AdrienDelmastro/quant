<?php
require_once 'PDOQuantik.php';
session_start();

function getPageLogin(): string {
    $form = '<!DOCTYPE html>
<html class="no-js" lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="Author" content="Dominique Fournier" />
    <link rel="stylesheet" href="css/quantik.css" />
    <script src="js/background.js"></script>
    <title>Accès à la salle de jeux</title></head>
<body>  <canvas id="bg"></canvas>
<div class="titreContainer"><h1>Accès au salon Quantik</h1></div><div class="contentContainer centered"><div class="container login"><h2 id="titreLogin">Identification du joueur</h2>
<form id="formLogin" action="' .$_SERVER['PHP_SELF'].'" method="post">

          <input id="inputLogin" type="text" placeholder="Nom" name="playerName" />
          <input class="largeButton" type="submit" name="action" value="connecter"></form>
 </div> </div> </body></html>';
    return $form;
}


if (isset($_REQUEST['playerName'])) {
    // connexion à la base de données
    require_once 'db.php';
    PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);
    $player = PDOQuantik::selectPlayerByName($_REQUEST['playerName']);
    if (is_null($player)) $player = PDOQuantik::createPlayer($_REQUEST['playerName']);
    $_SESSION['player'] = $player;
    $_SESSION['etat'] = 'home';
    header('HTTP/1.1 303 See Other');
    header('Location: index.php');
}
else {
    echo getPageLogin();
}
