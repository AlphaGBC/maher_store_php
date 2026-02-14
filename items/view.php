<?php

include "../connect.php";

$categoriesid     = filterRequest("categoriesid");


getAllData("itemsview" , "items_categories = $categoriesid") ;

?>
