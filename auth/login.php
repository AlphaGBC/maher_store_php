<?php

include "../connect.php";
 
$password = filterRequest("password"); 

$email = filterRequest("email"); 

getData("admin" , "admin_email = ? AND  admin_password = ?" , array($email , $password)) ; 
