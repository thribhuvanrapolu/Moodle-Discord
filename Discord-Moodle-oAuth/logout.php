<?php
    require __DIR__ . '/../../config.php';
    require_login();

    require __DIR__ . "/library/lib.php";

    // if(check_user_id($USER->id)){
    //     remove_discord_info($USER->id);
    // }

    echo "DISCORD ACCOUNT LOGOUT SUCCESSFUL ";
    echo " <a href=http://localhost/moodle>CLICK HERE TO REDIRECT TO MOODLE HOMEPAGE</a>";

    remove_discord_info($USER->id);

?>