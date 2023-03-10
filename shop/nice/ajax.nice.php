<?php
include_once('../../common.php');
include_once('../settle_nice.inc.php');

$price = (int)$_POST["Amt"];

$ediDate = date("YmdHis");
$hashString = bin2hex(hash('sha256', $ediDate.$default['de_nice_mid'].$price.$default['de_nice_key'], true));

$data = array("EdiDate" => $ediDate, "EncryptData" => $hashString);
echo json_encode($data);