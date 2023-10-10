<?php
define("DB_HOST", "localhost");
define("DB_USERNAME", "<db user>");
define("DB_PASSWORD", "<db pass>");
define("DB_DATABASE_NAME", "<db name>");

define("MY_API_SECRET", '<proper secret>');
define("SEC_TOKEN_EXPIRATION", 3600 * 24 * 7);

if(!$isRemoteDB)
{
    //dev
    $dbOptions = array(
        'db_host' => 'localhost',
        'db_user' => '<db user>',
        'db_pass' => '<db pass>',
        'db_name' => '<db name>'
    );
}
else
{
    //pro
    $dbOptions = array(
        'db_host' => '',
	    'db_user' => '<db user>',
	    'db_pass' => '<db pass>',
	    'db_name' => '<db name>'
    );
}
?>