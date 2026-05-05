<?php
$kasutaja = "d133850_andreileb";
$parool = "musthaus862686";
$andmebaas = "d133850_newssite";
$servername = "d133850.mysql.zonevs.eu";

$conn = new mysqli($servername, $kasutaja, $parool, $andmebaas);
$conn->set_charset("utf8");