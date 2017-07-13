docker pull owasp/zap2docker-stable
docker run -t owasp/zap2docker-stable zap-baseline.py -t [url] -r testreport.html



git rm tests/pa11y/Dockerfile
git rm tests/pa11y/docker/README.md
git rm tests/pa11y/docker/compose/docker-compose.yml
git rm tests/pa11y/docker/pa11y-dashboard/Dockerfile
git rm tests/pa11y/docker/pa11y-dashboard/Makefile
git rm tests/pa11y/docker/pa11y-dashboard/docker-entrypoint.sh
git rm tests/reports/html/.gitignore
