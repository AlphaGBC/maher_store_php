<?php 

include '../connect.php';

$msgError = array()  ;

$table = "categories";

$name = filterRequest("name");


$imagename = imageUpload( "../Upload/categories" , "files") ;

$data = array( 
"categories_name" => $name,
"categories_image" => $imagename,
);

sendFCM("تنبيه", "تم اضافة قسم جديد","point", "", "refreshcategories" , $accessToken);

insertData($table , $data);