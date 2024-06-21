<?php

class QuantikUIGenerator extends AbstractUIGenerator
{
    protected static function getButtonClass(PieceQuantik $piece): string
    {
        return "<button type='submit' name='active' disabled > $piece </button>";
    }

    protected static function getDivPlateauQuantik(PlateauQuantik $plateau): string
    {
        $html = "<div class='plateau'>";
        $html .= "<table>";
        for ($i = 0; $i < PlateauQuantik::NBROWS; $i++) {
            $html .= "<tr>";
            for ($j = 0; $j < PlateauQuantik::NBCOLS; $j++) {
                $piece = $plateau->getPiece($i, $j);
                $cssClass= $piece->getCssClass();
                $html .= "<td>" ."<button class = 'boutonPiece $cssClass' type='submit' name='' disabled >$piece</button>". "</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</table></div>";
        return $html;
    }

    public static function getDivPiecesDisponibles(ArrayPieceQuantik $apq): string
    {
        $html = "<div class='pieceDispo'>";
        for ($i = 0; $i < $apq->count(); $i++) {
            $piece = $apq->getPieceQuantik($i);
            $cssClass= $piece->getCssClass();
            $html .= "<button class = 'boutonPiece $cssClass' type='submit' name='' disabled >$piece</button>";
        }
        return $html .= '</div>';
    }


    protected static function getFormSelectionPiece(ArrayPieceQuantik $apq): string
    {
        $html = "<div class='ligne'>";
        for ($i = 0; $i < $apq->count(); $i++) {
            $html .= "<form action='traitementFormQuantik.php' method='post'>";
            $piece = $apq->getPieceQuantik($i);
            $cssClass= $piece->getCssClass();
            $html .= '<input type="hidden" name="piece" value="' . $i . '"/>';
            $html .= "<button type='submit' class= 'boutonPiece $cssClass' name='action' value='choisirPiece'>$piece</button>";
            $html .= "</form>";
        }
        return $html . '</div>';
    }

    protected static function getFormPlateauQuantik(PlateauQuantik $plateau, PieceQuantik $piece): string
    {
        $html = "<div class='plateau'><table>";
        $action = new ActionQuantik($plateau);
        for ($i = 0; $i < PlateauQuantik::NBROWS; $i++) {
            $html.='<tr>';
            for ($j = 0; $j < PlateauQuantik::NBCOLS; $j++) {
                $case = $plateau->getPiece($i, $j);
                $cssClass = $case->getCssClass();
                if ($action->isValidePose($i, $j, $piece) && $case->getForme() === PieceQuantik::VOID) {
                    $html .= "<td><form action='traitementFormQuantik.php' method='post'>";
                    $html .= '<input type="hidden" name="caseI" value="' . $i . '"/>';
                    $html .= '<input type="hidden" name="caseJ" value="' . $j . '"/>';
                    $html .= "<button type='submit' class='boutonPiece $cssClass' name='action' value='poserPiece'>$case</button>";
                    $html .= "</form></td>";
                } else {
                    $html .= "<td><button type='submit' class='boutonPiece $cssClass disabled' name='boutonForm' value='$i:$j' disabled>$case</button></td>";
                }
            }
            $html .= '</tr>';
        }
        $html .= "</table></div>";
        return $html;
    }

    protected static function getFormButtonAnnulerChoixPiece(): string
    {
        $html = "<form action='traitementFormQuantik.php' method='post'>";
        $html .= "<button type='submit' class='largeButton red' name='action' value='annulerChoix'>Annuler</button></form>";
        return $html;
    }

    protected static function getDivMessageVictoire(int $couleur): string
    {
        $color = "";
        if ($couleur === PieceQuantik::WHITE) {
            $color .= "Jaunes";
        } else {
            $color .= "Oranges";
        }
        return "<div class='titreContainer'> 
                    <h2>  Victoire des $color </h2>
                </div>";
    }

    protected static function getDivTitre(string $titre): string
    {

        return "<div class='titreContainer'> 
                    <h2>$titre</h2>
                </div>";
    }

    protected static function getDivMessageTour(int $couleur): string
    {
        $color = "";
        if ($couleur === PieceQuantik::WHITE) {
            $color .= "Jaunes";
        } else {
            $color .= "Oranges";
        }
        return "<div class='titreContainer'> 
                    <h2>C'est le tour des $color</h2>
                </div>";
    }

    protected static function getDivMessageAttenteJoueur(string $playername): string
    {

        return "<div> 
                    <span>En attente de $playername</span>
                </div>";
    }

    protected static function getLienRecommencer(): string
    {
        return "<a id='retry' href='traitementFormQuantik.php'>Recommencer</a>";
    }

    protected static function getLienHome(): string
    {
        return '<form action="traitementFormQuantik.php" method="post"><button class= "largeButton" type="submit" name="action" value="retournerHome">Continuer</button></form>';
    }

    public static function getPageSelectionPiece(QuantikGame $quantik, int $couleurActive): string
    {
        $html = "";
        $pieces = array();
        $html.= self::getDivMessageTour($couleurActive);
        if ($couleurActive == PieceQuantik::WHITE) {
            $html .= AbstractUIGenerator::getDebutHTML("Choix pièces blanches");
            $html .= QuantikUIGenerator::getDivPiecesDisponibles($quantik->piecesNoires);
            $pieces = $quantik->piecesBlanches;
        } else {
            $html .= AbstractUIGenerator::getDebutHTML("Choix pièces noirs");
            $html .= QuantikUIGenerator::getDivPiecesDisponibles($quantik->piecesBlanches);
            $pieces = $quantik->piecesNoires;
        }
        $html .= QuantikUIGenerator::getDivPlateauQuantik($quantik->plateau);
        $html .= QuantikUIGenerator::getFormSelectionPiece($pieces);
        return $html . AbstractUIGenerator::getFinHTML();
    }

    public static function getPagePosePiece(QuantikGame $quantik, int $couleurActive, int $posSelection): string
    {
        $html = "";
        $pieces = array();
        $piecesAdversaire = array();
        $html.= self::getDivMessageTour($couleurActive);
        if ($couleurActive == PieceQuantik::WHITE) {
            $html .= AbstractUIGenerator::getDebutHTML("Pose piece blanche");
            $pieces = $quantik->piecesBlanches;
            $piecesAdversaire = $quantik->piecesNoires;
        } else {
            $html .= AbstractUIGenerator::getDebutHTML("Pose piece noir");
            $pieces = $quantik->piecesNoires;
            $piecesAdversaire = $quantik->piecesBlanches;
        }
        $html .= QuantikUIGenerator::getDivPiecesDisponibles($pieces);
        $html .= QuantikUIGenerator::getFormPlateauQuantik($quantik->plateau, $pieces->getPieceQuantik($posSelection));
        $html .= QuantikUIGenerator::getDivPiecesDisponibles($piecesAdversaire);
        $html .= QuantikUIGenerator::getFormButtonAnnulerChoixPiece();
        return $html . AbstractUIGenerator::getFinHTML();
    }

    public static function getPageVictoire(QuantikGame $quantik, int $couleurActive): string
    {
        $html = AbstractUIGenerator::getDebutHTML('Victoire');

        $html .= QuantikUIGenerator::getDivMessageVictoire($couleurActive);
        $html .= QuantikUIGenerator::getDivPiecesDisponibles($quantik->piecesBlanches);
        $html .= QuantikUIGenerator::getDivPlateauQuantik($quantik->plateau);
        $html .= QuantikUIGenerator::getDivPiecesDisponibles($quantik->piecesNoires);
        $html .= QuantikUIGenerator::getLienHome();
        return $html .= AbstractUIGenerator::getFinHTML();
    }

    public static function getPageConsulter(QuantikGame $quantik, string $currentPlayerName): string
    {
        $html = AbstractUIGenerator::getDebutHTML('Consulter');
        $html.= QuantikUIGenerator::getDivTitre("Etat de la partie");
        $html .= QuantikUIGenerator::getDivPiecesDisponibles($quantik->piecesBlanches);
        $html .= QuantikUIGenerator::getDivPlateauQuantik($quantik->plateau);
        $html .= QuantikUIGenerator::getDivPiecesDisponibles($quantik->piecesNoires);
        $html .= QuantikUIGenerator::getDivMessageAttenteJoueur($currentPlayerName);
        $html .= QuantikUIGenerator::getLienHome();
        return $html .= AbstractUIGenerator::getFinHTML();
    }
}