# Moodle-Discord

Link Discord account with moodle account 

Helpful for setting up discord as communication medium in university which prevents unauthorized entry

Built using Python,PHP and uses sql-database


## How to test it

1. Setup moodle on your system
2. Move Discord-Moodle-oAuth folder to moodle/my folder
3. Remove simplehtml from moodle/blocks
4. Move simplehtml folder from this repo to moodle/blocks

#### SQL-Database
5. Create database discord_moodle and table data with 5 columns
6. Create column moodle_id,moodle_username,encrypt_state,discord_id_username,encrypt_discord_id
7. All are longtext,nullable except moodle_id
8. Make moodle_id unique and int

#### Python-code
9. Install prequisites::    pip install flask requests-oauthlib         
10. Update client_id,client_secret,redirect_uri,token_url,authorize_url in discord_oauth.py
11. Run discord_oauth.py
12. Copy the link that you see when you run discord_oauth.py and paste it in index.php line 11 $url

#### Library
13. Update lib.php,lib.py in Discord-Moodle-oAuth/library folder and lib.php in simplehtml/library with your sql_database info
14. Run 'openssl rand -hex 32' in terminal and paste the key in the above mentioned library files (PASSPHRASE)

#### Moodle-Homepage
13. Go to moodle dashboard and add simplehtml block


## How it works
Data stored in sql is encrypted with AES-256-GCM except moodle_id and moodle_username
 

Here is the flowchart of the algorithm
https://cloud.smartdraw.com/share.aspx/?pubDocShare=219DFC2D40C8490735B494DAB92F9815CDC




https://user-images.githubusercontent.com/93434497/179462026-64f3d8b8-190a-44e5-b5ee-43228d75c419.mp4

