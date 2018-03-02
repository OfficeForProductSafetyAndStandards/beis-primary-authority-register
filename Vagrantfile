$script = <<SCRIPT
cd /vagrant/devops/docker
sudo docker-compose up -d

sh setup.sh
SCRIPT

Vagrant::DEFAULT_SERVER_URL.replace('https://vagrantcloud.com')

Vagrant.configure("2") do |config|
  config.vm.box = "netsensia/xenial64"
  config.vm.box_version = "1.0.0"
  config.vm.network "private_network", ip: "192.168.82.68"
  config.vm.network "forwarded_port", guest: 8111, host: 8111
  config.vm.network "forwarded_port", guest: 5411, host: 5411

  config.ssh.username = "ubuntu"
  config.ssh.password = "vagrant"

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
    v.customize [ "modifyvm", :id, "--uartmode1", "disconnected" ]
  end
end
