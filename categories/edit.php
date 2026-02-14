<?php

include '../connect.php';

$table = "categories";

$id = filterRequest("id");

$name = filterRequest("name");

$date  = filterRequest("categories_date");

    $data = array(
        "categories_name" => $name,
        "categories_date"   => $date,
    );

updateData($table, $data, "categories_id = $id");
