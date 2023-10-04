<?php

/**
 * user short summary.
 *
 * user description.
 *
 * @version 1.0
 * @author recom3
 */
class UserProfileClass implements JsonSerializable
{
    public $id;
    public $auto_upload_trips;
    public $phone_number;
    public $gender;
    public $city;
    public $country_id;
    public $bio;

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}

class UserClass implements JsonSerializable
{
    public $id;
    public $email;
    public $first_name;
    public $last_name;
    public $facebook_id;
    public $mobile_active;
    public $last_login;
    public $measurement;
    public $buddy_tracking_enabled;
    public $buddy_tracking_stealth_mode_enabled;

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}

class UserData implements JsonSerializable
{
    public $access_token;
    public $token_type;
    public $expires;
    public $refresh_token;
    public $User;
    public $UserProfile;

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}