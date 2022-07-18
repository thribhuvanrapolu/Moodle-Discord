import os
from requests_oauthlib import OAuth2Session
from flask import Flask, request, redirect, session
from flask import session, url_for

from library.lib import *



# Disable SSL requirement
os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = '1'
    
# Settings for your app
base_discord_api_url = 'https://discordapp.com/api'
client_id = r'' # Get from https://discordapp.com/developers/applications
client_secret = r''
redirect_uri='https://example.com:8000/oauth_callback'
scope = ['identify','email']
token_url = 'https://discordapp.com/api/oauth2/token'
authorize_url = 'https://discordapp.com/api/oauth2/authorize'
app = Flask(__name__)
app.secret_key = os.urandom(24)

from logging import FileHandler, WARNING

if not app.debug:
    error_file_handler=FileHandler('errorlog.txt')
    error_file_handler.setLevel(WARNING)
    app.logger.addHandler(error_file_handler)


@app.route("/")
def home():
    encrypt_state=request.args.get('state')
    session['encrypt_state']=encrypt_state
    
    #check if the encrypt_state exists in sql database
    if(sql_search(session['encrypt_state'])):
        #create discord_oauth link
        oauth = OAuth2Session(client_id,state=session['encrypt_state'],redirect_uri=redirect_uri, scope=scope)
        login_url, state = oauth.authorization_url(authorize_url)

        session['state'] = state

        return redirect(login_url)

    else:
        return redirect('http://localhost/moodle/my/Discord-Moodle-oAuth/fail.php')


@app.route("/oauth_callback")
def oauth_callback():

    discord = OAuth2Session(client_id, redirect_uri=redirect_uri, state=session['state'], scope=scope)
    
    token = discord.fetch_token(
        token_url,
        client_secret=client_secret,
        authorization_response=request.url,
    )

    session['discord_token'] = token
    
    discord = OAuth2Session(client_id, token=session['discord_token'])
    response = discord.get(base_discord_api_url + '/users/@me')
    if(sql_search(session['encrypt_state'])):
        session['encrypt_discord']=encrypt(decrypt(session['encrypt_state'])+"--discord--"+str(response.json()['id'])+"--"+str(response.json()['username'])+"#"+str(response.json()['discriminator']))
        return redirect("http://localhost/moodle/my/Discord-Moodle-oAuth/auth.php"+'?state='+session['encrypt_discord']+"&encrypt_state="+session['encrypt_state'])
    else:
        return redirect('http://localhost/moodle/my/Discord-Moodle-oAuth/fail.php')
        



# Or run like this
# FLASK_APP=discord_oauth_login_server.py flask run -h 0.0.0.0 -p 8000
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8000)
