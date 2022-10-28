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

function verEstructura($e, $die = false)
{
    echo "<pre>";
    print_r($e);
    echo "</pre>";
    if ($die) die();
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
        echo json_encode(['data' => $res, 'error' => $error], JSON_UNESCAPED_UNICODE);
    }
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

function logFileEE($subPath, ErrorException $e, $class, $function)
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

/**
 * Chequea que el tamaño y tipo de archivos subidos sean los correctos
 * JS Alert si no lo son
 * @param int maxsize en mb del archivo, default 200mb
 * @param array formatos aceptados
 * @return bool false si hubo un error en el chequeo de archivos
 */
function checkFile($maxsize = 15)
{
    $acceptable = array('application/pdf', 'image/jpeg', 'image/jpg', 'image/gif', 'image/png', 'video/mp4', 'video/mpeg');
    if (isset($_FILES) && !empty($_FILES)) {
        $errors = array();

        $phpFileUploadErrors = array(
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk.',
            8 => 'A PHP extension stopped the file upload.',
        );

        $maxsize_multiplied = $maxsize * 1000000;

        foreach ($_FILES as $key => $value) {
            if (($value['size'] >= $maxsize_multiplied) && ($value['size'] != 0)) {
                $errors[] = "$key Archivo adjunto muy grande. Debe pesar menos de $maxsize megabytes.";
            }
            if ((!in_array($value['type'], $acceptable)) && !empty($value['type'])) {
                $error = "$key Tipo de archivo invalido. Solamente tipos ";
                foreach ($acceptable as $val) {
                    $error .= $val . ', ';
                }
                $error .= "se aceptan.";
                $errors[] = $error;
            }
            if ($value['error'] != 0 && !empty($value['type'])) {
                $errors[] = $phpFileUploadErrors[$value['error']];
            }
        }

        if (count($errors) === 0) {
            return true;
        } else {
            foreach ($errors as $error) {
                echo '<script>alert("' . $error . '");</script>';
            }
            return false;
        }
    }
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

            case 'application/pdf':
                return '.pdf';

            case 'image/bmp':
                return '.bmp';
        }
    };
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
        exit;
    }
}
