<?php

/**
 * Model for trip.
 *
 * This file hold the trip class.
 *
 * @version 1.0
 * @author recom3
 */
class TripClass implements JsonSerializable
{
    public $id;
    public $user_id;
    public $resort_id;
    public $sport_id;
    public $country_id;
    public $ws_trip_id;
    public $latitude;
    public $longitude;
    public $total_vert;
    public $city;
    public $start_datetime;
    public $jumps;
    public $trip_time;
    public $num_records;
    public $total_distance;
    public $max_speed;
    public $max_altitude;
    public $max_temp;
    public $min_temp;
    public $avg_temp;
    public $avg_speed;
    public $max_vertical_change;
    public $max_temp_change;
    public $active;
    public $created;
    public $modified;
    public $num_segments;
    public $max_jump_height;
    public $max_jump_distance;
    public $avg_jump_height;
    public $avg_jump_distance;
    public $max_jump_drop;
    public $max_air_time;
    public $avg_jump_drop;
    public $avg_air_time;
    public $num_jumps;
    public $public_share_override;
    public $trip_duration;
    public $sport;
    public $resort;
    public $country;

    public function __construct()
    {
        $unixTime = time();

        $this->start_datetime = "" . time();
    }

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}