$script = <<SCRIPT
echo I am provisioning...
cd /vagrant/docker
sudo docker-compose up -d
date > /etc/vagrant_provisioned_at
SCRIPT

Vagrant.configure("2") do |config|
  config.vm.box = "netsensia/parbeta"
  config.vm.box_version = "1.3.0"
  config.vm.network "private_network", ip: "192.168.82.68"
  config.vm.network "forwarded_port", guest: 8111, host: 8111
  config.vm.synced_folder ".", "/vagrant", nfs: true
  config.vm.provision "shell", inline: $script
end

