# Have a copy of your .env file in the directory directly above the cloned repo

cd ..
rm -rf beis-primary-authority-register
git clone git@github.com:UKGovernmentBEIS/beis-primary-authority-register
cd beis-primary-authority-register
cp ../.env .
php composer.phar install
chmod -R 777 web/modules/contrib
vagrant up


