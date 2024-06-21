<?php

class ActionQuantik{
    //attributs
    protected PlateauQuantik $plateau;
    //constructeurs
    public function __construct(PlateauQuantik $plateau){
        $this->plateau = $plateau;
    }

    public function getPlateau():PlateauQuantik {
        return $this->plateau;
    }

    public function isRowWin(int $numRow):bool {
        $arrPiece = $this->plateau->getRow($numRow);
        return self::isComboWin($arrPiece);
    }

    public function isColWin(int $numCol):bool {
        $arrPiece = $this->plateau->getCol($numCol);
        return self::isComboWin($arrPiece);
    }

    public function isCornerWin(int $dir): bool {
        $arrPiece = $this->plateau->getCorner($dir);
        return self::isComboWin($arrPiece);
    }

    private function isPositionWin(int $row, int $col): bool{
        $corner = PlateauQuantik::getCornerFromCoord($row, $col);
        if($this->isRowWin($row) || $this->isColWin($col) || $this->isCornerWin($corner)){
            return true;
        }
        return false;
    }

    public static function isGameWin(PlateauQuantik $plateau, int $row, int $col):bool{
        $actionQuantik = new ActionQuantik($plateau);
        return $actionQuantik->isPositionWin($row, $col);
    }
    public function isValidePose(int $rowNum, int $colNum, PieceQuantik $piece): bool{
        $colonne = $this->plateau->getCol($colNum);
        $ligne = $this->plateau->getRow($rowNum);
        $dir = $this->plateau->getCornerFromCoord($rowNum, $colNum);
        $corner = $this->plateau->getCorner($dir);
        return self::isPieceValide($colonne, $piece) && self::isPieceValide($ligne, $piece) && self::isPieceValide($corner, $piece) && $this->plateau->getPiece($rowNum, $colNum)->getForme() == 0;
    }

    public function posePiece(int $rowNum, int $colNum, PieceQuantik $piece): void{
        if(self::isValidePose($rowNum, $colNum, $piece)){
            $this->plateau->setPiece($rowNum, $colNum, $piece);
            echo "La pièce ". $piece . " a bien été posée en (" . $rowNum . ";" . $colNum .").";
        } else {
            echo "Il est impossible de poser la pièce en (" . $rowNum . ";" . $colNum .").";
            echo "Il existe déjà la pièce " . $this->plateau->getPiece($rowNum, $colNum) . " à cet emplacement.";
        }
    }

    public function __toString(): string{
        return $this->plateau->__toString();
    }

    private static function isComboWin(ArrayPieceQuantik $pieces): bool{
        $formeInitiale = $pieces->getPieceQuantik(0)->getForme();
        if($formeInitiale == 0){
            return false;
        }
        for($i = 1; $i < $pieces->count(); $i++){
            $formeCurrent = $pieces->getPieceQuantik($i)->getForme();
            if($formeInitiale === $formeCurrent || $formeCurrent == 0){
                return false;
            }
        }
        return true;
    }
    private static function isPieceValide(ArrayPieceQuantik $pieces, PieceQuantik $p): bool {
        $nbPiecesPosees = 0;
        for($i = 0; $i < PlateauQuantik::NBCOLS; $i++){
            if($pieces->getPieceQuantik($i)->getForme() !== PieceQuantik::VOID){
                $nbPiecesPosees++;
            }
            if($pieces->getPieceQuantik($i)->getForme() == $p->getForme() && $pieces->getPieceQuantik($i)->getCouleur() != $p->getCouleur()){
                return false;
            }
        }
        return $nbPiecesPosees < 4;
    }

}
