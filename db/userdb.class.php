<?php

/**
 * userdb short summary.
 *
 * userdb description.
 *
 * @version 1.0
 * @author recom3
 */
class userdb extends ObjBase
{
	protected $id = '', $first_name = '', $last_name='', $latitude = '', $longitude = '', $password = '', $mail = '', $hash = '', 
        $loc_time = 0;

	public function insert(){
        $values = sprintf("'%s','%s',%d,%d,'%s','%s','%s'", $this->first_name, $this->last_name, $this->latitude, $this->longitude,
            $this->password, $this->mail, $this->hash);

        $query = "
			INSERT INTO user (first_name, last_name, latitude, longitude, pwd, mail, hash)
			VALUES (
				" . $values . "
			) ON DUPLICATE KEY UPDATE latitude = ".$this->latitude.", longitude = ".$this->longitude;
        //echo $query;
		DB::query($query);
	}

	public function update(){
        $values = sprintf("%d,'%s','%s',%d,%d,'%s','%s',%d", $this->id, $this->first_name, $this->last_name, $this->latitude, $this->longitude,
            $this->password, $this->mail, $this->loc_time);

        $query = "
			INSERT INTO user (id, first_name, last_name, latitude, longitude, pwd, mail, loc_time)
			VALUES (
				" . $values . "
			) ON DUPLICATE KEY UPDATE latitude = ".$this->latitude.", longitude = ".$this->longitude.", loc_time = ".$this->loc_time;
        //echo $query;
		DB::query($query);
	}

	public static function getUser($mail){

        $query = sprintf("SELECT * FROM user WHERE mail = '%s'", $mail);
        //echo $query;
		$result = DB::query($query);
        return $result;
	}
}
