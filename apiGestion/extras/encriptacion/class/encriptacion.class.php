<?php

class Encriptacion{


    public static function hash($password){
        $pass = password_hash($password, PASSWORD_BCRYPT, array('cost'=>12));   

       return $pass; 
    }






}


?>