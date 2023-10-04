<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");
// include main configuration file 
require_once PROJECT_ROOT_PATH . '/vendor/autoload.php';
require_once PROJECT_ROOT_PATH . "/inc/env.php";

require_once PROJECT_ROOT_PATH . "/db/DB.class.php";
require_once PROJECT_ROOT_PATH . "/inc/config" . (!empty($env) ? "." . $env : "" ) . ".php";
require_once PROJECT_ROOT_PATH . "/router/router.php";

require_once PROJECT_ROOT_PATH . "/Controller/BaseController.php";
require_once PROJECT_ROOT_PATH . "/db/ObjBase.php";
require_once PROJECT_ROOT_PATH . "/db/userdb.class.php";
require_once PROJECT_ROOT_PATH . "/db/tripdb.class.php";
require_once PROJECT_ROOT_PATH . "/db/packagesdb.class.php";
require_once PROJECT_ROOT_PATH . "/db/frienddb.class.php";

require_once PROJECT_ROOT_PATH . "/model/user.class.php";
require_once PROJECT_ROOT_PATH . "/model/trip.class.php";

use ReallySimpleJWT\Token;
use ReallySimpleJWT\Exception\ValidateException;
use Symfony\Component\Debug\Exception\DummyException;

// Connecting to the database
DB::init($dbOptions);
?>