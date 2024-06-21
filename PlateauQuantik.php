<?php

class PlateauQuantik {
    public const NBROWS = 4;
    public const NBCOLS = 4;

    public const NW = 0;
    public const NE = 1;
    public const SW = 2;
    public const SE = 3;
    protected array $cases;

    public function __construct(){
        $this->cases = array(
            new ArrayPieceQuantik(),
            new ArrayPieceQuantik(),
            new ArrayPieceQuantik(),
            new ArrayPieceQuantik());

        $pieceVide = PieceQuantik::initVoid();
        for($i = 0; $i < self::NBROWS; $i++){
            for($j = 0; $j < self::NBCOLS; $j++){
                $this->cases[$i]->addPieceQuantik($pieceVide);}
        }
    }
    public function getPiece(int $rowNum, int $colNum): PieceQuantik{
        return $this->cases[$rowNum]->getPieceQuantik($colNum);
    }
    public function setPiece(int $rowNum, int $colNum, PieceQuantik $p): void{
        $this->cases[$rowNum]->setPieceQuantik($colNum, $p);
    }

    public function getRow(int $numRow): ArrayPieceQuantik{
       return $this->cases[$numRow];
    }

    public function getCol(int $numCol): ArrayPieceQuantik{
        $tab = new ArrayPieceQuantik();
        for($i = 0; $i < 4; $i++){
            $tab->addPieceQuantik($this->cases[$i]->getPieceQuantik($numCol));
        }
        return $tab;
    }

    public function getCorner(int $dir): ArrayPieceQuantik{
        $tab = new ArrayPieceQuantik();
        switch($dir){
            case PlateauQuantik::NW:
                $tab->addPieceQuantik($this->cases[0]->getPieceQuantik(0));
                $tab->addPieceQuantik($this->cases[0]->getPieceQuantik(1));
                $tab->addPieceQuantik($this->cases[1]->getPieceQuantik(0));
                $tab->addPieceQuantik($this->cases[1]->getPieceQuantik(1));
                return $tab;

            case PlateauQuantik::NE:
                $tab->addPieceQuantik($this->cases[0]->getPieceQuantik(2));
                $tab->addPieceQuantik($this->cases[0]->getPieceQuantik(3));
                $tab->addPieceQuantik($this->cases[1]->getPieceQuantik(2));
                $tab->addPieceQuantik($this->cases[1]->getPieceQuantik(3));
                return $tab;

            case PlateauQuantik::SW:
                $tab->addPieceQuantik($this->cases[2]->getPieceQuantik(0));
                $tab->addPieceQuantik($this->cases[2]->getPieceQuantik(1));
                $tab->addPieceQuantik($this->cases[3]->getPieceQuantik(0));
                $tab->addPieceQuantik($this->cases[3]->getPieceQuantik(1));
                return $tab;

            case PlateauQuantik::SE:
                $tab->addPieceQuantik($this->cases[2]->getPieceQuantik(2));
                $tab->addPieceQuantik($this->cases[2]->getPieceQuantik(3));
                $tab->addPieceQuantik($this->cases[3]->getPieceQuantik(2));
                $tab->addPieceQuantik($this->cases[3]->getPieceQuantik(3));
                return $tab;

            default:
                return $tab;
        }
    }
    public function __toString(): string{
        $chaine = "";
        for($i = 0; $i < 4; $i++){
            for($j = 0; $j < 4; $j++){
                $chaine .= "[" . $this->getPiece($i, $j) ."]";
            }
            $chaine .= "\n";
        }
        return $chaine;
    }

    public static function getCornerFromCoord(int $rowNum, int $colNum): int{
        if($rowNum <= 1){
           if($colNum <= 1){
              return PlateauQuantik::NW;
           } else{
               return PlateauQuantik::NE;
           }
        } else{
            if($colNum <= 1){
                return PlateauQuantik::SW;
            } else{
                return PlateauQuantik::SE;
            }
        }
    }

    public function getJson(): string {
        $json = "[";
        $jTab = [];
        foreach ($this->cases as $apq)
            $jTab[] = $apq->getJson();
        $json .= implode(',',$jTab);
        return $json.']';
    }

    public static function initPlateauQuantik(string|array $json) : PlateauQuantik
    {
        $pq = new PlateauQuantik();
        if (is_string($json))
            $json = json_decode($json);
        $cases = [];
        foreach($json as $elem)
            $cases[] = ArrayPieceQuantik::initArrayPieceQuantik($elem);
        $pq->cases = $cases;
        return $pq;
    }
}