<?php 

include '../connect.php';

$msgError = array()  ;

$table = "wholesale_customers";

$name = filterRequest("name");

$data = array( 
"wholesale_customers_name" => $name,
);

sendFCM("تنبيه", "تم اضافة عميل جملة جديد","point", "", "refreshwholesale" , $accessToken);


insertData($table , $data);