<?php

namespace App\Libraries;

class Hash
{
    public static function make($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function check($entered_password, $db_password_hash)
    {
        if (password_verify($entered_password, $db_password_hash)) {
            return true;
        } else {
            return false;
        }
    }
    public static function display_error($validation, $field)
    {
        if ($validation->hasError($field)) {
            return $validation->getError($field);
        } else {
            return false;
        }
    }
}
