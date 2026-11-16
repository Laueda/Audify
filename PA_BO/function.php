<?php

function logAction($conn, $user_id, $action, $page) {
    
    $ip = $_SERVER['REMOTE_ADDR'];

    $sql = "INSERT INTO LOG (user_id, action, page, ip, date) 
            VALUES ('$user_id', '$action', '$page', '$ip', NOW())";

    $conn->query($sql);
}

?>
 

