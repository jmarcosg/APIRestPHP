<?php

include '../../../app/config/global.php';

use App\Controllers\Arbolado\Arb_PodadorController;
use App\Controllers\RenaperController;

if ($_GET['numero']) {
    $n = $_GET['numero'];

    $podadorController = new Arb_PodadorController();
    $datos = $podadorController->getDatosCarnet($n);

    $datos = utf8ize($datos);

    $credencial = $datos["id"];
    $nombre = $datos["Nombre"];
    $dni = $datos["Documento"];
    $estado = $datos["estado"];
    $venc = $datos["fecha_vencimiento"];
    $revision = $datos["fecha_revision"];

    $renaper = new RenaperController();
    $img = $renaper->getImage($datos['genero'], $dni);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <title>Información Podador</title>
</head>

<body>
    <img class="p-3" style="width:100%" src="../../assets/banner.svg" />
    <div class="container mb-3">
        <div class="datos-perfil">
            <div class="card-body">
                <img class="rounded mx-auto d-block shadow-sm img-fluid m-3" style="max-width: 40%;" src="<?= $img["urlImagen"] ?>" alt="<?= $nombre ?>">
                <div class="container text-center">
                    <h3 style="font-size:1.5rem"><?= $nombre ?></h3>
                    <h4 style="font-size:1.2rem">DNI: <?= $dni ?></h4>
                    <h5 style="font-size: 1rem;">Credencial: <?= $credencial ?></h5>
                    <p class="text-dark" style="font-size: 0.8rem;">Fecha Otorgamiento: <?= $revision ?></p>
                    <p class="text-dark" style="font-size: 0.8rem;">Fecha Vencimiento: <?= $venc ?></p>

                </div>
            </div>
        </div>
    </div>

</body>

</html>