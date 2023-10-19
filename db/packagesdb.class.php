<?php

/**
 * tripdb short summary.
 *
 * tripdb description.
 *
 * @version 1.0
 * @author recom3
 */
class packagesdb extends ObjBase implements JsonSerializable
{
    protected $name='',$versionCode='',$versionName='',$fileName='',
        $id_user = 0,$id = 0,$image='',$label='',$description='';

	public function deleteAll(){
		DB::query("
			DELETE FROM user_app WHERE id_user=
				" . $this->id_user . "");
	}

	public static function getPackages($user_id, $offset, $limit){

        $query = sprintf("SELECT DISTINCT * FROM packages
            ORDER BY id DESC
            ");

        $query = sprintf("SELECT DISTINCT p.*,CASE WHEN u_a.id_user IS NULL THEN '' ELSE '1' END as id_user FROM packages p
            LEFT JOIN user_app u_a ON u_a.id_app=p.id AND u_a.id_user = %d
            WHERE u_a.id_user = %d OR u_a.id_user IS NULL
            ORDER BY p.id DESC
            ", $user_id, $user_id);

		$result = DB::query($query);

        return $result;
	}

	public function update(){

		DB::query("
			INSERT INTO user_app (id_user, id_app)
			VALUES (
				" . $this->id_user . "," . $this->id . "
			) ON DUPLICATE KEY UPDATE id_app = " . $this->id . "
			");
	}

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}
