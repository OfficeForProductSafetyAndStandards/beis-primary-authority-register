# Use official Maven image with JDK 17 (or change version as needed)
# Use official Maven base image with JDK
FROM maven:3.9.4-eclipse-temurin-17

# Install Chrome
RUN apt-get update && apt-get install -y wget gnupg2 unzip curl \
    && wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | apt-key add - \
    && echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" \
        > /etc/apt/sources.list.d/google-chrome.list \
    && apt-get update && apt-get install -y google-chrome-stable \
    && rm -rf /var/lib/apt/lists/*

# Install ChromeDriver (matching version)
RUN CHROME_VERSION=$(google-chrome --version | grep -oP '\d+' | head -1) && \
    curl -sS -o /tmp/chromedriver.zip \
    https://chromedriver.storage.googleapis.com/$(curl -sS https://chromedriver.storage.googleapis.com/LATEST_RELEASE)/chromedriver_linux64.zip && \
    unzip /tmp/chromedriver.zip -d /usr/local/bin && chmod +x /usr/local/bin/chromedriver

 #Set working directory
WORKDIR /app

# Copy project files
COPY . .


# Default command to run tests
CMD ["mvn", "clean", "test"]
