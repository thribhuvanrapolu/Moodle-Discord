<?php
    
    
    function check_already_login($id){        
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname="discord_moodle";

        // Create connection
        $conn = new mysqli($servername,$username,$password,$dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // echo "hi";
        // echo($id);

        $sql = "SELECT * FROM data WHERE moodle_id=$id LIMIT 1";
        if($result=$conn->query($sql)){
            $res=$result->fetch_assoc();    
        }


        if(decrypt($res["discord_id_username"])==""){
            return "False";
        }

        else{
            return substr(decrypt($res["discord_id_username"]),strpos(decrypt($res["discord_id_username"]),"--discord--")+11+20,strlen(decrypt($res["discord_id_username"])));
        }

        $conn->close();
    }

    const PASSPHRASE = 'dda417608d4bc1dcfcf34fe35795ac5fbf20972341da5b20c78dd71c39552946'; // use 'openssl rand -hex 32' to generate key, same with python
    
    function decrypt($data): string
    {
        $secret_key = hex2bin(PASSPHRASE);
        $json = json_decode(base64_decode($data), true);
        $iv = base64_decode($json['iv']);
        $tag = base64_decode($json['tag']);
        $encrypted_data = base64_decode($json['data']);
        $decrypted_data = openssl_decrypt($encrypted_data, 'aes-256-gcm', $secret_key, OPENSSL_RAW_DATA, $iv, $tag);
        try{
            return json_decode(base64_decode($decrypted_data),True);
        }
        catch(TypeError){
            return "";
        }
    }   
?>