<?php
require __DIR__ . "/inc/bootstrap.php";

function getRequestHeaders() {
    $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$controller = "";
if(!router($controller))
{
    header("HTTP/1.1 404 Not Found");
    exit();
}

//Get token from headers
$headers = getRequestHeaders();
if(array_key_exists("Authorization", $headers))
{
    $token = str_replace("Bearer ","", $headers["Authorization"]);
}
else
{
    $token = "";
}

require PROJECT_ROOT_PATH . "/controller/" . $controller;
$class=str_replace(".php", "", $controller);
$objFeedController = new $class();

$strMethodName = str_replace("/", "", $_SERVER['PATH_INFO']);
$objFeedController->{$strMethodName}($_SERVER['PATH_INFO']);
?>