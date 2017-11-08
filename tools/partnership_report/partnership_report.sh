if [ ! -f Staticfile.auth ]; then
    echo "Staticfile.auth not found"
    exit;
fi
cf login -a $GOVUK_CF_ENDPOINT -u $GOVUK_CF_USER -p $GOVUK_CF_PASSWORD
cf target -o beis-nmo-trial -s sandbox
cf ssh par-beta-$1 -c "cd app/tools/partnership_report && python partnership_report.py" > ./partnership_report.csv
php partnership_report.php
cf push --hostname par-beta-csv-green par-beta-csv-green
cf map-route par-beta-csv-green cloudapps.digital -n par-beta-csv
cf unmap-route par-beta-csv cloudapps.digital -n par-beta-csv
cf delete par-beta-csv -f
cf rename par-beta-csv-green par-beta-csv

