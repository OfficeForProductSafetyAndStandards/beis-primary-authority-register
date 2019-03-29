mkdir -p bin && cd bin && wget https://get.enterprisedb.com/postgresql/postgresql-9.5.7-1-linux-x64-binaries.tar.gz && tar -zxvf postgresql-9.5.7-1-linux-x64-binaries.tar.gz && cd ..

export PATH=/bin:/usr/bin:/home/vcap/app/bin/pgsql/bin
source /home/vcap/app/.profile.d/finalize_bp_env_vars.sh
export TEMP=/tmp
export HOME=/home/vcap/app
