<?php

include "../connect.php" ;

$id = filterRequest("id") ; 


deleteData("items" , "items_id = $id ") ; 