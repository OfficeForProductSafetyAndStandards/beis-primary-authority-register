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
    
    apps_req = urllib2.Request('https://' + os.environ['CF_ENDPOINT'] + '/v2/apps')
    apps_req.add_header('Authorization', 'Bearer ' + access_token)

    apps_resp = urllib2.urlopen(apps_req)
    apps_content = json.load(apps_resp)
    
    resources = apps_content['resources']
    
    for resource in resources:
        if resource['entity']['name'] == 'par-beta-production':
            guid = resource['metadata']['guid']
            break
            
    print guid
        
    stats_req = urllib2.Request('https://' + os.environ['CF_ENDPOINT'] + '/v2/apps/' + guid + '/stats')
    stats_req.add_header('Authorization', 'Bearer ' + access_token)

    stats_resp = urllib2.urlopen(stats_req)
    stats_content = json.load(stats_resp)
    
    def publish_callback(result, status):
        pass
        print "Published"
        # Handle PNPublishResult and PNStatus

    print "Publishing..."
    pubnub.publish().channel('cloud_foundry_8').message(stats_content).async(publish_callback)
    
    sleep(5)