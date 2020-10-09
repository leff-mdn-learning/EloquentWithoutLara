<?php


namespace Controllers;


use Models\User;

class Users
{
    public static function create_user($username, $email, $password): User
    {
        $user = User::create(['username'=>$username,'email'=>$email,'password'=>$password]);
        return $user;
    }

}