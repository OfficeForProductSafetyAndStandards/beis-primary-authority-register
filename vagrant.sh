# Run this command in the directory above the repo and Have a copy of your .env file in that directory
#
# sh beis-primary-authority-register/vagrant.sh
#

vagrant destroy
sudo lsof -ti:8111 | xargs kill
rm -rf beis-primary-authority-register
git clone git@github.com:UKGovernmentBEIS/beis-primary-authority-register
cd beis-primary-authority-register
cp ../.env .
php composer.phar install
chmod -R 777 web/modules/contrib
vagrant up


