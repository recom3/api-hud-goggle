<?php

/**
 * frienddb short summary.
 *
 * frienddb description.
 *
 * @version 1.0
 * @author recom3
 */
class frienddb extends ObjBase
{
	protected $id_source = '', $id_dest = '', $status = '';

	public function update(){
		DB::query("
			INSERT INTO request (id_source, id_dest, status)
			VALUES (
				" . $this->id_source . "," . $this->id_dest . ",'" . $this->status . "'
			) ON DUPLICATE KEY UPDATE status = '".$this->status."'
			");
	}

	public static function getFriends($user_id, $search, $offset, $limit){

        //var users = db.vwFriends.Where(u => u.id!=idUser && ((u.id_source!=idUser && u.id_dest!=idUser)
        //For what is needed this?
        //    || u.status != "accept"
        //    || u.status != "reject"
        //    || u.status != "remove"
        //    || u.status != "request"

        //Description:
        //Line 1: don't show himself/herself in results
        //Line 2: don't show pending requests
        $query = sprintf("SELECT DISTINCT * FROM vwfriends WHERE id != %d AND
            ((COALESCE(id_source,0)!=%d AND COALESCE(id_dest,0)!=%d) OR
              (COALESCE(id_source,0)=%d AND status='remove')) AND
            LOWER(last_name) LIKE '%%%s%%' ORDER BY id LIMIT %u OFFSET %u
            ", $user_id, $user_id, $user_id, $user_id, $search, $limit, $offset);

		$result = DB::query($query);

        return $result;
	}

	public static function getFriendsAccepted($user_id){

        $query = sprintf("SELECT DISTINCT * FROM vwincomingrequest WHERE
            (COALESCE(id,0)=%d OR COALESCE(id_dest,0)=%d
            ) AND status='accept'", $user_id, $user_id);

        //echo $query;

		$result = DB::query($query);

        return $result;
	}

	public static function getReceivedRequest($user_id){

        $query = sprintf("SELECT DISTINCT * FROM vwincomingrequest WHERE
            (COALESCE(id_dest,0)=%d
            ) AND status='request'", $user_id);

        //echo $query;

		$result = DB::query($query);

        return $result;
	}

	public static function getSentRequest($user_id){

        $query = sprintf("SELECT DISTINCT * FROM vwincomingrequest WHERE
            (COALESCE(id,0)=%d
            ) AND status='request'", $user_id);

        //echo $query;

		$result = DB::query($query);

        return $result;
	}

	public static function getRemovedRequest($user_id){

        $query = sprintf("SELECT DISTINCT * FROM vwincomingrequest WHERE
            (COALESCE(id,0)=%d
            ) AND status='remove'", $user_id);

        //echo $query;

		$result = DB::query($query);

        return $result;
	}

	public static function getRejectedRequest($user_id){

        $query = sprintf("SELECT DISTINCT * FROM vwincomingrequest WHERE
            (COALESCE(id_dest,0)=%d
            ) AND status='reject'", $user_id);

        //echo $query;

		$result = DB::query($query);

        return $result;
	}

}