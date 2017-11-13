import os
import urllib2
from pubnub.pnconfiguration import PNConfiguration
from pubnub.pubnub import PubNub
from time import sleep

for i in range(1000):
    pnconfig = PNConfiguration()
    pnconfig.subscribe_key = os.environ['BEIS_PAR_PUBNUB_SUBSCRIBE_KEY']
    pnconfig.publish_key = os.environ['BEIS_PAR_PUBNUB_PUBLISH_KEY']
    pnconfig.ssl = False

    pubnub = PubNub(pnconfig)

    req = urllib2.Request('https://api.cloud.service.gov.uk/v2/apps/' + os.environ['BEIS_PAR_CF_APP_KEY'] + '/stats')
    req.add_header('Authorization', os.environ['BEIS_PAR_CF_API_AUTH_TOKEN'])

    resp = urllib2.urlopen(req)
    content = resp.read()

    def publish_callback(result, status):
        pass
        # Handle PNPublishResult and PNStatus

    pubnub.publish().channel('cloud_foundry_6').message(content).async(publish_callback)

    print content
    
    file = open("/tmp/tmp.json", "w")
    file.write(content)

    sleep(60)