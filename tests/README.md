# e2e-testing
End-to-End Testing with Nightwatch and Cucumber.  Nightwatch is a browser automation framework component, supporting many browsers (inlcuding Electron). Tests are coded in Nodejs, and run against a Selenium/WebDriver server. API testing is also supported. Another key component is FakerJS, for test data generation.

### Step 1

Install all dependencies

```
$ npm install
```

### Step 2

Install selenium

```
$ npm run selenium-install
```

### Step 3

To run:

```
$ node_modules/.bin/nightwatch -c ./nightwatch-cucumber.conf.js -e firefox
$ node_modules/.bin/nightwatch -c ./nightwatch-cucumber.conf.js
$ node_modules/.bin/nightwatch -c ./nightwatch-cucumber.conf.js -e chrome-firefox
```

To run tests in parallel:

```
$ node_modules/.bin/nightwatch -c ./nightwatch-cucumber-parallel.conf.js
```

To run by tag

```
$ node_modules/.bin/nightwatch -c ./nightwatch-cucumber-parallel.conf.js -- --tag tagname
```

### Step 4

Generate HTML report (with screenshots)

```
$ node generate-report.js
```


# iOS testing

Using Appium - in order to run tests against, you will need Xcode installed, to use the simulators.

To run
$ node_modules/.bin/appium
$ node_modules/.bin/nightwatch -c ./nightwatch-cucumber-ios.conf.js