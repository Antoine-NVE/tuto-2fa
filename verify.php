<?php

use PragmaRX\Google2FA\Google2FA;

require 'vendor/autoload.php';

session_start();

// On vérifie si on a un POST et si la session existe
if (empty($_POST['code']) || empty($_SESSION['user']['google2FA_secret'])) {
    die(json_encode(['result' => false]));
}

// On initialise
$google2FA = new Google2FA();

// On récupère le code
$code = $_POST['code'];

$isValid = $google2FA->verifyKey($_SESSION['user']['google2FA_secret'], $code);

echo json_encode([
    'code' => $code,
    'result' => $isValid
]);
