<?php

require 'lnd.php';

$host = '';
$tlsPath = '';
$macaroonHex = '';

$lnd = new LND();
$lnd->setHost($host);
$lnd->setMacarronHex($macaroonHex);

$info = $lnd->getInfo();

var_dump($info->{'alias'});

?>
