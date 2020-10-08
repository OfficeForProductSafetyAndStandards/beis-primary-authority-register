# beispar/web:v3.2.0

FROM php:7.4-apache

WORKDIR /var/www/html

# Debian Jessie is EOL'd and original repos don't work.
# Switch to the archive mirror until we can get people to
# switch to Stretch.
RUN if grep -q Debian /etc/os-release && grep -q jessie /etc/os-release; then \
	rm /etc/apt/sources.list \
    && echo "deb http://archive.debian.org/debian/ jessie main" >> /etc/apt/sources.list \
    && echo "deb http://security.debian.org/debian-security jessie/updates main" >> /etc/apt/sources.list \
	; fi

# Update & install packages & libraries.
RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y \
        sudo \
        apt-utils \
        apt-transport-https \
        gnupg2 \
        wget \
        curl \
        certbot

RUN sh -c "echo debconf shared/accepted-oracle-license-v1-1 select true | debconf-set-selections" \
    && sh -c "echo debconf shared/accepted-oracle-license-v1-1 seen true | debconf-set-selections" \
    && apt-key adv --no-tty --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886 \
    && sh -c 'echo "deb https://packages.cloudfoundry.org/debian stable main" | sudo tee /etc/apt/sources.list.d/cloudfoundry-cli.list' \
    && apt-key adv --no-tty --fetch-keys https://packages.cloudfoundry.org/debian/cli.cloudfoundry.org.key \
    && sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ stretch-pgdg main" | sudo tee /etc/apt/sources.list.d/pgdg.list' \
    && apt-key adv --no-tty --fetch-keys https://www.postgresql.org/media/keys/ACCC4CF8.asc \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get -f install -y \
        postgresql-client-11 \
        software-properties-common \
        libnss3 \
        libgconf-2-4 \
        libfontconfig \
        python-pip \
        zip \
        unzip \
        cf-cli

# Install Vault
## Using vault installation from https://www.vaultproject.io/docs/install/index.html
RUN cd /tmp \
    && wget https://releases.hashicorp.com/vault/1.0.3/vault_1.0.3_linux_amd64.zip \
    && unzip vault_1.0.3_linux_amd64.zip \
    && mv vault /usr/local/bin/vault

# Install AWS CLI
## Using vault installation from https://www.vaultproject.io/docs/install/index.html
RUN cd /tmp \
    pip install awscli --upgrade

# Configure PHP extensions
## Using php upgrade from https://github.com/chialab/docker-php
## Sudo required due to https://github.com/docker-library/php/issues/750
RUN buildDeps=" \
        libbz2-dev \
        libsasl2-dev \
    " \
    runtimeDeps=" \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libmcrypt-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
    " \
    phpExt=" \
        exif \
        gd \
        intl \
        opcache \
        pdo_pgsql \
        pgsql \
        zip \
    " \
    && sudo -E apt-get update && sudo -E DEBIAN_FRONTEND=noninteractive apt-get install -y $buildDeps $runtimeDeps \
    && pecl install mcrypt-1.0.3 \
    && sudo -E docker-php-ext-enable mcrypt \
    && sudo -E docker-php-ext-install -j$(nproc) $phpExt \
    && apt-get purge -y --auto-remove $buildDeps \
    && rm -r /var/lib/apt/lists/* \
    && a2enmod rewrite

# Configure Apache virtual host
COPY ./par.localhost.conf /etc/apache2/sites-available/par.localhost.conf

# Install composer
## Using composer installation from https://getcomposer.org/download/
RUN cd /tmp \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '795f976fe0ebd8b75f26a6dd68f78fd3453ce79f32ecb33e7fd087d39bfeb978342fb73ac986cd4f54edd0dc902601dc') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

# Install node
## Using the nvm installation from https://github.com/mklement0/n-install
RUN cd /tmp \
    && sh -c 'git clone https://github.com/tj/n && cd n && make install' \
    && n 7.2.1 \
    && node --version \
    && npm --version

# install jq
RUN JQ_URL="https://circle-downloads.s3.amazonaws.com/circleci-images/cache/linux-amd64/jq-latest" \
  && curl --silent --show-error --location --fail --retry 3 --output /usr/bin/jq $JQ_URL \
  && chmod +x /usr/bin/jq \
  && jq --version

# Install Java 11 LTS / OpenJDK 11
# Using java installation from CircleCi docker images circleci/php:7.4.11
# @see https://github.com/circleci/circleci-images/blob/staging/shared/images/Dockerfile-browsers.template
RUN if grep -q Debian /etc/os-release && grep -q stretch /etc/os-release; then \
		echo 'deb http://deb.debian.org/debian stretch-backports main' | sudo tee -a /etc/apt/sources.list.d/stretch-backports.list; \
	elif grep -q Ubuntu /etc/os-release && grep -q xenial /etc/os-release; then \
		sudo apt-get update && sudo apt-get install -y software-properties-common && \
		sudo add-apt-repository -y ppa:openjdk-r/ppa; \
	fi && \
	sudo apt-get update && sudo apt-get install -y openjdk-11-jre openjdk-11-jre-headless openjdk-11-jdk openjdk-11-jdk-headless && \
	sudo apt-get install -y bzip2 libgconf-2-4 # for extracting firefox and running chrome, respectively

# Install firefox
RUN FIREFOX_URL="https://download.mozilla.org/?product=firefox-latest-ssl&os=linux64&lang=en-US" \
  && ACTUAL_URL=$(curl -Ls -o /dev/null -w %{url_effective} $FIREFOX_URL) \
  && curl --silent --show-error --location --fail --retry 3 --output /tmp/firefox.tar.bz2 $ACTUAL_URL \
  && sudo tar -xvjf /tmp/firefox.tar.bz2 -C /opt \
  && sudo ln -s /opt/firefox/firefox /usr/local/bin/firefox \
  && sudo apt-get install -y libgtk3.0-cil-dev libasound2 libasound2 libdbus-glib-1-2 libdbus-1-3 \
  && rm -rf /tmp/firefox.* \
  && firefox --version

# Install geckodriver
RUN export GECKODRIVER_LATEST_RELEASE_URL=$(curl https://api.github.com/repos/mozilla/geckodriver/releases/latest | jq -r ".assets[] | select(.name | test(\"linux64.tar.gz$\")) | .browser_download_url") \
     && curl --silent --show-error --location --fail --retry 3 --output /tmp/geckodriver_linux64.tar.gz "$GECKODRIVER_LATEST_RELEASE_URL" \
     && cd /tmp \
     && tar xf geckodriver_linux64.tar.gz \
     && rm -rf geckodriver_linux64.tar.gz \
     && sudo mv geckodriver /usr/local/bin/geckodriver \
     && sudo chmod +x /usr/local/bin/geckodriver \
     && geckodriver --version

# Install chrome
RUN sudo apt-get install -y xdg-utils libgbm1 fonts-liberation
RUN cd /tmp && wget -O google-chrome-stable_current_amd64.deb -t 5 "https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb" \
    && (sudo dpkg -i google-chrome-stable_current_amd64.deb || sudo apt-get -fy install)  \
    && rm -rf google-chrome-stable_current_amd64.deb \
    && sudo sed -i 's|HERE/chrome"|HERE/chrome" --disable-setuid-sandbox --no-sandbox|g' \
        "/opt/google/chrome/google-chrome" \
    && google-chrome --version

RUN CHROME_VERSION="$(google-chrome --version)" \
    && export CHROMEDRIVER_RELEASE="$(echo $CHROME_VERSION | sed 's/^Google Chrome //')" && export CHROMEDRIVER_RELEASE=${CHROMEDRIVER_RELEASE%%.*} \
    && CHROMEDRIVER_VERSION=$(curl --silent --show-error --location --fail --retry 4 --retry-delay 5 http://chromedriver.storage.googleapis.com/LATEST_RELEASE_${CHROMEDRIVER_RELEASE}) \
    && curl --silent --show-error --location --fail --retry 4 --retry-delay 5 --output /tmp/chromedriver_linux64.zip "http://chromedriver.storage.googleapis.com/$CHROMEDRIVER_VERSION/chromedriver_linux64.zip" \
    && cd /tmp \
    && unzip chromedriver_linux64.zip \
    && rm -rf chromedriver_linux64.zip \
    && sudo mv chromedriver /usr/local/bin/chromedriver \
    && sudo chmod +x /usr/local/bin/chromedriver \
    && chromedriver --version

# Google Chrome < v85 had libxss1 as a dependency. It no longer does but people
# have been depending on it so installing manually.
RUN sudo apt-get install -y libxss1

# Start apache
CMD apachectl -D FOREGROUND

EXPOSE 80