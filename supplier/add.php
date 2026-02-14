<?php 

include '../connect.php';

$msgError = array()  ;

$table = "supplier";

$name = filterRequest("name");

$data = array( 
"supplier_name" => $name,
);



insertData($table , $data);