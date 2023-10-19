<?php

/**
 * Download controller module.
 *
 * This file holds operations related to downloads.
 *
 * @version 1.0
 * @author recom3
 */
class DownloadController extends BaseController
{
    public function downloadgpx($path)
    {
        $path_parts=explode("/",$path);
        $pos_verb=3;
        $pos_id=2;
        //echo "id=" . $path_parts[$pos_id];

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

    public function downloadfile($path)
    {
        $path_parts=explode("/",$path);
        $pos_verb=3;
        $pos_id=2;

        if(!empty($path_parts[$pos_id]))
        {
            $file_name = "";
            for($i=$pos_id;$i<count($path_parts);$i++)
            {
                //echo $i;
                if($file_name!="")
                {
                    $file_name .= "/";
                }
                $file_name .= $path_parts[$i];
            }
        }
        else
        {
            exit;
        }

        $user_id = $this->validateToken();

        $file=__DIR__ . "/priv/" . $file_name;

        header("Pragma: public");
        header('Content-disposition: attachment; filename='.$file_name);

        readfile($file);
    }
}
?>