# Run this command in the directory above the repo and Have a copy of your .env file in that directory
#
# sh beis-primary-authority-register/vagrant.sh
#

# Into the repo
cd beis-primary-authority-register

# Save a copy of the .env file for later
cp .env ..

# Destroy
vagrant destroy

# Belt and braces, kill the process listening on port 8111, if any
sudo lsof -ti:8111 | xargs kill

# Up above the repo for the deletion
cd ..
rm -rf beis-primary-authority-register

# Clone the repo
git clone git@github.com:UKGovernmentBEIS/beis-primary-authority-register

# Into the repo
cd beis-primary-authority-register

# Get the .env file we saved earlier
cp ../.env .

# Compose and fix permissions
php composer.phar install
chmod -R 777 web/modules/contrib

# Bring up the VM
vagrant up

