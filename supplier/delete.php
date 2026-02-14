<?php

include "../connect.php" ;

$id = filterRequest("id") ; 


deleteData("supplier" , "supplier_id = $id ") ; 