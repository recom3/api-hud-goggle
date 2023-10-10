<?php

/**
 * tripdb short summary.
 *
 * tripdb description.
 *
 * @version 1.0
 * @author recom3
 */
class tripdb extends ObjBase
{
	protected $id = '',
        $id_user = 0,
        $name = '',
        $totDist = 0, $totDiff = 0, $maxSpeed = 0, $totVert = 0, $maxDist = 0, $trackDist = 0, $maxVert = 0, $trackVert = 0, $maxAlt = 0, $minAlt = 0,
        $loc_time = 0,
        $fileName = '',
        $country = '';

	public function insert(){
        $values = sprintf("%d,'%s',%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,'%s','%s'",
            $this->id_user,
            $this->name, $this->totDist, $this->totDiff, $this->maxSpeed, $this->totVert, $this->maxDist,
            $this->trackDist, $this->maxVert, $this->trackVert, $this->maxAlt, $this->minAlt,
            $this->fileName,
            $this->country
            );

        $query = "
            INSERT INTO trip
            (id_user, name, totDist, totDiff, maxSpeed, totVert, maxDist, trackDist, maxVert, trackVert, maxAlt, minAlt, fileName, country)
            VALUES
            (
				" . $values . "
			)";
        //echo $query;
		DB::query($query);

        return DB::getMySQLiObject()->insert_id;
	}

	public static function getTrips($user_id, $offset, $limit){

        $query = sprintf("SELECT DISTINCT * FROM trip WHERE id_user = %d
            ORDER BY id DESC
            ", $user_id);

        //echo $query;

		$result = DB::query($query);

        return $result;
	}

	public static function getTrip($trip_id){

        $query = sprintf("SELECT DISTINCT * FROM trip WHERE id = %d
            ", $trip_id);

        //echo $query;

		$result = DB::query($query);

        return $result;
	}
}
