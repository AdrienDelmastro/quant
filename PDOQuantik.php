<?php

require_once 'Player.php';
require_once 'QuantikGame.php';

class PDOQuantik
{
    private static PDO $pdo;

    public static function initPDO(string $sgbd, string $host, string $db, string $user, string $password, string $nomTable = ''): void
    {
        switch ($sgbd) {
            case 'pgsql':
                self::$pdo = new PDO('pgsql:host=' . $host . ' dbname=' . $db . ' user=' . $user . ' password=' . $password);
                break;
            case 'mysql':
                self::$pdo = new PDO('mysql:host=' . $host . ' dbname=' . $db . ' user=' . $user . ' password=' . $password);
                break;
            default:
                exit ("Type de sgbd non correct : $sgbd fourni, 'mysql' ou 'pgsql' attendu");
        }

        // pour récupérer aussi les exceptions provenant de PDOStatement
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /* requêtes Préparées pour l'entitePlayer */
    private static PDOStatement $createPlayer;
    private static PDOStatement $selectPlayerByName;

    /******** Gestion des requêtes relatives à Player *************/
    public static function createPlayer(string $name): Player
    {
        if (!isset(self::$createPlayer))
            self::$createPlayer = self::$pdo->prepare('INSERT INTO Player(name) VALUES (:name)');
        self::$createPlayer->bindValue(':name', $name, PDO::PARAM_STR);
        self::$createPlayer->execute();
        return self::selectPlayerByName($name);
    }

    public static function selectPlayerByName(string $name): ?Player
    {
        if (!isset(self::$selectPlayerByName))
            self::$selectPlayerByName = self::$pdo->prepare('SELECT * FROM Player WHERE name=:name');
        self::$selectPlayerByName->bindValue(':name', $name, PDO::PARAM_STR);
        self::$selectPlayerByName->execute();
        $player = self::$selectPlayerByName->fetchObject('Player');
        return ($player) ? $player : null;
    }

    /* requêtes préparées pour l'entiteGameQuantik */
    private static PDOStatement $createGameQuantik;
    private static PDOStatement $saveGameQuantik;
    private static PDOStatement $addPlayerToGameQuantik;
    private static PDOStatement $selectGameQuantikById;
    private static PDOStatement $selectAllGameQuantik;
    private static PDOStatement $selectAllGameQuantikByPlayerName;

    /******** Gestion des requêtes relatives à QuantikGame *************/

    /**
     * initialisation et execution de $createGameQuantik la requête préparée pour enregistrer une nouvelle partie
     */
    public static function createGameQuantik(string $playerName, string $json): void
    {
        if (!isset(self::$createGameQuantik))
            self::$createGameQuantik = self::$pdo->prepare('INSERT INTO QuantikGame(playerOne, json) VALUES (:playerName, :json)');
        self::$createGameQuantik->bindValue(':playerName', $playerName, PDO::PARAM_STR);
        self::$createGameQuantik->bindValue(':json', $json, PDO::PARAM_STR);
        self::$createGameQuantik->execute();
    }

    /**
     * initialisation et execution de $saveGameQuantik la requête préparée pour changer
     * l'état de la partie et sa représentation json
     */
    public static function saveGameQuantik(string $gameStatus, string $json, int $gameId): void
    {

        if (!isset(self::$saveGameQuantik))
            self::$saveGameQuantik = self::$pdo->prepare('UPDATE QuantikGame SET json = :json, gameStatus = :gameStatus WHERE gameId = :gameId');
        self::$saveGameQuantik->bindValue(':gameStatus', $gameStatus, PDO::PARAM_STR);
        self::$saveGameQuantik->bindValue(':json', $json, PDO::PARAM_STR);
        self::$saveGameQuantik->bindValue(':gameId', $gameId, PDO::PARAM_STR);
        self::$saveGameQuantik->execute();
    }

    /**
     * initialisation et execution de $addPlayerToGameQuantik la requête préparée pour intégrer le second joueur
     */
    public static function addPlayerToGameQuantik(string $playerId, string $json, int $gameId): void
    {
        if (!isset(self::$saveGameQuantik))
            self::$addPlayerToGameQuantik = self::$pdo->prepare('UPDATE QuantikGame SET json = :json, playerTwo = :playerid WHERE gameId = :gameid');
        self::$addPlayerToGameQuantik->bindValue(':playerid', $playerId, PDO::PARAM_STR);
        self::$addPlayerToGameQuantik->bindValue(':json', $json, PDO::PARAM_STR);
        self::$addPlayerToGameQuantik->bindValue(':gameid', $gameId, PDO::PARAM_STR);
        self::$addPlayerToGameQuantik->execute();
    }

    /**
     * initialisation et execution de $selectAllGameQuantikById la requête préparée pour récupérer
     * une instance de quantikGame en fonction de son identifiant
     */
    public static function getGameQuantikById(int $gameId): ?QuantikGame
    {
        if (!isset(self::$selectGameQuantikById))
            self::$selectGameQuantikById = self::$pdo->prepare('SELECT * FROM QuantikGame WHERE gameId=:id');
        self::$selectGameQuantikById->bindValue(':id', $gameId, PDO::PARAM_STR);
        self::$selectGameQuantikById->execute();
        $game = self::$selectGameQuantikById->fetch(PDO::FETCH_ASSOC);
        return (QuantikGame::initQuantikGame($game['json']));
    }

    /**
     * initialisation et execution de $selectAllGameQuantik la requête préparée pour récupérer toutes
     * les instances de quantikGame
     */
    public static function getAllGameQuantik(): array
    {
        if (!isset(self::$selectAllGameQuantik))
            self::$selectAllGameQuantik = self::$pdo->prepare('SELECT * FROM QuantikGame');
        self::$selectAllGameQuantik->execute();
        $game = self::$selectAllGameQuantik->fetchAll(PDO::FETCH_ASSOC);
        return ($game);
    }

    /**
     * initialisation et execution de $selectAllGameQuantikByPlayerName la requête préparée pour récupérer les instances
     * de quantikGame accessibles au joueur $playerName
     * ne pas oublier les parties "à un seul joueur"
     */
    public static function getAllGameQuantikByPlayerName(string $playerName): array
    {
        $player = PDOQuantik::selectPlayerByName($playerName);
        $id = $player->getId();
        if (!isset(self::$selectAllGameQuantikByPlayerName))
            self::$selectAllGameQuantikByPlayerName = self::$pdo->prepare('SELECT * FROM QuantikGame WHERE playerOne = :playername OR playerTwo = :playername ');
        self::$selectAllGameQuantikByPlayerName->bindValue(':playername', $id, PDO::PARAM_STR);
        self::$selectAllGameQuantikByPlayerName->execute();
        $game = self::$selectAllGameQuantikByPlayerName->fetchAll(PDO::FETCH_ASSOC);
        return ($game);
    }

    /**
     * initialisation et execution de la requête préparée pour récupérer
     * l'identifiant de la dernière partie ouverte par $playername
     */
}

