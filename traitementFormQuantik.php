<?php
require_once 'ActionQuantik.php';
require_once 'ArrayPieceQuantik.php';
require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'Player.php';
require_once 'QuantikGame.php';
require_once 'PDOQuantik.php';

//ini_set('display_errors', 'On');
//error_reporting(E_ALL);

session_start();
$action = $_POST['action'];
switch ($action) {
    case "creerPartie":
        require_once 'db.php';
        PDOQuantik::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
        $partie = new QuantikGame(array($_SESSION['player']));
        $json = $partie->getJson();
        PDOQuantik::createGameQuantik($_SESSION['player']->id, $json);
        $_SESSION['etat'] = 'home';
        $_SESSION['partieCreer'] = true;
        header('Location: index.php');
        break;
    case "continuerPartie":
        require_once 'db.php';
        $gameId = intval($_POST['gameid']);
        PDOQuantik::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
        $game = PDOQuantik::getGameQuantikById($gameId);
        $data = json_decode($game->getJson());
        $_SESSION['game'] = $game;
        if ($game->gameStatus !== 'finished') {
            $currentPlayer = $data->couleursPlayers[$data->currentPlayer]->name;
            if ($_SESSION['player']->name == $currentPlayer) {
                $_SESSION['etat'] = 'choixPiece';
            } else {
                $_SESSION['etat'] = 'consulterPartieEnCours';
            }
        } else {
            $_SESSION['etat'] = 'consulterPartieVictoire';
        }
        header('Location: index.php');
        break;
    case "rejoindrePartie":
        $gameId = intval($_POST['gameid']);
        require_once 'db.php';
        PDOQuantik::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
        $partie = PDOQuantik::getGameQuantikById($gameId);
        if(count($partie->couleurPlayer)===1) {
            $partie->couleurPlayer[] = $_SESSION['player'];
            $json = $partie->getJson();
            PDOQuantik::addPlayerToGameQuantik($_SESSION['player']->id, $json, $gameId);
            $partie->setStatus('waitingForPlayer');
            $partie->gameID = $gameId;
            $json = $partie->getJson();
            PDOQuantik::saveGameQuantik('waitingForPlayer', $json, $gameId);
        }
        else{
            $_SESSION['max'] = true;
        }
        header('Location: index.php');
        break;
    case "consulterPartie":

        header('Location: index.php');
        break;
    case "quitter":
        session_destroy();
        header('Location: index.php');
        break;

    case "choisirPiece":
        $_SESSION['piece'] = $_POST['piece'];
        $_SESSION['etat'] = 'posePiece';
        header('Location: index.php');
        break;
    case 'poserPiece':
        $i = $_POST['caseI'];
        $j = $_POST['caseJ'];
        $currentPlayer = $_SESSION['game']->currentPlayer;
        if ($currentPlayer == PieceQuantik::WHITE) {
            $piece = $_SESSION["game"]->piecesBlanches->getPieceQuantik($_SESSION["piece"]);
            $deck = $_SESSION["game"]->piecesBlanches;
        } else {
            $piece = $_SESSION["game"]->piecesNoires->getPieceQuantik($_SESSION["piece"]);
            $deck = $_SESSION["game"]->piecesNoires;
        }
        $_SESSION["game"]->plateau->setPiece($i, $j, $piece);
        $deck->removePieceQuantik($_SESSION["piece"]);

        if (ActionQuantik::isGameWin($_SESSION["game"]->plateau, $i, $j)) {
            $_SESSION["game"]->gameStatus = "finished";
            $_SESSION['etat'] = 'consulterPartieVictoire';
        } else {
            $_SESSION["game"]->currentPlayer = ($_SESSION["game"]->currentPlayer + 1) % 2;
            $_SESSION['etat'] = 'consulterPartieEnCours';
        }
        $json = $_SESSION["game"]->getJson();
        require_once 'db.php';
        PDOQuantik::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
        PDOQuantik::saveGameQuantik($_SESSION["game"]->gameStatus, $json, $_SESSION["game"]->gameID);
        header('Location: index.php');
        break;

    case "retournerHome":
        unset($_SESSION['game']);
        $_SESSION['etat'] = 'home';
        header('Location: index.php');
        break;
    case "annulerChoix":
        $_SESSION['etat'] = 'choixPiece';
        header('Location: index.php');
        break;
    default:
        $_SESSION['etat'] = 'erreur';
        header('Location: index.php');
        break;
}