docker pull owasp/zap2docker-stable
docker run -u zap -p 8080:8080 -i owasp/zap2docker-stable zap.sh -daemon -host 0.0.0.0 -port 8080 &&
docker run -t owasp/zap2docker-stable zap-baseline.py -t http://localhost:8111