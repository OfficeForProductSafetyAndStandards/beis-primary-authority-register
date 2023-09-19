# beispar/app:latest
FROM php:8.2.3-apache-buster as production

# Configure PHP extensions
## Using php upgrade from https://github.com/chialab/docker-php
## Sudo required due to https://github.com/docker-library/php/issues/750
RUN PHP_DEPS="\
      libbz2-dev \
      libsasl2-dev \
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
    && PHP_EXTENSIONS="\
      intl \
      mbstring \
      opcache \
      pdo_pgsql \
      pgsql \
      gd \
      exif \
      zip \
    " \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y $PHP_DEPS \
    && pecl install redis \
    && docker-php-ext-install -j$(nproc) $PHP_EXTENSIONS \
    && rm -rf /var/lib/apt/lists/*

# Enable the requisite Apache modules.
RUN a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod proxy_http \
    && a2enmod proxy_ajp \
    && a2enmod proxy_balancer \
    && a2enmod proxy_connect \
    && a2enmod proxy_html

# Configure Apache virtual host
COPY ./par.localhost.conf /etc/apache2/sites-available/par.localhost.conf
RUN mkdir -p /var/www/html/web \
    && chown -RhL www-data:www-data /var/www/html/web \
    && a2ensite par.localhost

# Install composer
## Using composer installation from https://getcomposer.org/download/
ENV COMPOSER_VERSION 2.6.3
RUN cd /tmp \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --version=$COMPOSER_VERSION --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer --version

# Install node
## Using the node installation from https://github.com/nodejs/docker-node
ENV NODE_VERSION 7.3.0
RUN ARCH= && dpkgArch="$(dpkg --print-architecture)" \
    && case "${dpkgArch##*-}" in \
      amd64) ARCH='x64';; \
      ppc64el) ARCH='ppc64le';; \
      s390x) ARCH='s390x';; \
      arm64) ARCH='arm64';; \
      armhf) ARCH='armv7l';; \
      i386) ARCH='x86';; \
      *) echo "unsupported architecture"; exit 1 ;; \
    esac \
    && set -ex \
    && NODE_DEPS="\
      ca-certificates \
      wget \
      gnupg \
      dirmngr \
      xz-utils \
    " \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends --no-upgrade $NODE_DEPS \
    && rm -rf /var/lib/apt/lists/* \
    && for key in \
      4ED778F539E3634C779C87C6D7062848A1AB005C \
      141F07595B7B3FFE74309A937405533BE57C7D57 \
      74F12602B6F1C4E913FAA37AD3A89613643B6201 \
      DD792F5973C6DE52C432CBDAC77ABFA00DDBF2B7 \
      61FC681DFB92A079F1685E77973F295594EC4689 \
      8FCCA13FEF1D0C2E91008E09770F7A9A5AE15600 \
      C4F0DFFF4E8C1A8236409D08E73BC641CC11F4C8 \
      890C08DB8579162FEE0DF9DB8BEAB4DFCF555EF4 \
      C82FA3AE1CBEDC6BE46B9360C43CEC45C17AB93C \
      108F52B48DB57BB0CC439B2997B01419BD92F80A \
      9554F04D7259F04124DE6B476D5A82AC7E37093B \
      94AE36675C464D64BAFA68DD7434390BDBE9B9C5 \
      1C050899334244A8AF75E53792EF661D867B9DFA \
      B9AE9905FFD7803F25714661B63B535A4C206CA9 \
      77984A986EBC2AA786BC0F66B01FBB92821C587A \
      93C7E9E91B49E432C2F75674B0A78B0A6C481CF6 \
      56730D5401028683275BD23C23EFEFE93C4CFFFE \
      71DCFD284A79C3B38668286BC97EC7A07EDE3FC1 \
      FD3A5288F042B6850C66B31F09FE44734EB7990E \
      61FC681DFB92A079F1685E77973F295594EC4689 \
      114F43EE0176B71C7BC219DD50A3051F888C628D \
      DD8F2338BAE7501E3DD5AC78C273792F7D83545D \
      A48C2BEE680E841632CD4E44F07496B3EB3C1762 \
      B9E2F5981AA6E0CD28160D9FF13993A75599653C \
      7937DFD2AB06298B2293C3187D33FF9D0246406D \
    ; do \
      gpg --batch --keyserver hkps://keys.openpgp.org --recv-keys "$key" || \
      gpg --batch --keyserver keyserver.ubuntu.com --recv-keys "$key" ; \
    done \
    && curl -fsSLO --compressed "https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-$ARCH.tar.xz" \
    && curl -fsSLO --compressed "https://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.asc" \
    && gpg --batch --decrypt --output SHASUMS256.txt SHASUMS256.txt.asc \
    && grep " node-v$NODE_VERSION-linux-$ARCH.tar.xz\$" SHASUMS256.txt | sha256sum -c - \
    && tar -xJf "node-v$NODE_VERSION-linux-$ARCH.tar.xz" -C /usr/local --strip-components=1 --no-same-owner \
    && rm "node-v$NODE_VERSION-linux-$ARCH.tar.xz" SHASUMS256.txt.asc SHASUMS256.txt \
    && ln -s /usr/local/bin/node /usr/local/bin/nodejs \
    # smoke tests
    && node --version \
    && npm --version

# Copy any .ini files.
COPY ./php/* /home/php/conf.d/

EXPOSE 80


# The dev image adds all the dependencies required for building and debugging the production image.
FROM production as dev

# Ensure the keyrings directory exists.
RUN mkdir -p /etc/apt/keyrings/

# Ensure the necessary build components are installed.
ARG BUILD_DEPS="\
    sudo \
    lsb-release \
    curl \
    bzip2 \
    xz-utils \
    unzip \
    apt-utils \
    apt-transport-https \
    ca-certificates \
    gnupg \
    jq"
# Ensure the necessary packages are installed: CloudFoundry (with conduit), Vault, Postgres
ARG BUILD_PACKAGES="\
    cf8-cli \
    vault \
    postgresql-client-13"
# Add apt sources, update the packages list and install the dependencies.
RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends --no-upgrade $BUILD_DEPS \
    && curl -sS https://packages.cloudfoundry.org/debian/cli.cloudfoundry.org.key | gpg -o /etc/apt/keyrings/cloudfoundry.gpg --dearmor \
    && curl -sS https://apt.releases.hashicorp.com/gpg | gpg -o /etc/apt/keyrings/vault.gpg --dearmor \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg -o /etc/apt/keyrings/postgresql.gpg --dearmor \
    && sh -c 'echo "deb [signed-by=/etc/apt/keyrings/cloudfoundry.gpg] https://packages.cloudfoundry.org/debian stable main" | sudo tee /etc/apt/sources.list.d/cloudfoundry-cli.list' \
    && sh -c 'echo "deb [signed-by=/etc/apt/keyrings/vault.gpg] https://apt.releases.hashicorp.com $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/hashicorp.list' \
    && sh -c 'echo "deb [signed-by=/etc/apt/keyrings/postgresql.gpg] https://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list' \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends --no-upgrade $BUILD_PACKAGES \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /etc/apt/sources.list.d/cloudfoundry-cli.list \
    && rm -rf /etc/apt/sources.list.d/hashicorp.list \
    && rm -rf /etc/apt/sources.list.d/pgdg.list \
    && cf install-plugin conduit -f

# Install AWS CLI
## Using vault installation from https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html
RUN cd /tmp \
    && curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip" \
    && unzip awscliv2.zip \
    && ./aws/install

# Configure PCov & xdebug code coverage reports.
RUN cd /tmp \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && pecl install xdebug

# Configure the container with a custom entrypoint script.
COPY --chmod=755 ./par-entrypoint.sh /usr/local/bin/par-entrypoint
ENTRYPOINT ["par-entrypoint"]


# The final test image adds the remaining test suites.
FROM dev as test

# Install Java
ARG BUILD_DEPS="\
    default-jdk-headless"
# Install java dependencies.
RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends --no-upgrade $BUILD_DEPS \
    && rm -rf /var/lib/apt/lists/* \
    && java --version

# Install Maven
## Using the maven installation from https://github.com/carlossg/docker-maven
ARG MAVEN_VERSION=3.9.4
ARG MAVEN_HOME="/opt/maven"
ARG SHA=deaa39e16b2cf20f8cd7d232a1306344f04020e1f0fb28d35492606f647a60fe729cc40d3cba33e093a17aed41bd161fe1240556d0f1b80e773abd408686217e
ARG BASE_URL=https://downloads.apache.org/maven/maven-3/${MAVEN_VERSION}/binaries

RUN set -eux; curl -fsSLO --compressed ${BASE_URL}/apache-maven-${MAVEN_VERSION}-bin.tar.gz \
  && echo "${SHA} *apache-maven-${MAVEN_VERSION}-bin.tar.gz" | sha512sum -c - \
  && curl -fsSLO --compressed ${BASE_URL}/apache-maven-${MAVEN_VERSION}-bin.tar.gz.asc \
  && export GNUPGHOME="$(mktemp -d)"; \
  for key in \
  6A814B1F869C2BBEAB7CB7271A2A1C94BDE89688 \
  29BEA2A645F2D6CED7FB12E02B172E3E156466E8 \
  ; do \
  gpg --batch --keyserver hkps://keyserver.ubuntu.com --recv-keys "$key" ; \
  done; \
  gpg --batch --verify apache-maven-${MAVEN_VERSION}-bin.tar.gz.asc apache-maven-${MAVEN_VERSION}-bin.tar.gz
RUN mkdir -p ${MAVEN_HOME} ${MAVEN_HOME}/ref \
  && tar -xzf apache-maven-${MAVEN_VERSION}-bin.tar.gz -C ${MAVEN_HOME} --strip-components=1 \
  && ln -s ${MAVEN_HOME}/bin/mvn /usr/bin/mvn \
  && mvn --version

# Install jMeter
## Using instalation from https://github.com/justb4/docker-jmeter/blob/master/Dockerfile
ARG JMETER_VERSION="5.5"
ENV JMETER_HOME /opt/apache-jmeter-${JMETER_VERSION}
ENV JMETER_HOME /opt/apache-jmeter-${JMETER_VERSION}
ENV	JMETER_BIN ${JMETER_HOME}/bin
ENV	JMETER_DOWNLOAD_URL https://archive.apache.org/dist/jmeter/binaries/apache-jmeter-${JMETER_VERSION}.tgz
RUN mkdir -p /tmp/dependencies \
  && curl -L --silent ${JMETER_DOWNLOAD_URL} >  /tmp/dependencies/apache-jmeter-${JMETER_VERSION}.tgz \
  && mkdir -p /opt \
  && tar -xzf /tmp/dependencies/apache-jmeter-${JMETER_VERSION}.tgz -C /opt \
  && rm -rf /tmp/dependencies
# Set global PATH such that "jmeter" command is found
ENV PATH $PATH:$JMETER_BIN

# Install jMeter Plugins
ARG FILTER_RESULTS_PLUGIN_VERSION="2.2"
ENV	FILTER_RESULTS_PLUGIN_URL https://jmeter-plugins.org/files/packages/jpgc-filterresults-${FILTER_RESULTS_PLUGIN_VERSION}.zip
RUN mkdir -p /tmp/dependencies \
  && curl -L --silent ${FILTER_RESULTS_PLUGIN_URL} >  \
    /tmp/dependencies/jmeter-cmd-plugin-${FILTER_RESULTS_PLUGIN_VERSION}.zip \
  && unzip -o /tmp/dependencies/jmeter-cmd-plugin-${FILTER_RESULTS_PLUGIN_VERSION}.zip -d ${JMETER_HOME}
ARG SYNTHESIS_PLUGIN_VERSION="2.2"
ENV	SYNTHESIS_PLUGIN_URL https://jmeter-plugins.org/files/packages/jpgc-synthesis-${SYNTHESIS_PLUGIN_VERSION}.zip
RUN mkdir -p /tmp/dependencies \
  && curl -L --silent ${SYNTHESIS_PLUGIN_URL} >  \
    /tmp/dependencies/jmeter-cmd-plugin-${SYNTHESIS_PLUGIN_VERSION}.zip \
  && unzip -o /tmp/dependencies/jmeter-cmd-plugin-${SYNTHESIS_PLUGIN_VERSION}.zip -d ${JMETER_HOME}
ARG CMD_PLUGIN_VERSION="2.2"
ENV	CMD_PLUGIN_URL https://jmeter-plugins.org/files/packages/jpgc-cmd-${CMD_PLUGIN_VERSION}.zip
RUN mkdir -p /tmp/dependencies \
  && curl -L --silent ${CMD_PLUGIN_URL} >  \
    /tmp/dependencies/jmeter-cmd-plugin-${CMD_PLUGIN_VERSION}.zip \
  && unzip -o /tmp/dependencies/jmeter-cmd-plugin-${CMD_PLUGIN_VERSION}.zip -d ${JMETER_HOME}

#=====================================
# Browser testing for GDS requirements
# https://www.gov.uk/service-manual/technology/designing-for-different-browsers-and-devices
#=====================================
# Install geckodriver
ARG GECKODRIVER_VERSION=0.33.0
RUN echo "Using GeckoDriver version: "$GECKODRIVER_VERSION \
  && wget --no-verbose -O /tmp/geckodriver.tar.gz https://github.com/mozilla/geckodriver/releases/download/v$GECKODRIVER_VERSION/geckodriver-v$GECKODRIVER_VERSION-linux64.tar.gz \
  && rm -rf /opt/geckodriver \
  && tar -C /opt -zxf /tmp/geckodriver.tar.gz \
  && rm /tmp/geckodriver.tar.gz \
  && mv /opt/geckodriver /opt/geckodriver-$GECKODRIVER_VERSION \
  && chmod 755 /opt/geckodriver-$GECKODRIVER_VERSION \
  && ln -fs /opt/geckodriver-$GECKODRIVER_VERSION /usr/bin/geckodriver

# Install firefox
ARG FIREFOX_VERSION=114.0.2
RUN wget --no-verbose -O /tmp/firefox.tar.bz2 https://download-installer.cdn.mozilla.net/pub/firefox/releases/$FIREFOX_VERSION/linux-x86_64/en-US/firefox-$FIREFOX_VERSION.tar.bz2 \
    && rm -rf /opt/firefox \
    && tar -C /opt -xjf /tmp/firefox.tar.bz2 \
    && rm /tmp/firefox.tar.bz2 \
    && mv /opt/firefox /opt/firefox-$FIREFOX_VERSION \
    && ln -fs /opt/firefox-$FIREFOX_VERSION/firefox /usr/bin/firefox

# Install chrome
RUN curl -sS https://dl.google.com/linux/linux_signing_key.pub | gpg -o /etc/apt/keyrings/google.gpg --dearmor \
    && sh -c 'echo "deb [arch=amd64 signed-by=/etc/apt/keyrings/google.gpg] http://dl.google.com/linux/chrome/deb/ stable main" | sudo tee /etc/apt/sources.list.d/google-chrome.list' \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends --no-upgrade google-chrome-stable \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /etc/apt/sources.list.d/google-chrome.list \
    && google-chrome --version

RUN export CHROMEDRIVER_VERSION=$(curl --location --fail --retry 3 http://chromedriver.storage.googleapis.com/LATEST_RELEASE) \
    && export CHROMEDRIVER_DOWNLOAD_URL="https://chromedriver.storage.googleapis.com/${CHROMEDRIVER_VERSION}/chromedriver_linux64.zip" \
    && curl --silent --show-error --location --fail --retry 3 --output /tmp/chromedriver_linux64.zip $CHROMEDRIVER_DOWNLOAD_URL \
    && cd /tmp \
    && unzip chromedriver_linux64.zip \
    && rm -rf chromedriver_linux64.zip \
    && mv chromedriver /opt/google/chromedriver-$CHROMEDRIVER_VERSION \
    && chmod +x /opt/google/chromedriver-$CHROMEDRIVER_VERSION \
    && ln -fs /opt/google/chromedriver-$CHROMEDRIVER_VERSION /usr/bin/chromedriver \
    && chromedriver --version
