<?php
require_once 'AbstractUIGenerator.php';
require_once 'ActionQuantik.php';
require_once 'ArrayPieceQuantik.php';
require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'Player.php';
require_once 'QuantikGame.php';
require_once 'QuantikUIGenerator.php';
require_once 'PDOQuantik.php';

function getPageHome(): string
{
    require_once 'db.php';
    PDOQuantik::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
    $allGames = PDOQuantik::getAllGameQuantik();
    $allPlayerGames = PDOQuantik::getAllGameQuantikByPlayerName($_SESSION['player']->name);
    $page = '<!DOCTYPE html>
<html class="no-js" lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="Author" content="Adrien et le BG" />
    <script src="js/background.js"></script>
    <link rel="stylesheet" href="css/quantik.css"/>
    <title>Home</title>
  </head>
  <body>
  <canvas id="bg"></canvas>
  <div class="titreContainer"><h1>Quantik Game</h1> <h2>Salon Quantik de ' . $_SESSION['player']->name . '</h2></div>
     <div class="contentContainer"> 

   <div class="container"> <h2>Créer une partie</h2>
       <form action="traitementFormQuantik.php" method="post">
            <button class="largeButton" type="submit" name="action" value="creerPartie">+</button>
       </form>
   </div>
   <div class="container"> <h2>Continuer une partie</h2>';
    foreach ($allPlayerGames as $game) {
        if ($game['gameStatus'] === 'waitingForPlayer') {
            $data = json_decode($game['json']);
            $playeroneName = $data->couleursPlayers[$data->currentPlayer]->name;
            $page .= '<form class="formHome" action="traitementFormQuantik.php" method="post"> <span> Partie numéro '.$data->gameID.' en attente de: ' . $playeroneName . '</span>';
            $page .= '<input type="hidden" name="gameid" value="' . $game['gameId'] . '>"</input>';
            $page .= '<button class="smallButton" type="submit" name="action" value="continuerPartie"> > </button></form>';
        }
    }
    $page .= '</div>
   <div class="container"> <h2>Rejoindre une partie</h2>';

    foreach ($allGames as $game) {
        if ($game['gameStatus'] === 'constructed' && $game['playerOne'] !== $_SESSION['player']->id) {
            $data = json_decode($game['json']);
            $playeroneName = $data->couleursPlayers[0]->name;
            $page .= '<form class="formHome" action="traitementFormQuantik.php" method="post"> <span>Partie créée par: ' . $playeroneName . "</span>";
            $page .= '<input type="hidden" name="gameid" value="' . $game['gameId'] . '>"</input>';
            $page .= '<button class="smallButton" type="submit" name="action" value="rejoindrePartie"> > </button></form>';
        }
    }
    $page .= '</div> <div class="container"> <h2>Consulter une partie terminée</h2>';
    foreach ($allGames as $game) {
        if ($game['gameStatus'] === 'finished') {
            $data = json_decode($game['json']);
            $nomGagnant = $data->couleursPlayers[$data->currentPlayer]->name;
            $page .= '<form class="formHome" action="traitementFormQuantik.php" method="post"> <span>Partie gagnée par: ' . $nomGagnant . "</span> ";
            $page .= '<input type="hidden" name="gameid" value="' . $game['gameId'] . '>"</input>';
            $page .= '<button class="smallButton" type="submit" name="action" value="continuerPartie"> > </button></form>';
        }
    }
    $page .= '</div >     
    <div class="container"> <h2> Quitter</h2>
       <form action = "traitementFormQuantik.php" method = "post" >
            <button class="largeButton red" type = "submit" name = "action" value ="quitter">x</button>
       </form>
   </div>  </div>    
  </body>
</html> ';
    if (isset($_SESSION['partieCreer']) && $_SESSION['partieCreer']) {

        $page .= '<div class = "message messageDisparation" >
            <span>Une partie a été créée !</span>
            </div >';
        $_SESSION['partieCreer'] = false;
    }

    if (isset($_SESSION['max']) && $_SESSION['max']) {

        $page .= '<div class = "message messageDisparation" >
            <span>La partie est pleine!</span>
            </div >';
        $_SESSION['max'] = false;
    }

    return $page . '</body></html>';
}


session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

if (!isset($_SESSION['player'])) {
    $_SESSION['etat'] = "login";
}
switch ($_SESSION['etat']) {
    case 'home':
        echo getPageHome();
        break;

    case 'choixPiece':
        echo QuantikUIGenerator::getPageSelectionPiece($_SESSION["game"], $_SESSION["game"]->currentPlayer);
        break;

    case 'posePiece':
        echo QuantikUIGenerator::getPagePosePiece($_SESSION["game"], $_SESSION["game"]->currentPlayer, intval($_SESSION["piece"]));
        break;

    case 'consulterPartieVictoire':
        echo QuantikUIGenerator::getPageVictoire($_SESSION['game'], $_SESSION['game']->currentPlayer);
        break;

    case 'consulterPartieEnCours':
        $json = $_SESSION['game']->getJson();
        $data = json_decode($json);
        $playerName = $data->couleursPlayers[$_SESSION['game']->currentPlayer]->name;

        echo QuantikUIGenerator::getPageConsulter($_SESSION['game'], $playerName);
        break;

    case 'login':
        require_once 'login.php';
        break;

    default:
        $_SESSION['etat'] = 'home';
        echo QuantikUIGenerator::getPageErreur("Une erreur est survenue", "index.php");
        break;

}
