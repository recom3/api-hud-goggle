<?php

/**
 * buddies short summary.
 *
 * buddies description.
 *
 * @version 1.0
 * @author recom3
 */
class Buddies implements JsonSerializable
{
    public $Friend;
    public $FriendRequest;
    public $FriendRequesting;
    public $FriendRemoved;
    public $FriendRejected;
    public $epoch;

    public function __construct()
    {
        //$unixTimeMsec = time()*1000;
        $unixTime = time();
        $this->epoch = "".$unixTime;
    }

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}

class Friend implements JsonSerializable
{
    public $id;
    public $first_name;
    public $last_name;

    public $UserLocation;

    public function __construct()
    {
        $this->id = "12345";
        $this->first_name = "xxxx";
        $this->last_name = "yyyy";
        $this->UserLocation = new UserLocation();
    }

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}

class UserLocation implements JsonSerializable
{
    public $latitude;
    public $longitude;
    public $location_time;

    //latitude=dd.ddddddd&amp;location_time=1644089668&amp;accuracy=dd.ddddddddddddddd&amp;longitude=dd.ddddddd&amp;provider=phone_fused

    public function __construct()
    {
        $this->longitude = "00.0000000";
        $this->latitude = "00.0000000";
        //$this->location_time = "1644089668";
        $unixTime = time();
        $this->location_time = "".$unixTime;
    }

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}
?>