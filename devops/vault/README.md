# HOWTO: Installing Vault On AWS Linux
This is quick *howto* for installing [vault](https://github.com/hashicorp/vault) on AWS Linux, mostly to remind myself. At the end of this tutorial,  you'll have a working vault server, using s3 for the backend, self signed certificates for tls, and supervisord to ensure that the vault server is always running, and starts on reboot.


## Setting up S3
First things first, let's set up an s3 bucket to use as the storage backend for our s3 instance.
1. From the AWS Mangement Console, go to the S3 console.

2. Click on the `Create Bucket` button

3. Name it something


## IAM Stuff
Next, let's create an IAM Policy with full access to our newly created bucket. We'll also create an IAM Role and IAM User in this step, but this should not be neccessary once Vault v5 is released.
1. From the AWS Management Console, go the IAM console.

2. Click on **Policies** in the sidenav

3. Click on **Create Policy**

4. Select the **Policy Generator** option, because it's easy.

5. Select **Amazon S3** from the *AWS Service* dropdown

6. Select **All Actions (*)** from the *Actions* dropdown

7. Enter the **Amazon Resource Name**: `arn:aws:s3:::<your_bucket_name>`

8. Click **Add Statement**

9. Next, repeat steps 5-8, except use the following **ARN**: `arn:aws:s3:::<your_bucket_name>/*` *(this is required to let vault manage all keys within the bucket)*

10. Click **Next Step**

11. Give the policy a name: `s3-vault-full-access`

12. Click `Add Policy`

Next, we create an **IAM Role** and attach our policy to it. We will use this role as the **EC2** instance role later on.
1. Click on **Roles** in the side nav

2. Click **Create New Role**

3. Under **Select Role Type**, select **Amazon EC2** from the *AWS Service Role* section

4. Attach our newly created `s3-vault-full-access` policy to the role and click **Next Step**

5. Give the role a name: `vault-ec2`

7. Lastly, review our role, and click **Create Role**

Lastly, due to a bug in the current version of vault (v4.1), we create a new user and assign the policy to it. We will then generate access keys for this user to use when initializing vault.
1. Click on **Users** from the side nav

2. Click on **Add User**

3. Enter a username: `vault`

4. Select **Programmatic access** from the *Select AWS access type* section

5. Click **Next: Permissions**

6. Click **Attach existing policies directly**

7. Select the `s3-vault-full-access` policy.

6. Click **Next: Review**

7. Click **Create User**

8. Save/download the security credentials on the next screen

9. Back to the **Users** screen, and click on our newly created user

## Launch EC2
Ok, now it's time to launch an ec2 that will act as our Vault server.
1. From the AWS Management Console, go to the EC2 console.

2. Click **Launch Instance**

3. Select the most recent **Amazon Linux AMI**, usually at the top

4. Select an appropriate size, for this tutorial, I'll use a t2.nano

5. Click **Next: Configure Instance Details**

6. Under **IAM role**, select the **IAM Role** we created earlier (`s3-vault-full-access`)

7. Click **Next: Add Storage**

8. The default storage is fine, so click **Next: Add Tags**

9. Give your instance a name tag: `vault`

10. Click **Next: Configure Security Group**

11. Give your security group a name: `vault`

12. Give your security group a description: `vault server security group`

13. Click **Add Rule**

14. Select **Custom TCP Rule** and define a port range: `8200`

15. Under source, for the purposes of this tutorial, select **My IP**. However, in production, you should restrict this port to the security groups of the servers that require access to vault.

16. Click **Review and Launch**

17. Click **Launch**

18. If you have an existing key-pair, you can use it, or create a new one and download it

19. Lastly, click **Launch Instance** and then **View Instances**


## Option 1. Generating self-signed certificate for vault
Now, we're going to generate a self-signed certificate to use with vault. You can go ahead and skip this step if you already have an ssl certificate to use. *Note:* these steps were taken from [http://www.akadia.com/services/ssh_test_certificate.html](http://www.akadia.com/services/ssh_test_certificate.html)

1. ssh into the ec2 instance
```bash
ssh -i <path/to/key.pem> ec2-user@<ec2-dns>
```

  *note:* if this is a new key, you may receive a permission denied error, in which case, modify the key permissions and try again.
  ```bash
  chmod 0700 <path/to/key.pem>
  ```

2. Create a directory to hold the ssl stuff
```bash
mkdir .ssl && cd .ssl
```

3. Generate a private key, remember the password you use
```bash
openssl genrsa -des3 -out server.key 1024
```

4. Generate a CSR (Certificate Signing Request). This will prompt you to enter some details, go ahead and skip the challenge password part by pressing `enter`. Enter the FQDN for the server, when prompted.
```bash
openssl req -new -key server.key -out server.csr
```

5. Remove passhprase from key
```bash
cp server.key server.key.org
openssl rsa -in server.key.org -out server.key
```

6. Generate a Self-Signed Certificate
```bash
openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt
```

7. Minor cleanup, discard the temporary key file
```bash
rm server.key.org && cd
```

8. Add the certificate to the certificate manager of any machine that needs to make calls to the vault.

## Option 2. Generating a Let's Encrypt certificate

1. Temporarily enable inbound ports 80 and 443 from Anywhere
2. Run the following commands
```bash
sudo yum update -y
sudo yum install git
sudo git clone https://github.com/certbot/certbot /opt/letsencrypt
cd /etc
sudo mkdir letsencrypt
cd
wget https://dl.eff.org/certbot-auto
chmod a+x certbot-auto
./certbot-auto certonly --standalone --debug -d YOUR_DOMAIN
sudo cp /etc/letsencrypt/live/YOUR_DOMAIN/fullchain.pem /home/ec2-user/.ssl/server.crt
sudo cp /etc/letsencrypt/live/YOUR_DOMAIN/privkey.pem /home/ec2-user/.ssl/server.key
sudo chown ec2-user:ec2-user /home/ec2-user/.ssl/server.*
```
3. Remove the inbound rules added in step 1

## Installing Vault
Once the instance has finished initializing, it's time to download the Vault binary and unpack it.

1. update the instance
```bash
sudo yum update
```

2. install Vault *(find the latest binary on the vault project page)*
```bash
wget https://releases.hashicorp.com/vault/0.8.0/vault_0.8.0_linux_amd64.zip
```

3. unzip it
```bash
unzip vault_0.8.0_linux_amd64.zip
```

4. move the binary
```bash
sudo mv vault /usr/local/bin/vault
```

5. verify that Vault is ready to go
```bash
vault version
```

6. create the vault configuration file
```bash
touch vault-config.hcl
```

7. edit the file
```bash
nano vault-config.hcl
```

8. define the vault configuration like so. 

```bash
listener "tcp" {
    address = "0.0.0.0:8200"
    tls_cert_file="/home/ec2-user/.ssl/server.crt"
    tls_key_file="/home/ec2-user/.ssl/server.key"
}

backend "s3" {
    bucket = "<your_bucket_name>"
    region = "us-west-2"
}

disable_mlock=true
```

9. exit and save (control+x to exit, y to save, enter to confirm)

## Installing supervisord
Next, we install [supervisord](http://supervisord.org/), which will simplify the whole "let's get Vault running as a service, and have it start on reboot, blah blah blah"

1. install `supervisor`
```bash
sudo easy_install supervisor
```

2. create a new `supervisord` [configuration](http://supervisord.org/configuration.html) file
```bash
echo_supervisord_conf > supervisord.conf
```

3. modify the configuration file
    - under `[unix_http_server]`
        - change `;chmod=0700` to `chmod=0766`
    - change the `;[program:theprogramname]` header to `[program:vault]`
    - under `[program:vault]`
        - change `;command=/bin/cat` to `command=/usr/local/bin/vault server -config=/home/ec2-user/vault-config.hcl`
        - change `;user=chrism` to `user=ec2-user`
        - change `;environment=A="1",B="2"` to `environment=AWS_ACCESS_KEY_ID="<your_access_key_id",AWS_SECRET_ACCESS_KEY="<your_secret_access_key>"`, where `<your_access_key_id>` and `<your_secret_access_key>` are the credentials you downloaded/wrote down when we created the `vault` user.


## Configuring supervisord
Lastly, we need to configure `supervisord` to start on init.

1. create a new init script
```bash
sudo touch /etc/init.d/supervisord
```

2. edit the file contents:
```bash
sudo nano /etc/init.d/supervisord
```

```bash
#!/bin/sh
# Amazon Linux AMI startup script for a supervisor instance
#
# chkconfig: 2345 80 20
# description: Autostarts supervisord.

# Source function library.
. /etc/rc.d/init.d/functions

supervisorctl="/usr/local/bin/supervisorctl"
supervisord="/usr/local/bin/supervisord"
name="supervisor-python"

[ -f $supervisord ] || exit 1
[ -f $supervisorctl ] || exit 1

RETVAL=0

start() {
     echo -n "Starting $name: "
     $supervisord -c /home/ec2-user/supervisord.conf
     RETVAL=$?
     echo
     return $RETVAL
}

stop() {
     echo -n "Stopping $name: "
    $supervisorctl shutdown
     RETVAL=$?
     echo
     return $RETVAL
}

case "$1" in
         start)
             start
             ;;

         stop)
             stop
             ;;

         restart)
             stop
             start
             ;;
esac

exit $REVAL
```

3. make the init script executable
```bash
sudo chmod +x /etc/init.d/supervisord
```

3. add the supervisor init script to chkconfig services
```bash
sudo chkconfig --add supervisord
```

4. start the supervisord service
```bash
sudo service supervisord start
supervisorctl
```

## Initialise the vault

Exit out of the Linux VM
```bash
exit
```

Create a key, require only the one key to unseal the vault.
```bash
vault init -key-shares=1 -key-threshold=1
```

## Create the Travis user token

From the root of the Primary Authority Register repository:

    vault policy-write travis devops/vault/policies/travis.hcl
    vault auth-enable userpass
    vault write auth/userpass/users/travis password=**** policies=travis
    