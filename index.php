<?php
require_once "Database.php";
$id = 4;
//$users = Database::getInstance()->query("SELECT * FROM users WHERE email IN (?)",['Shoev']);
//$users = Database::getInstance()->get('users',['password','=','123']);
//$users = Database::getInstance()->delete('users',['email','=','shoev']);
//Database::getInstance()->insert('users',["email"=>"shoev-03","password"=>"4321"]);
//Database::getInstance()->update('users',$id, ["email"=>"string", "password" =>"1242"]);

$users = Database::getInstance()->get('users',['email','=','str']);

echo $users->first()->email;


die();
if ($users->error())
{
    echo "error";
}else
{
    foreach ($users->result() as $user)
    {
        echo $user->email . "<br>";
    }
}



