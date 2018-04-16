docker pull owasp/zap2docker-stable
docker run -v $(pwd):/zap/wrk/:rw -t owasp/zap2docker-stable zap-baseline.py -t http://192.168.82.68:8111 -g gen.conf -r testreport.html
