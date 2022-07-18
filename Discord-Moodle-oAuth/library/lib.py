#sql
import mysql.connector
from sqlalchemy import true

#encrypt-decrypt
import base64
import binascii
import json
from Crypto import Random
from Crypto.Cipher import AES

def sql_search(encrypt_state):

    mydb = mysql.connector.connect(
      host="localhost",
      user="root",
      password="",
      database="discord_moodle",
    )

    # mycursor = mydb.cursor()

    mycursor = mydb.cursor(prepared=True)

    sql="""SELECT * FROM data WHERE encrypt_state= %s"""
    # state=(encrypt_state,1)

    mycursor.execute(sql,(encrypt_state,))

    myresult = mycursor.fetchall()


    if(myresult==[]):
        return False
    else:
        x=str(myresult[0][0])+"-"+str(myresult[0][1])
        if(decrypt(encrypt_state)==x):
            return True
        else:
            return False




PASSPHRASE = b'';  # use 'openssl rand -hex 32' to generate key, same with PHP. don't forget to cast to bytes

def encrypt(data: str) -> str:
    global PASSPHRASE
    data_json_64 = base64.b64encode(json.dumps(data).encode('ascii'))
    try:
        key = binascii.unhexlify(PASSPHRASE)
        iv = Random.get_random_bytes(AES.block_size)
        cipher = AES.new(key, AES.MODE_GCM, iv)
        encrypted, tag = cipher.encrypt_and_digest(data_json_64)
        encrypted_64 = base64.b64encode(encrypted).decode('ascii')
        iv_64 = base64.b64encode(iv).decode('ascii')
        tag_64 = base64.b64encode(tag).decode('ascii')
        json_data = {'iv': iv_64, 'data': encrypted_64, 'tag': tag_64}
        return base64.b64encode(json.dumps(json_data).encode('ascii')).decode('ascii')
    except:  # noqa
        return ''


def decrypt(data: str) -> str:
    global PASSPHRASE
    try:
        key = binascii.unhexlify(PASSPHRASE)
        encrypted = json.loads(base64.b64decode(data).decode('ascii'))
        encrypted_data = base64.b64decode(encrypted['data'])
        iv = base64.b64decode(encrypted['iv'])
        tag = base64.b64decode(encrypted['tag'])
        cipher = AES.new(key, AES.MODE_GCM, iv)
        decrypted = cipher.decrypt_and_verify(encrypted_data, tag)
        return json.loads(base64.b64decode(decrypted).decode('ascii'))
    except:  # noqa
        return {}
