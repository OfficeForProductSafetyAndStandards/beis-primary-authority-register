# beispar/web:v3.0.0

FROM php:7.4.19-apache-buster

WORKDIR /var/www/html

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
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get -f install -y \
        build-essential \
        bzip2 \
        libnss3 \
        libfontconfig \
        libgconf-2-4 \
        libgl1-mesa-glx \
        libgtk-3-dev \
        unzip \
        python-pip \
        software-properties-common \
        cf-cli

# Install the Cloudfoundry Conduit tool for connecting to remote PaaS instances
RUN cf install-plugin conduit -f

# Install Postgres (client)
RUN RELEASE=$(lsb_release -cs) \
    && echo "deb http://apt.postgresql.org/pub/repos/apt/ ${RELEASE}"-pgdg main | sudo tee  /etc/apt/sources.list.d/pgdg.list \
    && apt-key adv --no-tty --fetch-keys https://www.postgresql.org/media/keys/ACCC4CF8.asc \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get -f install -y \
        postgresql-client-11

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
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libonig-dev \
        libpng-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
    " \
    && sudo -E apt-get update && sudo -E DEBIAN_FRONTEND=noninteractive apt-get install -y $buildDeps $runtimeDeps \
    && sudo -E docker-php-ext-install -j$(nproc) \
      intl \
      mbstring \
      opcache \
      pdo_pgsql \
      pgsql \
    && sudo -E docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ \
    && sudo -E docker-php-ext-install gd \
      exif \
      zip \
    && apt-get purge -y --auto-remove $buildDeps \
    && rm -r /var/lib/apt/lists/* \
    && a2enmod rewrite

# Configure Apache virtual host
COPY ./par.localhost.conf /etc/apache2/sites-available/par.localhost.conf

# Install composer
## Using composer installation from https://getcomposer.org/download/
ENV COMPOSER_VERSION 2.0.13
RUN cd /tmp \
    && sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --version=$COMPOSER_VERSION --install-dir=/usr/local/bin --filename=composer \
ParDataStorage    && php -r "unlink('composer-setup.php');" \
    && composer --version

# Install node
## Using the nvm installation from https://github.com/mklement0/n-install
RUN cd /tmp \
    && sh -c 'git clone https://github.com/tj/n && cd n && make install' \
    && n 7.2.1 \
    && node --version \
    && npm --version

# Install java 8
## Using java installation from CircleCi docker images circleci/php:7.3.1-apache-buster-node-browsers
RUN apt-add-repository 'deb http://security.debian.org/debian-security stretch/updates main' \
    && apt-get update \
    && apt-get install -y --no-install-recommends --no-upgrade openjdk-8-jre openjdk-8-jre-headless openjdk-8-jdk openjdk-8-jdk-headless

#=====================================
# Browser testing for GDS requirements
# https://www.gov.uk/service-manual/technology/designing-for-different-browsers-and-devices
#=====================================
# Install geckodriver
ARG GECKODRIVER_VERSION=0.29.1
RUN echo "Using GeckoDriver version: "$GECKODRIVER_VERSION \
  && wget --no-verbose -O /tmp/geckodriver.tar.gz https://github.com/mozilla/geckodriver/releases/download/v$GECKODRIVER_VERSION/geckodriver-v$GECKODRIVER_VERSION-linux64.tar.gz \
  && rm -rf /opt/geckodriver \
  && tar -C /opt -zxf /tmp/geckodriver.tar.gz \
  && rm /tmp/geckodriver.tar.gz \
  && mv /opt/geckodriver /opt/geckodriver-$GECKODRIVER_VERSION \
  && chmod 755 /opt/geckodriver-$GECKODRIVER_VERSION \
  && ln -fs /opt/geckodriver-$GECKODRIVER_VERSION /usr/local/bin/geckodriver

# Install firefox
## @TODO install actual working version of ff and other supported browsers
ARG FIREFOX_VERSION=88.0.1
RUN wget --no-verbose -O /tmp/firefox.tar.bz2 https://download-installer.cdn.mozilla.net/pub/firefox/releases/$FIREFOX_VERSION/linux-x86_64/en-US/firefox-$FIREFOX_VERSION.tar.bz2 \
    && rm -rf /opt/firefox \
    && tar -C /opt -xjf /tmp/firefox.tar.bz2 \
    && rm /tmp/firefox.tar.bz2 \
    && mv /opt/firefox /opt/firefox-$FIREFOX_VERSION \
    && ln -fs /opt/firefox-$FIREFOX_VERSION/firefox /usr/local/bin/firefox

# Install chrome
## Using chromedriver installation from CircleCi docker images circleci/php:7.3.1-apache-stretch-node-browsers
RUN curl --silent --show-error --location --fail --retry 3 --output /tmp/google-chrome-stable_current_amd64.deb https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb \
      && (sudo dpkg -i /tmp/google-chrome-stable_current_amd64.deb || sudo apt-get -fy install)  \
      && rm -rf /tmp/google-chrome-stable_current_amd64.deb \
      && sudo sed -i 's|HERE/chrome"|HERE/chrome" --disable-setuid-sandbox --no-sandbox|g' \
           "/opt/google/chrome/google-chrome" \
      && google-chrome --version

RUN export CHROMEDRIVER_RELEASE=$(curl --location --fail --retry 3 http://chromedriver.storage.googleapis.com/LATEST_RELEASE) \
      && curl --silent --show-error --location --fail --retry 3 --output /tmp/chromedriver_linux64.zip "http://chromedriver.storage.googleapis.com/$CHROMEDRIVER_RELEASE/chromedriver_linux64.zip" \
      && cd /tmp \
      && unzip chromedriver_linux64.zip \
      && rm -rf chromedriver_linux64.zip \
      && sudo mv chromedriver /usr/local/bin/chromedriver \
      && sudo chmod +x /usr/local/bin/chromedriver \
      && chromedriver --version

# Start apache
CMD apachectl -D FOREGROUND

EXPOSE 80
