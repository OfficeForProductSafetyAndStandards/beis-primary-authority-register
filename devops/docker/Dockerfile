# beispar/web:v1

FROM php:7.3-apache

WORKDIR /var/www/html

# Update & install packages & libraries.
RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y \
        sudo \
        apt-utils \
        gnupg2 \
        wget \
        curl

RUN sh -c "echo debconf shared/accepted-oracle-license-v1-1 select true | debconf-set-selections" \
    && sh -c "echo debconf shared/accepted-oracle-license-v1-1 seen true | debconf-set-selections" \
    && apt-key adv --no-tty --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886 \
    && sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ stretch-pgdg main" | sudo tee /etc/apt/sources.list.d/pgdg.list' \
    && apt-key adv --no-tty --fetch-keys https://www.postgresql.org/media/keys/ACCC4CF8.asc \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get -f install -y \
        postgresql-client-10 \
        software-properties-common \
        libnss3 \
        libgconf-2-4 \
        libfontconfig \
        unzip \
        python-pip

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
    && sudo -E apt-get update && sudo -E DEBIAN_FRONTEND=noninteractive apt-get install -y $buildDeps $runtimeDeps \
    && sudo -E docker-php-ext-install -j$(nproc) intl mbstring opcache pdo_pgsql pgsql \
    && sudo -E docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && sudo -E docker-php-ext-install gd \
    && sudo -E docker-php-ext-install exif \
    && pecl install mcrypt-1.0.2 \
    && sudo -E docker-php-ext-enable mcrypt \
    && apt-get purge -y --auto-remove $buildDeps \
    && rm -r /var/lib/apt/lists/* \
    && a2enmod rewrite

# Configure Apache
RUN adduser --disabled-password --gecos "" composer \
    && sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/web/g' /etc/apache2/sites-available/000-default.conf

# Install composer
## Using composer installation from https://getcomposer.org/download/
RUN cd /tmp \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
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

# Install java 8
## Using java installation from CircleCi docker images circleci/php:7.3.1-apache-stretch-node-browsers
RUN if grep -q Debian /etc/os-release && grep -q jessie /etc/os-release; then \
    echo "deb http://http.us.debian.org/debian/ jessie-backports main" | sudo tee -a /etc/apt/sources.list \
    && echo "deb-src http://http.us.debian.org/debian/ jessie-backports main" | sudo tee -a /etc/apt/sources.list \
    && sudo apt-get update; sudo apt-get install -y -t jessie-backports openjdk-8-jre openjdk-8-jre-headless openjdk-8-jdk openjdk-8-jdk-headless \
  ; elif grep -q Ubuntu /etc/os-release && grep -q Trusty /etc/os-release; then \
    echo "deb http://ppa.launchpad.net/openjdk-r/ppa/ubuntu trusty main" | sudo tee -a /etc/apt/sources.list \
    && echo "deb-src http://ppa.launchpad.net/openjdk-r/ppa/ubuntu trusty main" | sudo tee -a /etc/apt/sources.list \
    && sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-key DA1A4A13543B466853BAF164EB9B1D8886F44E2A \
    && sudo apt-get update; sudo apt-get install -y openjdk-8-jre openjdk-8-jre-headless openjdk-8-jdk openjdk-8-jdk-headless \
  ; else \
    sudo apt-get update; sudo apt-get install -y openjdk-8-jre openjdk-8-jre-headless openjdk-8-jdk openjdk-8-jdk-headless \
  ; fi \
  && sudo apt-get install -y bzip2 libgconf-2-4 # for extracting firefox and running chrome, respectively

# Install firefox
## Using firefox installation from CircleCi docker images circleci/php:7.3.1-apache-stretch-node-browsers
RUN FIREFOX_URL="https://download.mozilla.org/?product=firefox-latest-ssl&os=linux64&lang=en-US" \
  && ACTUAL_URL=$(curl -Ls -o /dev/null -w %{url_effective} $FIREFOX_URL) \
  && curl --silent --show-error --location --fail --retry 3 --output /tmp/firefox.tar.bz2 $ACTUAL_URL \
  && sudo tar -xvjf /tmp/firefox.tar.bz2 -C /opt \
  && sudo ln -s /opt/firefox/firefox /usr/local/bin/firefox \
  && sudo apt-get install -y libgtk3.0-cil-dev libasound2 libasound2 libdbus-glib-1-2 libdbus-1-3 \
  && rm -rf /tmp/firefox.* \
  && firefox --version

# Install chrome
## Using chromedriver isntallation from CircleCi docker images circleci/php:7.3.1-apache-stretch-node-browsers
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
CMD sudo service apache2 start

EXPOSE 80