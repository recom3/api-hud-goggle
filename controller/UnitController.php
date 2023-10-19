<?php

/**
 * Unit controller module.
 *
 * This file holds the operations related to units.
 *
 * @version 1.0
 * @author recom3
 */
class UnitController extends BaseController
{
    public function reconnunitss()
    {
        $sofwareVersion = (object)array(
        "recon_product_id" => "3.2.1",
        "display_name" => "snow2",
        "svn" => "111"
        );

        $unit = (object)array(
        "serial_number" => "283543590",
        "brand" => "oakley",
        "model" => "airwave",
        "software_version" => $sofwareVersion
        );

        echo json_encode($unit);
    }

    public function meereconnunitss()
    {
        $result = array();

        $sofwareVersion = (object)array(
        "recon_product_id" => "283543590",
        "display_name" => "oakley",
        "svn" => "airwave"
        );

        $unit = (object)array(
        "serial_number" => "283543590",
        "brand" => "oakley",
        "model" => "airwave",
        "software_version" => $sofwareVersion
        );

        $result[] = $unit;

        $out = array_values($result);
        echo json_encode($out);
    }
}
?>