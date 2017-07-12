docker pull owasp/zap2docker-stable
docker run -t owasp/zap2docker-weekly zap-baseline.py -t http://localhost:80
