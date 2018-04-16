docker pull owasp/zap2docker-stable
docker run -t owasp/zap2docker-stable zap-baseline.py -t http://localhost:8111
