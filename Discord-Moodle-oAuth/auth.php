<?php
    require __DIR__ . '/../../config.php';
    require_login();

    require __DIR__ . "/library/lib.php";

    // echo(getmoodle_id_name($_GET['state'])==$USER->id."-".$USER->username);
    // echo(check_encrypt_state($_GET['encrypt_state']));

    if(getmoodle_id_name($_GET['state'])==$USER->id."-".$USER->username && check_encrypt_state($_GET['encrypt_state']) && decrypt($_GET['encrypt_state'])==$USER->id."-".$USER->username){
    
        $_SESSION['discord_id']=encrypt_discord_id($_GET['state']);

        
        
        if(check_discord_id($_SESSION['discord_id'])){
            
            echo"Sorry!You cant link more than one discord account. This discord account is already linked with username:        ";
            echo " <a href=http://localhost/moodle>CLICK HERE TO REDIRECT TO MOODLE HOMEPAGE</a>";

            echo(display_moodle_username($_SESSION['discord_id']));
            off_encrypt_state($_GET['encrypt_state'],$USER->id);


        }   
        else{

            save_discord_id_username($_GET['state'],$_SESSION['discord_id']);
            echo "SUCCESSFULLY LINKED YOUR MOODLE ACCOUNT WITH DISCORD";
            echo " <a href=http://localhost/moodle>CLICK HERE TO REDIRECT TO MOODLE HOMEPAGE</a>";

        }
    }

    else{
        echo "Uh-oh Something went wrong please go back to euclid page and try again";
        echo " <a href=http://localhost/moodle>CLICK HERE TO REDIRECT TO MOODLE HOMEPAGE</a>";
    }

?>