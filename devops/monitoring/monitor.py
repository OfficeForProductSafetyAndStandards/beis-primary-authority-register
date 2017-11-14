import os
import urllib
import urllib2
import json
import sys
from pubnub.pnconfiguration import PNConfiguration
from pubnub.pubnub import PubNub
from time import sleep

while True:
    pnconfig = PNConfiguration()
    pnconfig.subscribe_key = os.environ['PUBNUB_SUBSCRIBE_KEY']
    pnconfig.publish_key = os.environ['PUBNUB_PUBLISH_KEY']
    pnconfig.ssl = False

    pubnub = PubNub(pnconfig)

    print "Requesting token..."
    token_data = urllib.urlencode({'username'   : os.environ['CF_LOGIN_EMAIL'],
                                   'password'   : os.environ['CF_LOGIN_PASSWORD'],
                                   'grant_type' : 'password',
                                   'scopes'     : '*'})
                         
    token_req = urllib2.Request('https://' + os.environ['CF_LOGIN_ENDPOINT'] + '/oauth/token', token_data)
    token_req.add_header('Authorization', 'Basic Y2Y6')

    token_resp = urllib2.urlopen(token_req)
    token_content = json.load(token_resp)
    
    access_token = token_content['access_token']
    refresh_token = token_content['refresh_token']
    
    stats_req = urllib2.Request('https://' + os.environ['CF_ENDPOINT'] + '/v2/apps/' + os.environ['CF_APP_KEY'] + '/stats')
    stats_req.add_header('Authorization', 'Bearer ' + access_token)

    stats_resp = urllib2.urlopen(stats_req)
    body = json.load(stats_resp)
    
    def publish_callback(result, status):
        pass
        print "Published"
        # Handle PNPublishResult and PNStatus

    print "Publishing..."
    pubnub.publish().channel('cloud_foundry_8').message(body).async(publish_callback)
    
    sleep(5)