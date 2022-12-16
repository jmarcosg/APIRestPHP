<?php

spl_autoload_register(function ($class_name) {
    $directorys = array(
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        CON_PATH . '/'
    );

    foreach ($directorys as $directory) {
        if (file_exists($directory . $class_name . '.php')) {
            include($directory . $class_name . '.php');
            return;
        }
    }
});

/* Permite hacer catch de los warning. */
set_error_handler(
    function ($severity, $message, $file, $line) {
        throw new ErrorException($message, $severity, $severity, $file, $line);
    }
);

function verEstructura($e, $exit = false)
{
    echo "<pre>";
    print_r($e);
    echo "</pre>";
    if ($exit) exit;
}

function getBearerToken()
{
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function getAuthorizationHeader()
{
    $headers = null;

    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

function sendRes($res, string $error = null, array $params = null)
{

    $res = utf8ize($res);
    if ($error) {
        echo json_encode(['data' => null, 'error' => $error, 'params' => $params]);
    } else {
        echo json_encode(['data' => $res, 'error' => $error, 'params' => $params], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

function utf8ize($d)
{
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string($d)) {
        if (!mb_detect_encoding($d, "UTF-8", true)) {
            return utf8_encode($d);
        }
        return $d;
    }
    return $d;
}

function deutf8ize($param)
{
    if (is_array($param)) {
        foreach ($param as $unaKey => $unValor) {
            $param[$unaKey] = deutf8ize($unValor);
        }
    } else if (is_string($param)) {
        if (mb_detect_encoding($param, "UTF-8", true)) {
            $param = utf8_decode($param);
        }
        return $param;
    }
    return $param;
}

function logFileEE($subPath, ErrorException $e, $class, $function, $data = [])
{
    $path = LOG_PATH . $subPath . "/";

    if (!file_exists($path)) mkdir($path, 0755, true);
    $errorMsg = $e->getMessage();
    $errorLine = $e->getLine();
    $msg = date("d/m/Y H:i:s") . " | $errorMsg | Line: $errorLine | Clase: $class | Function: $function";

    $logFile = fopen($path . date("Ymd") . ".log", 'a') or die("Error creando archivo");
    fwrite($logFile, "\n" . "$msg") or die("Error escribiendo en el archivo");
    fclose($logFile);
}

function createJsonError($subPath, ErrorException $e, $class = null, $function = null, $data = null, $obj = null)
{
    $path = LOG_PATH . $subPath . "/";

    if (!file_exists($path)) mkdir($path, 0755, true);

    $regArray = [
        'datetime' => date("d/m/Y H:i:s"),
        'error' => $e->getMessage(),
        'line' =>  $e->getLine(),
        'class' => $class,
        'object' => $obj,
        'trace' => $e->getTrace(),
        'function' => $function,
        'data' => $data,
        'globals' => [
            '$_COOKIE' => $_COOKIE,
            '$_ENV' => $_ENV,
            '$_FILES' => $_FILES,
            '$_GET' => $_GET,
            '$_POST' => $_POST,
            '$_REQUEST' => $_REQUEST,
            '$_SERVER' => $_SERVER,
        ],
    ];;

    $json_string = json_encode(utf8ize($regArray));
    $file = $path . date('Ymd_His') . '_' . uniqid() . '.json';

    file_put_contents($file, $json_string);
}

function isErrorException($object)
{
    return $object instanceof ErrorException;
}

function compararFechas($string, $get, $format = 'd/m/Y')
{
    $now = new DateTime();
    $date = new DateTime($string);

    $array = [
        'now' => $now,
        'date' => $date,
        'dif' => $now->diff($date, true)->$get,
    ];
    return $array;
}

function esVigente($string, $format = 'Y-m-d')
{
    if ($string == null) return true;

    $now = new DateTime();
    $date = DateTime::createFromFormat($format, $string);
    return $date > $now;
}

function eClean()
{
    session_unset();
    exit();
}

function is_multi_array(array $a)
{
    foreach ($a as $v) {
        if (is_array($v)) return true;
    }
    return false;
}

function getPathFile($file, $path, $fileName)
{
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    };

    if (!empty($file)) {
        $path = $path . $fileName;
    };

    return $path;
}

function getTypeFile($str)
{
    switch ($str) {
        case 'jpeg':
            return 'image/jpeg';
        case 'jpg':
            return 'image/jpg';
        case 'png':
            return 'image/png';
        case 'pdf':
            return 'application/pdf';
    }
}

function getBase64String($path, $file)
{
    $type = getTypeFile(explode('.', $file)[1]);
    return "data:$type;base64," . base64_encode(file_get_contents($path));
}

function getExtFile($file)
{
    if (!empty($file)) {
        switch ($file['type']) {
            case ('image/jpg'):
                return '.jpg';

            case ('image/jpeg'):
                return '.jpeg';

            case ('image/png'):
                return '.png';
            case ('png'):
                return '.png';

            case 'application/pdf':
                return '.pdf';

            case 'image/bmp':
                return '.bmp';
        }
    };
}

/**
 * @param $file
 * @param $fileType
 * @param $destinationFilepath
 * @return boolean
 * 
 * Esta funcion comprime imagenes que recibe en con extension .jpg, .jpeg, .png o .bmp
 * Crea una nueva imagen en formato .webp para no perder calidad en la compresion
 * Devuelve true si la compresion fue exitosa, false si no
 */
function comprimirImagen($file, $fileType, $destinationFilepath)
{
    $imgSize = $file['size'];
    $tempFile = $file['tmp_name'];

    // Porcentaje de compresion segun peso de la imagen
    // A menor porcentaje, mayor compresion
    if ($imgSize > 3072000) {
        $porcentajeCompresion = 1;
    } else if ($imgSize < 3072000 && $imgSize > 1024000) {
        $porcentajeCompresion = 75;
    } else if ($imgSize < 1024000) {
        $porcentajeCompresion = 75;
    } else if ($imgSize < 102400) {
        $porcentajeCompresion = 90;
    }

    if ($fileType == 'image/jpg' || $fileType == 'image/jpeg') {
        $image = imagecreatefromjpeg($tempFile);
    } elseif ($fileType == 'image/png') {
        $image = imagecreatefrompng($tempFile);

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    } elseif ($fileType == 'image/bmp') {
        $image = imagecreatefrombmp($tempFile);
    } else {
        return false;
    }

    if (imagewebp($image, $destinationFilepath, $porcentajeCompresion)) {
        imagedestroy($image);
        return true;
    } else {
        return false;
    }
}

function sendEmail($address, $subject, $body, $attachments = null)
{
    $arrayFilds = ['address' => $address, 'subject' => $subject, 'htmlBody' => $body];

    if ($attachments) {
        $arrayFilds['attachments'] = $attachments;
    }
    $post_fields = json_encode($arrayFilds);
    $uri = "https://weblogin.muninqn.gov.ar/api/Mail";
    $ch = curl_init($uri);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

function deleteDir($dirPath)
{
    if (is_dir($dirPath)) {
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}
/** Convert the first character of each word to uppercase: */
function firstUpper($string)
{
    return ucwords(strtolower($string));
}

function getQrByUlr($url, $w, $h, $color = '', $margin = '0', $error = 'L')
{
    $baseUrl = "https://chart.googleapis.com/chart?cht=qr&chco=$color&chs=$w" . "x$h&chl=$url&chld=$error|$margin";
    $data = file_get_contents($baseUrl);
    $base64 = 'data:image/png;base64,' . base64_encode($data);
    return $base64;
}

function sendResError($object, $error = null, $params = null)
{
    if ($object instanceof ErrorException) {
        sendRes(null, $error, $params);
    }
}

/**
 * Function that groups an array of associative arrays by some key.
 * 
 * @param {String} $key Property to sort by.
 * @param {Array} $data Array that stores multiple associative arrays.
 */
function group_by($key, $data)
{
    $result = array();

    foreach ($data as $val) {
        if (array_key_exists($key, $val)) {
            $result[$val[$key]][] = $val;
        } else {
            $result[""][] = $val;
        }
    }

    return $result;
}
