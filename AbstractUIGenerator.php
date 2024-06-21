<?php

abstract class AbstractUIGenerator{

    protected static function getDebutHTML(string $title = "title content"):string
    {
        $html = "";
        return "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0' /><script src='js/background.js'></script> <title>$title</title><link rel='stylesheet' href='css/quantik.css'></head><body>  <canvas id='bg'></canvas>";
    }

    protected static function getFinHTML():string{
        return "</body></html>";
    }

    public static function getPageErreur(string $message, string $urlLien):string
    {
        return "<h2>$message</h2> </br><a href='$urlLien'>retourHome</a>";
    }
}