# Run this command in the directory above the repo and Have a copy of your .env file in that directory
#
# sh <repo directory>/vagrant.sh [branch]
#

command -v php >/dev/null 2>&1 || { 
    echo "################################################################################################"
    echo >&2 "PHP executable not found"
    echo "################################################################################################"
    exit 1 
}

command -v vagrant >/dev/null 2>&1 || { 
    echo "################################################################################################"
    echo >&2 "Vagrant executable not found"
    echo "################################################################################################"
    exit 1 
}

command -v git >/dev/null 2>&1 || { 
    echo "################################################################################################"
    echo >&2 "Git executable not found"
    echo "################################################################################################"
    exit 1 
}

IFS="/" read -ra REPO_DIR <<< "$0"

# Into the repo
cd $REPO_DIR

# Save a copy of the .env file for later
cp .env ..

# Destroy
vagrant destroy

# Belt and braces, kill the process listening on port 8111, if any
sudo lsof -ti:8111 | xargs kill

# Up above the repo for the deletion
cd ..
rm -rf $REPO_DIR

# Clone the repo
git clone git@github.com:UKGovernmentBEIS/beis-primary-authority-register $REPO_DIR

# Into the repo
cd $REPO_DIR

# Checkout the required branch, or default to master if none specified
if [ $# -eq 1 ]; then git checkout $1; fi

# Get the .env file we saved earlier
cp ../.env .

# Compose and fix permissions
php composer.phar install
chmod -R 777 web/modules/contrib

# Bring up the VM
vagrant up

