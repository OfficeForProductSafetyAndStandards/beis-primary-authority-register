cf ssh par-beta-staging -c "cd app/tools/partnership_report && python partnership_report.py" > ./partnership_report.csv
php partnership_report.php
cf push --hostname par-beta-csv-green par-beta-csv-green
cf map-route par-beta-csv-green cloudapps.digital -n par-beta-csv
cf unmap-route par-beta-csv cloudapps.digital -n par-beta-csv
cf delete par-beta-csv -f
cf rename par-beta-csv-green par-beta-csv

