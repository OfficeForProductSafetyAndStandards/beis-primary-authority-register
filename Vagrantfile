$script = <<SCRIPT
echo I am provisioning...
date > /etc/vagrant_provision_start

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

date > /etc/vagrant_provision_end
SCRIPT

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/xenial64"
  config.vm.network "private_network", ip: "192.168.82.68"
  config.vm.network "forwarded_port", guest: 8111, host: 8111
  config.vm.network "forwarded_port", guest: 5411, host: 5411

  host_user_id = Process.uid
  host_group_id = Process.gid

  if (/darwin/ =~ RUBY_PLATFORM) != nil
    config.vm.synced_folder ".", "/vagrant", nfs: true, :bsd__nfs_options => ["mapall=#{host_user_id}:#{host_group_id}"]
  else
    config.vm.synced_folder ".", "/vagrant", nfs: true, :linux__nfs_options => ["no_root_squash"]
  end

  config.vm.provision "shell", inline: $script
  config.vm.provision :shell, :inline => "sudo rm /etc/localtime && sudo ln -s /usr/share/zoneinfo/Europe/London /etc/localtime", run: "always"

  config.vm.provider "virtualbox" do |v|
    v.memory = 3096
    v.cpus = 4
  end
end
