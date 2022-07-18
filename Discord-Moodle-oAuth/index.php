<?php
    require __DIR__ . '/../../config.php';
    require_login();

    require __DIR__ . "/library/lib.php";
    
    $_SESSION['state']=encrypt($USER->id."-".$USER->username);

    create_row($USER->id,$USER->username,$_SESSION['state']);

    $url="";
    header("Location: $url".'?state='.$_SESSION['state']);

?>