<?php

include '../connect.php';

$table = "categories";

$id = filterRequest("categories_id");

$name = filterRequest("categories_name");

$image = filterRequest("categories_image");

$date  = filterRequest("categories_date");


    $data = array(
        "categories_name"    => $name,
        "categories_image" => $image,
        "categories_date"   => $date,
    );

updateData($table, $data, "categories_id = $id");
