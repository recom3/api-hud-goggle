<?php

/**
 * Package controller module.
 *
 * This file holds the operations related to trips.
 *
 * @version 1.0
 * @author recom3
 */
class PackageController extends BaseController
{
    /*
     * End point created for checking package update: this is not supported in original dll
     */
    function packages()
    {
        $user_id = $this->getUserFromToken();

        $offset = 0;
        $limit = 9999;
        $records = packagesdb::getPackages($user_id, $offset, $limit);

        //Output
        $result = array();

        $row = $records->fetch_array();

        if($row)
        {
            $result[] = new packagesdb(array(
                            'id'		=> $row['id'],
                            'id_user'	=> $row['id_user'],
							'name'		=> $row['name'],
							'versionCode' => $row['versionCode'],
                            'versionName' => $row['versionName'],
                            'fileName' => $row['fileName'],
                            'image' => $row['image'],
                            'label' => $row['label'],
                            'description' => $row['description']
						));
            while($row = $records->fetch_array()){
                $result[] = new packagesdb(array(
                            'id'		=> $row['id'],
                            'id_user'	=> $row['id_user'],
							'name'		=> $row['name'],
							'versionCode' => $row['versionCode'],
                            'versionName' => $row['versionName'],
                            'fileName' => $row['fileName'],
                            'image' => $row['image'],
                            'label' => $row['label'],
                            'description' => $row['description']
						));
            }
        }

        $out = array_values($result);
        echo json_encode($out);        
    }

    /**
     * Updates the packages to be downloaded by user (it is used by the web)
     */
    function packagessupdate()
    {
        $user_id = $this->validateToken();

        $jsonString = file_get_contents("php://input");
        //The second parameter set the result as an object(false, default) or an associative array(true).
        $assocArray = json_decode($jsonString, true);

        $userPackages = new packagesdb(array(
                        'id_user'	=> $user_id
					));

        $userPackages->deleteAll();

        foreach($assocArray as $row) {

            $userPackages = new packagesdb(array(
                            'id_user'	=> $user_id,
							'id'	=> $row['id']
						));

            $userPackages->update();
        }
    }

    /**
     * Download large packages and apps from the app store
     * @param mixed $path 
     */
    function packagedownload($path)
    {
        $user_id = $this->validateToken();

        $path_parts=explode("/",$path);
        $pos_verb=1;
        $pos_id=2;

        $file_name = $path_parts[$pos_id];
        $file=__DIR__ . "/priv/" . $file_name;

        //Deactivate buffering for large files
        if (ob_get_level()) {
            ob_end_clean();
        }

        header("Pragma: public");
        header('Content-disposition: attachment; filename='.$file_name);
        header('Content-Length: ' . filesize($file));
        //header("Content-type: ".mime_content_type($file));
        //header('Content-Transfer-Encoding: binary');
        //ob_clean();
        //flush();
        readfile($file);
    }

    /**
     * Update all the packages a user have
     */
    function mepackagessupdate()
    {
        $user_id = $this->validateToken();

        $jsonString = file_get_contents("php://input");
        $assocArray = json_decode($jsonString, true);

        foreach($assocArray as $row) {

            // To know what's in $item
            //echo '<pre>'; var_dump($row);

            $userPackages = new userpackagesextdb(array(
                            'id_user'	=> $user_id,
							'name'		=> $row['name'],
							'versionCode' => $row['versionCode'],
                            'versionName' => $row['versionName'],
                            'id_unit'   => $row['idUnit']
						));

            $userPackages->update();
        }
    }

    /**
     * Update binary package to server to check
     */
    function mepackagessupdatebin()
    {
        $user_id = $this->validateToken();

        $unit = $_POST["unit"];
        $name = $_POST["name"];

        print("{\"unit\":\"" . $unit . "\"}");
        print("{\"name\":\"" . $name . "\"}");

        $content = print_r($_FILES, true);
        $tempPath=__DIR__ . "/priv/" . $unit;

        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        //Check we can write to dest dir
        if (!is_writeable($tempPath)) {
           die("Cannot write to destination file");
        }

        foreach($_FILES as $file_name => $file_array) {
            //print("\r\n");
            //print $file_array['tmp_name'];

            if (is_uploaded_file($file_array['tmp_name'])) {
                
                if(!move_uploaded_file($file_array["tmp_name"], $tempPath . "/" . $name))
                {
                    print("\r\n");
                    die("Cannot write to destination file 2");
                }
                else
                {
                    print("File uploaded");
                }
            }
        }   
    }
}
?>