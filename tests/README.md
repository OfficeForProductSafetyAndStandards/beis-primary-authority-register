# e2e-testing
End-to-End Testing with Nightwatch and Cucumber


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

### Step 4

Generate HTML report (with screenshots)

```
$ node generate-report.js
```

