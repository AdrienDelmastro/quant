<?php

class PieceQuantik{
    public const BLACK = 1;
    public const WHITE = 0;
    public const VOID = 0;
    public const CUBE = 1;
    public const CONE = 2;
    public const CYLINDRE = 3;
    public const SPHERE = 4;
    protected int $forme;
    protected int $couleur;

    private function __construct(int $forme, int $couleur){
        $this->forme = $forme;
        $this->couleur = $couleur;
    }

    public function getForme(): int{
        return $this->forme;
    }
    public function getCouleur(): int{
        return $this->couleur;
    }

    public function __toString(): string{
        $chaine = "(";
        switch ($this->forme){
            case PieceQuantik::CUBE:
                $chaine .= "CU:";
                if($this->couleur == self::WHITE){
                    return $chaine."B)";
                } else{
                    return $chaine."N)";
                }
            case PieceQuantik::CONE:
                $chaine .= "CO:";
                if($this->couleur == self::WHITE){
                    return $chaine."B)";
                } else{
                    return $chaine."N)";
                }
            case PieceQuantik::CYLINDRE:
                $chaine .= "Cy:";
                if($this->couleur == self::WHITE){
                    return $chaine."B)";
                } else{
                    return $chaine."N)";
                }
            case PieceQuantik::SPHERE:
                $chaine .= "Sp:";
                if($this->couleur == self::WHITE){
                    return $chaine."B)";
                } else{
                    return $chaine."N)";
                }
            default:
                return $chaine . "  : )";
        }
    }

    public function getCssClass():string{
        $class = "";
        switch ($this->forme){
            case self::VOID:
                $class.="void";
                break;
            case self::CUBE:
                $class .="cube";
                break;
            case self::CONE:
                $class.= "cone";
                break;
            case self::CYLINDRE:
                $class.= "cylindre";
                break;
            case self::SPHERE:
                $class.="sphere";
                break;
        }
        if($this->couleur == self::WHITE && $class !== "void"){
            $class.="W";
        }
        else{
            $class.="B";
        }
        return $class;
    }

    public static function initVoid(): PieceQuantik{
        return new PieceQuantik(self::VOID,self::WHITE);
    }

    public static function initWhiteCube(): PieceQuantik{
        return new PieceQuantik(self::CUBE,self::WHITE);
    }

    public static function initBlackCube(): PieceQuantik{
        return new PieceQuantik(self::CUBE,self::BLACK);
    }

    public static function initWhiteCone(): PieceQuantik{
        return new PieceQuantik(self::CONE,self::WHITE);
    }

    public static function initBlackCone(): PieceQuantik{
        return new PieceQuantik(self::CONE,self::BLACK);
    }

    public static function initWhiteCylindre(): PieceQuantik{
        return new PieceQuantik(self::CYLINDRE,self::WHITE);
    }

    public static function initBlackCylindre(): PieceQuantik{
        return new PieceQuantik(self::CYLINDRE,self::BLACK);
    }
    public static function initWhiteSphere(): PieceQuantik{
        return new PieceQuantik(self::SPHERE,self::WHITE);
    }

    public static function initBlackSphere(): PieceQuantik{
        return new PieceQuantik(self::SPHERE,self::BLACK);
    }

    public function getJson(): string {
        return '{"forme":'. $this->forme . ',"couleur":'.$this->couleur. '}';
    }

    public static function initPieceQuantik(string|object $json): PieceQuantik {
        if (is_string($json)) {
            $props = json_decode($json, true);
            return new PieceQuantik($props['forme'], $props['couleur']);
        }
        else
            return new PieceQuantik($json->forme, $json->couleur);
    }
}