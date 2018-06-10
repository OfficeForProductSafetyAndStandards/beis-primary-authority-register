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

#### Examples

Direct partnership related tests
```
$ node_modules/.bin/nightwatch -c ./nightwatch-cucumber-parallel.conf.js -- --tag directpartnership
```
Coordinated partnership related tests
```
$ node_modules/.bin/nightwatch -c ./nightwatch-cucumber-parallel.conf.js -- --tag coordinatedpartnership
```
Enforcement Notice related tests
```
$ node_modules/.bin/nightwatch -c ./nightwatch-cucumber-parallel.conf.js -- --tag enforcementnotice
```

### Step 4

Generate HTML report (with screenshots)

```
$ node generate-report.js
```


# iOS testing

Using Appium - in order to run tests against, you will need Xcode installed, to use the simulators.

Prequisistes:
XCode 9.2+
Android Studio SDK


There are enough emulators that come with XCode package, but to install, you will need to open from Xmcode app:

* Xcode > Open Developer Tool > Simulator
* In Simulator go to: Hardware > Device > Manage Devices
* In devices window at the bottom of the left column, click the Add button (+)
* Now follow the dialog.

To run:

$ npm install -g 
$ appium
[NEW CONSOLE TAB]
$ npm run nightwatch-cucumber-ios -- --tag iostest
