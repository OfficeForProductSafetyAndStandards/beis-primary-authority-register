$script = <<SCRIPT
echo I am provisioning...

curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -    
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"    
sudo apt-get update -y   
apt-cache policy docker-ce    
sudo apt-get install -y docker-ce
sudo apt-get install -y docker-compose
sudo adduser ubuntu docker
newgrp docker

cd /vagrant/docker
sudo docker-compose up -d

sh setup.sh
    
date > /etc/vagrant_provisioned_at
SCRIPT

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/xenial64"
  config.vm.network "private_network", ip: "192.168.82.68"
  config.vm.network "forwarded_port", guest: 8111, host: 8111
  
  config.vm.synced_folder ".", "/vagrant", type: "nfs"
  config.vm.provision "shell", inline: $script

end

