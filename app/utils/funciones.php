<?php

$rm = $_SERVER['REQUEST_METHOD'];

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

function verEstructura($e)
{
    echo "<pre>";
    print_r($e);
    echo "</pre>";
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
    if ($error) {
        echo json_encode(['data' => null, 'error' => $error, 'params' => $params]);
    } else {
        echo json_encode(['data' => $res, 'error' => $error]);
    }
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
