<?php

include "../connect.php" ;

$id = filterRequest("id") ; 


deleteData("wholesale_customers" , "wholesale_customers_id = $id ") ; 