<?php

class ArrayPieceQuantik implements ArrayAccess, Countable
{
    //attributs
    protected array $piecesQuantiks;

    //constructeurs
    public function __construct(){
        $this->piecesQuantiks = array();
    }


    //méthodes
    public function __toString():string{
        $str = "";
        foreach ($this->piecesQuantiks as $item) {
            $str.= $item->__toString().' ';
        }
        return $str;
    }

    public function getPieceQuantik(int $pos):PieceQuantik{
        return $this->piecesQuantiks[$pos];
    }

    public function setPieceQuantik(int $pos, PieceQuantik $pieceQuantik){
        $this->piecesQuantiks[$pos] = $pieceQuantik;
    }

    public function addPieceQuantik(PieceQuantik $piece):void{
        $this->piecesQuantiks[] = $piece;
    }

    public function removePieceQuantik(int $pos):void{
        array_splice($this->piecesQuantiks, $pos, 1);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset):bool{
        return $offset < $this->count();
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset):PieceQuantik{
        return $this->piecesQuantiks[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset,  $value):void{
        $this->piecesQuantiks[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset):void{
        unset($this->piecesQuantiks[$offset]);
    }

    public function count():int{
        return count($this->piecesQuantiks);
    }

    public static function initPiecesNoires():ArrayPieceQuantik{
        $noirs = new ArrayPieceQuantik();
        $noirs->addPieceQuantik(PieceQuantik::initBlackCone());
        $noirs->addPieceQuantik(PieceQuantik::initBlackCone());
        $noirs->addPieceQuantik(PieceQuantik::initBlackCube());
        $noirs->addPieceQuantik(PieceQuantik::initBlackCube());
        $noirs->addPieceQuantik(PieceQuantik::initBlackCylindre());
        $noirs->addPieceQuantik(PieceQuantik::initBlackCylindre());
        $noirs->addPieceQuantik(PieceQuantik::initBlackSphere());
        $noirs->addPieceQuantik(PieceQuantik::initBlackSphere());
        return $noirs;
    }

    public static function initPiecesBlanches():ArrayPieceQuantik{
        $blancs = new ArrayPieceQuantik();
        $blancs->addPieceQuantik(PieceQuantik::initWhiteCone());
        $blancs->addPieceQuantik(PieceQuantik::initWhiteCone());
        $blancs->addPieceQuantik(PieceQuantik::initWhiteCube());
        $blancs->addPieceQuantik(PieceQuantik::initWhiteCube());
        $blancs->addPieceQuantik(PieceQuantik::initWhiteCylindre());
        $blancs->addPieceQuantik(PieceQuantik::initWhiteCylindre());
        $blancs->addPieceQuantik(PieceQuantik::initWhiteSphere());
        $blancs->addPieceQuantik(PieceQuantik::initWhiteSphere());
        return $blancs;
    }

    public function getJson(): string
    {
        $json = "[";
        $jTab = [];
        foreach ($this->piecesQuantiks as $p)
            $jTab[] = $p->getJson();
        $json .= implode(',', $jTab);
        return $json . ']';
    }

    public static function initArrayPieceQuantik(string|array $json): ArrayPieceQuantik
    {
        echo '$json';
        print_r($json);
        $apq = new ArrayPieceQuantik();
        if (is_string($json)) {
            $json = json_decode($json);
        }
        foreach ($json as $j)
            $apq->addPieceQuantik(PieceQuantik::initPieceQuantik($j));
        return $apq;
    }
}