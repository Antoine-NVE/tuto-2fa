<?php

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

require 'vendor/autoload.php';

session_start();

// On initialise Google2FA
$google2FA = new Google2FA();

// On génère une clé secrète
$secretKey = $google2FA->generateSecretKey();

// On stocke dans une variable $user
$user = [
    'google2FA_secret' => $secretKey,
    'email' => 'antoine@gmail.com'
];

$_SESSION['user'] = $user;

// On nomme notre app
$appName = 'Nouvelle-Techno.fr';

$qrCodeUrl = $google2FA->getQRCodeUrl($appName, $user['email'], $secretKey);

// On prépare le QRCode
$imageSize = 250;
$writer = new Writer(
    new GDLibRenderer($imageSize)
);

// On encode l'url
$encodedQrCodeData = base64_encode($writer->writeString($qrCodeUrl));

// On récupère le code actuel de l'application
$currentOtp = $google2FA->getCurrentOtp($user['google2FA_secret']);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Authenticator</title>
</head>

<body>
    <h1>Google Authenticator</h1>

    <div id="qrcode">
        <p>Code actuel : <?= $currentOtp ?></p>
        <img src="data:image/png;base64,<?= $encodedQrCodeData ?>" alt="QR code">
    </div>

    <h2>Vérification de code</h2>
    <form id="verify">
        <input type="text" id="code" placeholder="Entrer le code">
        <button type="submit">Vérifier</button>
    </form>

    <script>
        // On récupère le submit du formulaire
        document.querySelector('#verify').addEventListener('submit', function(event) {
            // On empêche le submit
            event.preventDefault();

            // On récupère le code
            let code = document.querySelector('#code').value;

            if (!code) {
                alert('Merci d\'entrer un code');
                return false;
            }

            // On génère les données de formulaire
            let formData = new FormData();
            formData.append('code', code);
            fetch('verify.php', {
                    method: 'post',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    
                    if (!data.result) {
                        alert('Code incorrect : ' + data.code);
                        return false;
                    }
                    alert('Code correct : ' + data.code);
                })
        })
    </script>
</body>

</html>