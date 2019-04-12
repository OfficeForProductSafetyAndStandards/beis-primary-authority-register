####################################################################################
# Run this script from the directory in which it lives
####################################################################################
# You'll need the following installed
#
#    AWS CLI - http://docs.aws.amazon.com/cli/latest/userguide/installing.html
#    Cloud Foundry CLI - https://docs.cloudfoundry.org/cf-cli/install-go-cli.html
#    Vault CLI - https://www.vaultproject.io/docs/install/index.html
####################################################################################

import os, sys, subprocess, hvac

def error(str):
  print("################################################################################################")
  print(str, file=sys.stderr)
  print("################################################################################################")
  exit(1)

def which(program):
  import os
  def is_exe(fpath):
    return os.path.isfile(fpath) and os.access(fpath, os.X_OK)

  fpath, fname = os.path.split(program)
  if fpath:
    if is_exe(program):
      return program
  else:
    for path in os.environ["PATH"].split(os.pathsep):
      exe_file = os.path.join(path, program)
      if is_exe(exe_file):
        return exe_file

  return None  

def bash_command(cmd, my_env, wait=False):
  sp = subprocess.Popen("cd ../../ && " + cmd, shell=True, executable='/bin/bash', env=my_env)
  if (wait):
    sp.wait();

def write_file(path, contents):
  file = open(path, "w")
  file.write(contents)

def cf_login(local_env):
  print("Logging in")
  bash_command("cf login -a " + os.environ["GOVUK_CF_ENDPOINT"] +
               " -u " + os.environ["GOVUK_CF_USER"] +
               " -p \"" + os.environ["GOVUK_CF_PASSWORD"] + "\"" +
               " -o " + os.environ["GOVUK_CF_ORG"] +
               " -s " + os.environ["GOVUK_CF_SPACE"], local_env)

def make_directories(environment):
  build_dir = "build-" + environment
  os.system("rm -rf " + build_dir)
  os.system("mkdir " + build_dir)
  os.system("rm -rf files")
  os.system("mkdir files")
  return build_dir

def cf_push(app_name, local_env):
  bash_command("cf push " + app_name + " -d $GOVUK_CF_ROOT_DOMAIN -k 1024M -m 512M", my_env=local_env, wait=True)

def cf_set_env(app_name, vault, local_env):
  bash_command("cf set-env " + app_name + " BEIS_PAR_PUBNUB_PUBLISH_KEY " + vault["BEIS_PAR_PUBNUB_PUBLISH_KEY"], my_env=local_env)
  bash_command("cf set-env " + app_name + " BEIS_PAR_PUBNUB_SUBSCRIBE_KEY " + vault["BEIS_PAR_PUBNUB_SUBSCRIBE_KEY"], my_env=local_env)
  bash_command("cf set-env " + app_name + " UPTIME_ROBOT_API_KEY " + vault["UPTIME_ROBOT_API_KEY"], my_env=local_env)

def cf_restage(app_name, local_env):
  bash_command("cf restage " + app_name, my_env=local_env, wait=True)

def check_readiness():
  if (which("vault") == None): 
    error("Please install Vault CLI - https://www.vaultproject.io/docs/install/index.html")
    exit(1)

  if (which("aws") == None):
    error("Please install AWS CLI - http://docs.aws.amazon.com/cli/latest/userguide/installing.html. If you have it set up in a Python virtual env, you may need to run workon")
    exit(1)

  if (which("cf") == None):
    error("Please install Cloud Foundry CLI - https://docs.cloudfoundry.org/cf-cli/install-go-cli.html")
    exit(1)

########################################################################################################################################################################################################

def main():
  app_name = "beis-par-dashboard"

  check_readiness()

  vault_client = hvac.Client(url=os.environ["GOVUK_VAULT_ADDR"], token=os.environ['GOVUK_VAULT_AUTH_TOKEN'])
  vault_client.unseal(os.environ["GOVUK_VAULT_UNSEAL_KEY"])
  vault_env = vault_client.read("secret/" + app_name + "/env/production")["data"]

  local_env_vars = os.environ

  #cf_login(local_env_vars)

  cf_push(app_name, local_env_vars)

  cf_set_env(app_name, environment, local_env_vars)

  #cf_restage(app_name, environment, local_env_vars)


main()