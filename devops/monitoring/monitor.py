import os
import urllib2
import json

req = urllib2.Request('https://api.cloud.service.gov.uk/v2/apps/' + os.environ['BEIS_CF_APP_KEY'] + '/stats')
req.add_header('Authorization', 'bearer ' + os.environ['BEIS_CF_API_TOKEN'])

resp = urllib2.urlopen(req)
content = resp.read()

print content
