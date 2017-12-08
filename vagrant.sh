cd ..
rm -rf beis-primary-authority-register
git clone git@github.com:UKGovernmentBEIS/beis-primary-authority-register
cd beis-primary-authority-register
php composer.phar install
chmod -R 777 web/modules/contrib
vagrant up


