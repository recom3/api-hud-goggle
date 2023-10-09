<?php
use ReallySimpleJWT\Token;
use ReallySimpleJWT\Exception\ValidateException;
use Symfony\Component\Debug\Exception\DummyException;

/**
 * Login controller module.
 *
 * This file holds the operations relaten to login, signup, obtaining token.
 *
 * @version 1.0
 * @author recom3
 */
class LoginController //extends BaseController
{
    public function login()
    {
        $jsonString = file_get_contents("php://input");

        $assocArray = json_decode($jsonString, true);
	
        //This is the login of uplink form
        $queries = array();
        parse_str($jsonString, $queries);

        //Input: incomming parameters
        //$assocArray["username"]
        //$assocArray["password"]
        $isUplinkResponse = false;
        if(array_key_exists("email", $queries))
        {
            $isUplinkResponse = true;
            //This is the login of uplink form
            $mail = $queries["email"];
            $mail = strtolower(trim($mail));
            $password = $queries["password"];
        }
        else
        {
            $mail = filter_var($assocArray["username"], FILTER_SANITIZE_EMAIL);
            $mail = strtolower(trim($mail));
            $maxLenPassword = 50;
            $password = substr($assocArray["password"], 0, $maxLenPassword);
        }

		$records = userdb::getUser($mail);

        $row = $records->fetch_array();

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

                $minExpiresTime = SEC_TOKEN_EXPIRATION;
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

    public function token($path)
    {
        global $isRemoteDB, $remoteHost, $secret;

        $secret = MY_API_SECRET;

        if($path=='/token')
        {
            $jsonString = file_get_contents("php://input");
            $queries = array();
            parse_str($jsonString, $queries);
            $userId = $this->validateCode($queries["code"]);
        }
        else
        {
            $userId = $this->validateToken();
        }

        $userData = new UserData();
        $userData->User = new UserClass();
        $userData->UserProfile = new UserProfileClass();

        $userData->token_type = "jwt";
        $userData->access_token = "<replace with access token>";

        $minExpiresTime = SEC_TOKEN_EXPIRATION;
        $unixTime = time() + $minExpiresTime;
        $userData->expires = $unixTime;
        $userData->refresh_token = "xxxx";

        $userData->User->id = "1000";//userRecord.id;
        $userData->User->email = "me@mine.com";

        $userData->User->first_name = "first_name";//userRecord.first_name;
        $userData->User->last_name = "last_name";//userRecord.last_name;
        $userData->User->facebook_id = "1234";
        $userData->User->mobile_active = "";
        $userData->User->last_login = "";
        $userData->User->measurement = "";

        $userData->User->buddy_tracking_enabled = false;
        $userData->User->buddy_tracking_stealth_mode_enabled = false;

        $userData->UserProfile->id = "1000";//userRecord.id;
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

    public function signup()
    {
        $jsonString = file_get_contents("php://input");
        $assocArray = json_decode($jsonString, true);
        $first_name = $assocArray["firstname"];
        $last_name = $assocArray["username"];
        $password = $assocArray["password"];
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $mail = $assocArray["email"];
        $tx_hash = $assocArray["hash"];

        //Update db
        $userdb = new userdb(array(
                'id' => 0,
                'first_name'=> $first_name,
                'last_name' => $last_name,
                'latitude' => 0,
                'longitude' => 0,
                'password' => $hash,
                'mail' => $mail,
                'hash' => $tx_hash
            ));
        $userdb->insert();
    }

    function validateCode($code)
    {
        global $secret;

        $result = Token::validate($code, $secret);
        if(!$result)
        {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        $user_id = Token::getPayload($code, $secret)['user_id'];

        return $user_id;
    }

    function validateToken()
    {
        global $token, $secret;

        //echo "token=" . $token . "\n";

        $result = Token::validate($token, $secret);

        if(!$result)
        {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        try{
            $user_id = Token::getPayload($token, $secret)['user_id'];
        }
        catch (ValidateException $e) {
            echo 'Captured exception: ',  $e->getMessage(), "\n";
            echo 'Code: ',  $e->getCode(), "\n";
            exit;
        }

        return $user_id;
    }
}
?>