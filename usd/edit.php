<?php

include '../connect.php';

$table = "usd";

$id     = "1";
$price      = filterRequest("price");

    $data = array( 
    "usd_price"        => $price,
        );


sendFCM("تنبيه", "تم تحديث سعر صرف الدولار","point", "", "refreshusd" , $accessToken);

updateData($table, $data, "usd_id = $id");
