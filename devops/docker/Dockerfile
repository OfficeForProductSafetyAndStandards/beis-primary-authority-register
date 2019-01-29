# beispar/web:v1

FROM php:7.1-apache
RUN sh -c "echo debconf shared/accepted-oracle-license-v1-1 select true | debconf-set-selections" \
    && sh -c "echo debconf shared/accepted-oracle-license-v1-1 seen true | debconf-set-selections" \
    && apt-get update && apt-get install -y wget npm \
    && wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - \
    && sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ xenial-pgdg main" >> /etc/apt/sources.list.d/postgresql.list' \
    && apt-get update && apt-get install -y \
        sudo \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        libpq-dev \
        php5-cli \
        postgresql-client-9.6 \
        git \
        software-properties-common \
        python-software-properties \
        libnss3 \
        libgconf-2-4 \
        libfontconfig \
        unzip \
        python-pip \
        google-chrome-stable \
    && sh -c "echo \"deb http://ppa.launchpad.net/webupd8team/java/ubuntu xenial main\" | tee /etc/apt/sources.list.d/webupd8team-java.list" \
    && sh -c "echo \"deb-src http://ppa.launchpad.net/webupd8team/java/ubuntu xenial main\" | tee -a /etc/apt/sources.list.d/webupd8team-java.list" \
    && apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886 \
    && apt-get -y update \
    && apt-get -y install oracle-java8-installer \
    && docker-php-ext-install -j$(nproc) iconv mcrypt pdo pdo_pgsql \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && sh -c 'cd /tmp && git clone https://github.com/tj/n && cd n && make install' \
    && n 7.2.1 \
    && rm `find / -name *.tar.gz` \
    && adduser --disabled-password --gecos "" composer \
    && sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/web/g' /etc/apache2/sites-available/000-default.conf

