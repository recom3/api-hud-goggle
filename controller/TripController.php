<?php

/**
 * Trip controller module.
 *
 * This file holds the operations related to trips.
 *
 * @version 1.0
 * @author recom3
 */
class TripController extends BaseController
{
    function CreateTripFromVw($row)
    {
        $tripData = new TripClass();

        if(empty($row['totDiff'])==false && $row['totDiff']!=0)
        {
            $kmhAvgSpeed = $row['totDist'] / $row['totDiff'] * 3600.0 / 1000.0;
        }
        else
        {
            $kmhAvgSpeed = 0;
        }

        $tripData->id = $row['id'];

        $tripData->city = $row['name'];
        $tripData->total_distance = "".round($row['totDist']);
        $tripData->trip_duration = "".round($row['totDiff']);
        $tripData->max_speed = "".round($row['maxSpeed']);
        $tripData->avg_speed = "".round($kmhAvgSpeed);
        $tripData->total_vert = "".round($row['totVert']);
        ////$tripData->maxDist = str_replace(".",",",$row['maxDist']);
        ////$tripData->trackDist = str_replace(".",",",$row['trackDist']);
        $tripData->max_vertical_change = "".round($row['maxVert']);
        ////$tripData->trackVert = str_replace(".",",",$row['trackVert']);
        $tripData->max_altitude = "".round($row['maxAlt']);
        ////$tripData->minAlt = str_replace(".",",",$row['minAlt']);

        $tripData->country = ($row['country'] === null || trim($row['country']) === '' ? $tripData->country : $row['country']);

        return $tripData;
    }

    public function meetripss()
    {
        $user_id = $this->validateToken();

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
                $result[] = $this->CreateTripFromVw($row);
                while($row = $records->fetch_array()){
                    $result[] = $this->CreateTripFromVw($row);
                }
            }

            $out = array_values($result);
            echo json_encode($out);
        }        
    }

    public function downloadgpx($path)
    {
        $path_parts=explode("/",$path);
        $pos_verb=3;
        $pos_id=2;

        if(!empty($path_parts[$pos_id]))
        {
            $idTrip = $path_parts[$pos_id];
        }
        $user_id = $this->validateToken();

        $offset = 0;
        $limit = 9999;
        if(!empty($idTrip))
        {
            $records = tripdb::getTrip($idTrip);
        }
        else
        {
            $records = tripdb::getTrips($user_id, $offset, $limit);
        }

        $row = $records->fetch_array();
        if($row)
        {
            $file_name=sprintf("%s_%s", $row['id'], $row['fileName']);
            $file=__DIR__ . "/priv/" . $file_name;

            header("Pragma: public");
            header('Content-disposition: attachment; filename='.$file_name);

            readfile($file);
        }        
    }

    public function metripsupdate()
    {
        $user_id = $this->validateToken();

        $jsonString = file_get_contents("php://input");
        $assocArray = json_decode($jsonString, true);

        $name = $assocArray["name"];
        $totDist = $assocArray["totDist"];
        $totDiff = $assocArray["totDiff"];
        $maxSpeed = $assocArray["maxSpeed"];
        $totVert = $assocArray["totVert"];
        $maxDist = $assocArray["maxDist"];
        $trackDist = $assocArray["trackDist"];
        $maxVert = $assocArray["maxVert"];
        $trackVert = $assocArray["trackVert"];
        $maxAlt = $assocArray["maxAlt"];
        $minAlt = $assocArray["minAlt"];
        $fileName = $assocArray["fileName"];
        $country = $assocArray["country"];

        //Update db
        $tripdb = new tripdb(array(
            'id_user' => $user_id,
            'name' => $name,
            'totDist' => $totDist,
            'totDiff' => $totDiff,
            'maxSpeed' => $maxSpeed,
            'totVert' => $totVert,
            'maxDist' => $maxDist,
            'trackDist' => $trackDist,
            'maxVert' => $maxVert,
            'trackVert' => $trackVert,
            'maxAlt' => $maxAlt,
            'minAlt' => $minAlt,
            'fileName' => $fileName,
            'country' => $country
            ));
        $idTrip = $tripdb->insert();

        $tripObj = (object)array(
        "id_trip" => $idTrip
        );

        echo json_encode($tripObj);
    }
}
?>