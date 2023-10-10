<?php

$routes = array(
    '/hello' => 'Hello, World!',
    '/oauth2' => 'login.html',

    '/login' => 'LoginController.php',
    '/token' => 'LoginController.php',
    '/meetripss' => 'TripController.php',
    '/packages' => 'PackageController.php',
    '/userss' => 'UserController.php'
);

// This is our router.
function router(&$controller)
{
    global $routes;

    //echo "path info=" . $_SERVER['PATH_INFO'];
    //echo "<br/>";

    // Iterate through a given list of routes.
    foreach ($routes as $path => $content) {

        //echo $path;
        //echo "<br/>";
        //echo $content;
        //echo "<br/><br/>";

        if ($path == $_SERVER['PATH_INFO']) {
            // If the path matches, display its contents and stop the router.
            //echo $content;
            if (strpos($path, '.php') !== false)
            {
                echo file_get_contents($content);
            }
            else
            {
                $controller = $content;    
            }
            return true;
        }
    }

    // This can only be reached if none of the routes matched the path.
    //echo 'Sorry! Page not found';
    return false;
}
?>