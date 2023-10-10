<?php
use ReallySimpleJWT\Token;
use ReallySimpleJWT\Exception\ValidateException;
use Symfony\Component\Debug\Exception\DummyException;

/**
 * Trip controller module.
 *
 * This file holds the operations related to buddies.
 *
 * @version 1.0
 * @author recom3
 */
class UserController extends BaseController
{
    function CreateUserFromVw($row)
    {
        $userData = new UserData();
        $userData->User = new UserClass();
        $userData->UserProfile = new UserProfileClass();

        $userData->token_type = "jwt";
        $userData->access_token = "<replace with access token>";
        $userData->relation_state = "none";

        $minExpiresTime = 3600;
        $unixTime = time() + $minExpiresTime;
        $userData->expires = $unixTime;
        $userData->refresh_token = "xxxx";

        $userData->User->id = $row['id'];
        $userData->User->email = "me@mine.com";

        $userData->User->first_name = $row['first_name'];//userRecord.first_name;
        $userData->User->last_name = $row['last_name'];//userRecord.last_name;
        $userData->User->facebook_id = $row['id'];
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

        return $userData;
    }

    function CreateBuddies($user_id)
    {
        $buddies = new Buddies();

        $buddies->Friend = array();
        $buddies->FriendRequest = array();
        $buddies->FriendRequesting = array();
        $buddies->FriendRemoved = array();
        $buddies->FriendRejected = array();

        $records = frienddb::getFriendsAccepted($user_id);
        $row = $records->fetch_array();
        if($row)
        {
            $buddies->Friend[] = $this->CreateUserFromFriend($row, $user_id);
            while($row = $records->fetch_array()){

                $buddies->Friend[] = $this->CreateUserFromFriend($row, $user_id);
            }
        }

        $records = frienddb::getReceivedRequest($user_id);
        $row = $records->fetch_array();
        if($row)
        {
            $buddies->FriendRequest[] = $this->CreateUserFromFriend($row, $user_id);
            while($row = $records->fetch_array()){

                $buddies->FriendRequest[] = $this->CreateUserFromFriend($row, $user_id);
            }
        }

        $records = frienddb::getSentRequest($user_id);
        $row = $records->fetch_array();
        if($row)
        {
            $buddies->FriendRequesting[] = $this->CreateUserFromFriend($row, $user_id);
            while($row = $records->fetch_array()){

                $buddies->FriendRequesting[] = $this->CreateUserFromFriend($row, $user_id);
            }
        }

        $records = frienddb::getRemovedRequest($user_id);
        $row = $records->fetch_array();
        if($row)
        {
            $buddies->FriendRemoved[] = $this->CreateUserFromFriend($row, $user_id);
            while($row = $records->fetch_array()){

                $buddies->FriendRemoved[] = $this->CreateUserFromFriend($row, $user_id);
            }
        }

        $records = frienddb::getRejectedRequest($user_id);
        $row = $records->fetch_array();
        if($row)
        {
            $buddies->FriendRejected[] = $this->CreateUserFromFriend($row, $user_id);
            while($row = $records->fetch_array()){

                $buddies->FriendRejected[] = $this->CreateUserFromFriend($row, $user_id);
            }
        }

        return $buddies;
    }

    /*
     * This function is using for searching friends in /userss
     */
    function CreateUserFromFriend($row, $idUser)
    {
        $Friend = new Friend();

        if ($row['id'] == $idUser)
        {
            $Friend->id = $row['id_dest'];
            $Friend->first_name = $row['first_name_dest'];
            $Friend->last_name = $row['last_name_dest'];
            $Friend->UserLocation->longitude = "".$row['longitude_dest']/10000000;
            $Friend->UserLocation->latitude = "".$row['latitude_dest']/10000000;
            $Friend->UserLocation->location_time = "".$row['loc_time_dest'];
        }
        else
        {
            $Friend->id = $row['id'];
            $Friend->first_name = $row['first_name'];
            $Friend->last_name = $row['last_name'];

            $Friend->UserLocation->longitude = "".$row['longitude']/10000000;
            $Friend->UserLocation->latitude = "".$row['latitude']/10000000;
            $Friend->UserLocation->location_time = "".$row['loc_time'];

            //$Friend->UserLocation->longitude = str_replace(".",",","".$row['latitude']/10000000);
            //$Friend->UserLocation->latitude = str_replace(".",",","".$row['latitude']/10000000);
        }

        return $Friend;
    }

    public function userss()
    {
        $user_id = $this->validateToken();

        $search = trim(filter_var($_GET["name"], FILTER_SANITIZE_STRING));
        $offset =  filter_var($_GET["offset"], FILTER_SANITIZE_NUMBER_INT) - 1;
        $limit =  filter_var($_GET["limit"], FILTER_SANITIZE_NUMBER_INT);

        $minLenSearch = 4;
        if(strlen($search)<$minLenSearch)
        {
            echo "[]";
            exit;
        }

        //Output
        $result = array();

        $records = frienddb::getFriends($user_id, $search, $offset, $limit);

		$row = $records->fetch_array();

		if($row)
		{
            $result[] = $this->CreateUserFromVw($row);
			while($row = $records->fetch_array()){
                $result[] = $this->CreateUserFromVw($row);
			}
		}

        $out = array_values($result);
        echo json_encode($out);        
    }

    public function friend($path)
    {
        echo "friend accept";
        echo "\r\n";
        echo $path;
        echo "\r\n";
        $path_parts=explode("/",$path);
        $pos_verb=3;
        $pos_id=2;
        echo "verb=" . $path_parts[$pos_verb];

        $user_id = $this->validateToken();
        $verb = $path_parts[$pos_verb];
        $id = $path_parts[$pos_id];

        if($verb=="accept")
        {
			$request = new frienddb(array(
					'id_source'	=> $id,
					'id_dest' => $user_id,
                    'status' => 'accept'
				));
			$request->update();
        }
        else if($verb=="invite")
        {
			$request = new frienddb(array(
					'id_source'	=> $user_id,
					'id_dest' => $id,
                    'status' => 'request'
				));
			$request->update();
        }
        else if($verb=="reject")
        {
			$request = new frienddb(array(
					'id_source'	=> $id,
					'id_dest' => $user_id,
                    'status' => 'reject'
				));
			$request->update();
        }
        else if($verb=="remove")
        {
			$request = new frienddb(array(
					'id_source'	=> $user_id,
					'id_dest' => $id,
                    'status' => 'remove'
				));
			$request->update();
        }
    }

    public function meefriendsslocationss()
    {
        $user_id = $this->validateToken();

        $buddies = $this->CreateBuddies($user_id);

        echo json_encode($buddies);
    }

    public function meelocationss()
    {
        $user_id = $this->validateToken();

        //Input data in this format
        //latitude=dd.ddddddd&location_time=1645867502&accuracy=dd.dddddddddddddd&longitude=dd.dddddd&provider=phone_fused

        //Input data
        $jsonString = file_get_contents("php://input");

        $assocArray = json_decode($jsonString, true);
        $queries = array();
        parse_str($jsonString, $queries);
        $latitude = $queries["latitude"];
        $longitude = $queries["longitude"];
        $location_time = $queries["location_time"];

        //Update db
        $userdb = new userdb(array(
                'id'	=> $user_id,
                'latitude' => $latitude * 10000000,
                'longitude' => $longitude * 10000000,
                'loc_time' => $location_time
            ));
        $userdb->update();

        //Output
        $buddies = new Buddies();
        $buddies->Friend = array();
        $buddies->FriendRequest = array();
        $buddies->FriendRequesting = array();
        $buddies->FriendRemoved = array();
        $buddies->FriendRejected = array();

        echo json_encode($buddies);
    }
}
?>