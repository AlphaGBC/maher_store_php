<?php
include '../connect.php';

$table = "items";

// جلب البيانات من الطلب
$name      = filterRequest("name");
$storehousecount     = filterRequest("storehousecount");
$pointofsale1count     = filterRequest("pointofsale1count");
$pointofsale2count     = filterRequest("pointofsale2count");
$costprice     = filterRequest("costprice");
$wholesaleprice     = filterRequest("wholesaleprice");
$retailprice     = filterRequest("retailprice");
$wholesalediscount  = filterRequest("wholesalediscount");
$retaildiscount  = filterRequest("retaildiscount");
$itemsqr    = filterRequest("itemsqr");
$Catid    = filterRequest("items_categories");




$data = [
    "items_name"        => $name,
    "items_storehouse_count"     => $storehousecount,
    "items_pointofsale1_count"     => $pointofsale1count,
    "items_pointofsale2_count"       => $pointofsale2count,
    "items_cost_price"       => $costprice,
    "items_wholesale_price"       => $wholesaleprice,
    "items_retail_price"    => $retailprice,
    "items_wholesale_discount"      => $wholesalediscount,
    "items_retail_discount"    => $retaildiscount,
    "items_qr"    => $itemsqr,
    "items_categories"    => $Catid,
];


sendFCM("تنبيه", "تم اضافة منتج جديد","point", "", "refreshitems" , $accessToken);

insertData($table, $data);
