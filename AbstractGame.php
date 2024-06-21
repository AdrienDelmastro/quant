<?php

abstract class AbstractGame{
    protected int $gameID;
    protected array $players;
    public int $currentPlayer;
    public String $gameStatus;
}