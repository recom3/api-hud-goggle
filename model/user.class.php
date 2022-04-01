<?php

/**
 * Model for user.
 *
 * This file hold the user class.
 *
 * @version 1.0
 * @author recom3
 */
class UserData implements JsonSerializable
{

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}