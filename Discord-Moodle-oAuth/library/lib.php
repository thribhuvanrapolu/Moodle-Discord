<?php
  

const PASSPHRASE = ''; // use 'openssl rand -hex 32' to generate key, same with python


function encrypt(string $data): string
{
    $data_json_64 = base64_encode(json_encode($data));
    $secret_key = hex2bin(PASSPHRASE);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-gcm'));
    $tag = '';
    $encrypted_64 = openssl_encrypt($data_json_64, 'aes-256-gcm', $secret_key, 0, $iv, $tag);
    $json = new stdClass();
    $json->iv = base64_encode($iv);
    $json->data = $encrypted_64;
    $json->tag = base64_encode($tag);
    return base64_encode(json_encode($json));
}

function decrypt(string $data): string
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

function getmoodle_id_name($data){
    return substr(decrypt($data), 0, strpos(decrypt($data),"--discord--"));
}

//sql functions

function create_row($id,$user,$encrypt_state){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname="discord_moodle";

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // echo "Connected successfully";

      $sql = "INSERT INTO data (moodle_id, moodle_username,encrypt_state)
      VALUES (:id,:user,:encrypt_state) ON DUPLICATE KEY UPDATE encrypt_state=:encrypt_state";

      $sth = $conn->prepare($sql);

      $sth->bindParam(':id', $id);
      $sth->bindParam(':user', $user);
      $sth->bindParam(':encrypt_state', $encrypt_state);

      $sth->execute();

    } catch(PDOException $e) {
      // echo "Connection failed: " . $e->getMessage();
      echo "Connection failed: ";
    }

    $conn = null;

}

function save_discord_id_username($data,$discord_id){
  $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname="discord_moodle";

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // echo "Connected successfully";
      
      $id=substr(decrypt($data), 0, strpos(decrypt($data),"-"));


      $sql="UPDATE `data` SET `discord_id_username` = :enc_data, `encrypt_state` = 'OFF',`encrypt_discord_id`=:discord_id WHERE `data`.`moodle_id` = :id";



      $sth = $conn->prepare($sql);

      $sth->bindParam(':enc_data', $data);
      $sth->bindParam(':discord_id', $discord_id);
      $sth->bindParam(':id', $id);

      $sth->execute();

    } catch(PDOException $e) {
      // echo "Connection failed: " . $e->getMessage();
      echo "Connection failed: ";
    }

    $conn = null;

}


function check_encrypt_state($data){
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname="discord_moodle";


  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";

    $sql = "SELECT * FROM data WHERE encrypt_state=:enc_data";


    $sth = $conn->prepare($sql);

    $sth->bindParam(':enc_data', $data);

    $sth->execute();
    
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    if(! $row){
      return False; 
    }
    else{
      return True;
    }
    

  } catch(PDOException $e) {
    // echo "Connection failed: " . $e->getMessage();
    echo "Connection failed: ";
  }

  $conn = null;
}

function remove_discord_info($id){
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname="discord_moodle";

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";

    $sql="UPDATE `data` SET `discord_id_username` = NULL, `encrypt_state` = 'OFF',`encrypt_discord_id`=NULL WHERE `data`.`moodle_id` = :id";

    $sth = $conn->prepare($sql);

    $sth->bindParam(':id', $id);

    $sth->execute();

  } catch(PDOException $e) {
    // echo "Connection failed: " . $e->getMessage();
    echo "Connection failed: ";
  }
  
  $conn = null;

}

function encrypt_discord_id($data){
  
  $ciphering="AES-256-CTR";
  $option=0;
  $encryption_iv='1234567890123456';
  $encryption_key=PASSPHRASE;

  return openssl_encrypt(substr(decrypt($data),strpos(decrypt($data),"--discord--")+11,strpos(decrypt($data),"--discord--")+11+20),$ciphering,$encryption_key,$option,$encryption_iv);
}

function check_discord_id($data){
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname="discord_moodle";


  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";

    $sql = "SELECT * FROM data WHERE encrypt_discord_id=:id";

    $sth = $conn->prepare($sql);
    
    $sth->bindParam(':id', $data);

    $sth->execute();

    $row = $sth->fetch(PDO::FETCH_ASSOC);


    if(! $row){
      return False; 
    }
    else{
      return True;
    }

  } catch(PDOException $e) {
    // echo "Connection failed: " . $e->getMessage();
    echo "Connection failed: ";
  }
  $conn = null;
}

function display_moodle_username($data){
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname="discord_moodle";

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";

    $sql = "SELECT * FROM data WHERE encrypt_discord_id=:id";

    $sth = $conn->prepare($sql);
    
    $sth->bindParam(':id', $data);

    $sth->execute();

    $row=$sth->setFetchMode(PDO::FETCH_ASSOC);

    $result = $sth->fetchColumn(1);

    
    return $result;


  } catch(PDOException $e) {
    // echo "Connection failed: " . $e->getMessage();
    echo "Connection failed: ";
  }
  $conn = null;

}

function off_encrypt_state($data,$id){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname="discord_moodle";

    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // echo "Connected successfully";
      
      $sql="UPDATE `data` SET `encrypt_state` = 'OFF' WHERE `data`.`moodle_id` = :id";



      $sth = $conn->prepare($sql);

      $sth->bindParam(':id', $id);

      $sth->execute();

    } catch(PDOException $e) {
      // echo "Connection failed: " . $e->getMessage();
      echo "Connection failed: ";

    }
    $conn = null;

}

?>
