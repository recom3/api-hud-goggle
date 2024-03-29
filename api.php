<?php

require __DIR__ . '/const/api.const.php';
require __DIR__ . '/model/user.class.php';
require __DIR__ . '/model/trips.class.php';

$dbOptions = array(
	'db_host' => DB_HOST,
	'db_user' => DB_USER,
	'db_pass' => DB_PASS,
	'db_name' => DB_NAME
);
	
// Connecting to the database
DB::init($dbOptions);

// Execute the router with our list of routes.
if(!router($routes))
{
    $headers = getRequestHeaders();

    if(array_key_exists("Authorization", $headers))
    {
        $token = str_replace("Bearer ","", $headers["Authorization"]);
    }
    else
    {
        $token = "";
    }
	
    $path = $_SERVER['PATH_INFO'];

    if($path=='/login')
    {
        $jsonString = file_get_contents("php://input");

        $assocArray = json_decode($jsonString, true);
		
		$mail = filter_var($assocArray["username"], FILTER_SANITIZE_EMAIL);
		$mail = strtolower(trim($mail));
		$maxLenPassword = 50;
		$password = substr($assocArray["password"], 0, $maxLenPassword);
		
		$records = userdb::getUser($mail);
		
		if($row && password_verify($password, $row['pwd']))
		{
            $minExpiresTime = SEC_TOKEN_EXPIRATION;

            $secret = MY_API_SECRET;
            $expiration = time() + $minExpiresTime;
            $issuer = 'localhost';

            $token = Token::create($row["id"], $secret, $expiration, $issuer);

            if(!$isUplinkResponse)
            {
                echo $token;
            }
            else
            {
                $userData = new UserData();
                $userData->User = new UserClass();
                $userData->UserProfile = new UserProfileClass();

                $userData->token_type = "jwt";
                $userData->access_token = $token;

                $minExpiresTime = SEC_TOKEN_EXPIRATION * 24 * 7;
                $unixTime = time() + $minExpiresTime;
                $userData->expires = $unixTime;
                $userData->refresh_token = "";

                $userData->User->id = $row['id'];
                $userData->User->email = $row['mail'];

                $userData->User->first_name = $row['first_name'];
                $userData->User->last_name = $row['last_name'];
                $userData->User->facebook_id = "1234";
                $userData->User->mobile_active = "";
                $userData->User->last_login = "";
                $userData->User->measurement = "";

                $userData->User->buddy_tracking_enabled = false;
                $userData->User->buddy_tracking_stealth_mode_enabled = false;

                $userData->UserProfile->id = $row['id'];
                $userData->UserProfile->auto_upload_trips = false;
                $userData->UserProfile->phone_number = "";

                $userData->UserProfile->gender = "";
                $userData->UserProfile->country_id = "";
                $userData->UserProfile->city = "";
                $userData->UserProfile->bio = "";

                $userData->access_token = $token;

                echo json_encode(get_object_vars($userData));
            }
        }
        else
        {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }		
	}
    else if (
        //This is used from mobile app
        $path=='/token'
        //This is from windows app
        || $path=='/me'
        )
    {
        if($path=='/token')
        {
            $jsonString = file_get_contents("php://input");
            $queries = array();
            parse_str($jsonString, $queries);
            $userId = validateCode($queries["code"]);
        }
        else
        {
            $userId = validateToken();
        }
		
        $userData = new UserData();
        $userData->User = new UserClass();
        $userData->UserProfile = new UserProfileClass();

        $userData->token_type = "jwt";
        $userData->access_token = "<<to be replaced ahead>>";

        $minExpiresTime = 3600 * 24 * 7;
        $unixTime = time() + $minExpiresTime;
        $userData->expires = $unixTime;
        $userData->refresh_token = "xxxx";

        $userData->User->id = "1000";
        $userData->User->email = "me@mine.com";

        $userData->User->first_name = "first_name";
        $userData->User->last_name = "last_name";
        $userData->User->facebook_id = "1234";
        $userData->User->mobile_active = "";
        $userData->User->last_login = "";
        $userData->User->measurement = "";

        $userData->User->buddy_tracking_enabled = false;
        $userData->User->buddy_tracking_stealth_mode_enabled = false;

        $userData->UserProfile->id = "1000";
        $userData->UserProfile->auto_upload_trips = false;
        $userData->UserProfile->phone_number = "";

        $userData->UserProfile->gender = "";
        $userData->UserProfile->country_id = "";
        $userData->UserProfile->city = "";
        $userData->UserProfile->bio = "";

        $secret = MY_API_SECRET;
        $expiration = time() + $minExpiresTime;
		
        if(!$isRemoteDB)
        {
            $issuer = 'localhost';
        }
        else
        {
            $issuer = $remoteHost;
        }

        $token = Token::create($userId, $secret, $expiration, $issuer);

        $userData->access_token = $token;

        echo json_encode(get_object_vars($userData));		
	}		
	else if($path=='/signup')
    {
	}
    else if($path=='/download')
    {
	}
    else if ($path=='/meetrips')
    {
        $user_id = validateToken();

        $isMockTrips = false;

        $offset = 0;
        $limit = 9999;
        $records = tripdb::getTrips($user_id, $offset, $limit);

        $row = $records->fetch_array();
        if(!$row)
        {
            $isMockTrips = true;
        }

        if($isMockTrips)
        {
            $trip1 = new TripClass();
            $trips = array($trip1);

            $out = array_values($trips);

            echo json_encode($out);
        }
        else
        {
            //Output
            $result = array();

            if($row)
            {
                $result[] = CreateTripFromVw($row);
                while($row = $records->fetch_array()){
                    $result[] = CreateTripFromVw($row);
                }
            }

            $out = array_values($result);
            echo json_encode($out);
        }		
	}		
}
?>