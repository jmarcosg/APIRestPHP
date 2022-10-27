<?php
$noUrl = true;
include '../../../app/config/global.php';

use App\Controllers\QRIdentificacion\QRI_CodigoQRController;
use App\Controllers\QRIdentificacion\QRI_PersonaController;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/qrstyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <title>Document</title>
</head>

<body>
    <header class="mb-5 mt-2">
        <div class="px-4">
            <nav class="navbar px-20 rounded-bottom navbar">
                <div class="container-fluid justify-content-center">
                    <img src="../../assets/banner.svg" alt="banner" class="img-fluid w-25" alt="" style="width: 100%">
                </div>
            </nav>
        </div>
    </header>
    <div class="container bg-white rounded p-2 mt-5">
        <?php
        if (!isset($_GET['token'])) {
        ?>
            <h1 class="text-center">QR INVALIDO</h1>
    </div>
</body>

</html>
<?php
            die();
        }

        $token = $_GET['token'];

        $infoQR = QRI_CodigoQRController::index(['qr_token' => $token]);

        if (count($infoQR) == 0) {
?>
    <h1 class="display-1 text-center">DATOS DE CONTACTO NO ENCONTRADOS</h1>
    </div>
    </body>

    </html>
<?php
            die();
        }
        $infoQR = $infoQR[0];
        $persona = QRI_PersonaController::index(['id' => $infoQR['id_persona_identificada']])[0];
?>

<h1 class="display-3 text-center"><?= utf8ize($persona['nombre']) ?> <?= utf8ize($persona['apellido']) ?></h1>

<h4 class="my-3">Tel&eacute;fono: <?= $persona['telefono'] ?></h4>

<?php
if ($persona['telefono_alternativo'] != "") {
?>
    <h4 class="my-3">Tel&eacute;fono alternativo: <?= $persona['telefono_alternativo'] ?></h4>
<?php
}
?>

<h4 class="my-3">Email: <?= $persona['email'] ?></h4>

<h4 class="my-3">Cargo: <?= utf8ize($persona['cargo']) ?></h4>

<?php
if ($persona['lugar_trabajo'] != "") {
?>
    <h4 class="my-3">Lugar de trabajo: <?= $persona['lugar_trabajo'] ?></h4>
<?php
}
if (ENV == "local") {
?>
    <a href="../../../files/qr-identificacion/<?= $infoQR['id'] ?>/tarjeta-de-contacto.vcf" download class="btn btn-primary offset-10 col-2">Agregar contacto</a>

<?php
} else {
?>
    <a href="<?= base64_encode(FILE_PATH . "qr-identificacion/$infoQR[id]/$infoQR[qr_path]") ?>" download class="btn btn-primary offset-10 col-2">Agregar contacto</a>
<?php
}
?>
</div>
</body>

</html>