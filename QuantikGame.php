<?php
require_once 'Player.php';
require_once 'AbstractGame.php';

class QuantikGame extends AbstractGame {
    public PlateauQuantik $plateau;
    public ArrayPieceQuantik $piecesBlanches;
    public ArrayPieceQuantik $piecesNoires;
    public array $couleurPlayer;
    public int $gameID;
    public string $gameStatus;

    public function __construct(array $players){
        $this->currentPlayer = 0;
        $this->piecesBlanches = ArrayPieceQuantik::initPiecesBlanches();
        $this->piecesNoires = ArrayPieceQuantik::initPiecesNoires();
        $this->plateau = new PlateauQuantik();
        $this->gameStatus = "constructed";
        $this->gameID = 0;
        foreach ($players as $player) {
            $this->couleurPlayer[] = $player;
        }
    }

    public function setStatus($newStatus){
        $this->gameStatus = $newStatus;
    }
    public function __toString(): string
    {
        return 'Partie n°' . $this->gameID . ' lancée par joueur ' . $this->couleurPlayer[0].'Statut: '.$this->gameStatus;
    }
    public function getJson(): string
    {
        $json = '{';
        $json .= '"plateau":' . $this->plateau->getJson();
        $json .= ',"piecesBlanches":' . $this->piecesBlanches->getJson();
        $json .= ',"piecesNoires":' . $this->piecesNoires->getJson();
        $json .= ',"currentPlayer":' . $this->currentPlayer;
        $json .= ',"gameID":' . $this->gameID;
        $json .= ',"gameStatus":' . json_encode($this->gameStatus);
        if (is_null($this->couleurPlayer[1]))
            $json .= ',"couleursPlayers":[' . $this->couleurPlayer[0]->getJson() . ']';
        else
            $json .= ',"couleursPlayers":[' . $this->couleurPlayer[0]->getJson() . ',' . $this->couleurPlayer[1]->getJson() . ']';
        return $json . '}';
    }
    public static function initQuantikGame(string $json): QuantikGame
    {
        $object = json_decode($json);
        $players = [];
        foreach ($object->couleursPlayers as $stdObj) {
            $p = new Player();
            $p->setName($stdObj->name);
            $p->setId($stdObj->id);
            $players[] = $p;
        }
        $qg = new QuantikGame($players);
        $qg->plateau = PlateauQuantik::initPlateauQuantik($object->plateau);
        $qg->piecesBlanches = ArrayPieceQuantik::initArrayPieceQuantik($object->piecesBlanches);
        $qg->piecesNoires = ArrayPieceQuantik::initArrayPieceQuantik($object->piecesNoires);
        $qg->currentPlayer = $object->currentPlayer;
        $qg->gameID = $object->gameID;
        $qg->gameStatus = $object->gameStatus;
        return $qg;
    }
}