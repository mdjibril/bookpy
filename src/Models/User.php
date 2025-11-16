<?php

namespace App\Models;

class User
{
    private $id;
    private $name;
    private $email;
    private $password;

    public function __construct($name, $email, $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}