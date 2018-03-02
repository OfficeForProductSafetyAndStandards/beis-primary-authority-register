Cucumberjs/Webdriverio
====================
* Install the dependencies (`npm install` or `yarn install`)

# How to run a test

To run your tests just call the [WDIO runner](http://webdriver.io/guide/testrunner/gettingstarted.html):

```
$ ./node_modules/.bin/wdio wdio.DEV.conf.js
```

Environments available are: 
* DEV (when testing on your local machine)
* BUILD (travis)
* BROWSERSTACK 

# Running single feature
Sometimes its useful to only execute a single feature file, to do so use the following command:

```sh
$ ./node_modules/.bin/wdio --spec ./test/features/select.feature wdio.DEV.conf.js
```

# Reports

Run following command to generate a html report

```sh
$ npm run generatehtmlreport
```

## Maintaining tests (Gherkin)

Most maintenance of tests involves updating things such as the way elements are referenced.  

This is within the Gherkin, so easy to update. 

## JMeter Load tests

* ./jmeter -n -t /jmeter/load-test-plan.jmx -l /jmeter/file.csv

# List of predefined steps

Check out all predefined snippets. You can see how they get used in [`sampleSnippets.feature`](https://github.com/webdriverio/cucumber-boilerplate/blob/master/src/features/sampleSnippets.feature).

## Given steps

- `I open the (url|site) "([^"]*)?"` <br>Open a site in the current browser window/tab
- `the element "([^"]*)?" is( not)* visible` <br>Check the (in)visibility of a element
- `the element "([^"]*)?" is( not)* enabled` <br>Check if a element is (not) enabled
- `the element "([^"]*)?" is( not)* selected` <br>Check if a element is (not) selected
- `the checkbox "([^"]*)?" is( not)* checked` <br>Check if a checkbox is (not) checked
- `there is (an|no) element "([^"]*)?" on the page` <br>Check if a element (does not) exist
- `the title is( not)* "([^"]*)?"` <br>Check the title of the current browser window/tab
- `the element "([^"]*)?" contains( not)* the same text as element "([^"]*)?"` <br>Compaire the text of two elements
- `the (element|inputfield) "([^"]*)?"( not)* contains the text "([^"]*)?"` <br>Check if a element contains the given text
- `the (element|inputfield) "([^"]*)?"( not)* contains any text` <br>Check if a element does not contain any text
- `the page url is( not)* "([^"]*)?"` <br>Check the url of the current browser window/tab
- `the( css)* attribute "([^"]*)?" from element "([^"]*)?" is( not)* "([^"]*)?"` <br>Check the value of a element's (css) attribute
- `the cookie "([^"]*)?" contains( not)* the value "([^"]*)?"` <br>Check the value of a cookie
- `the cookie "([^"]*)?" does( not)* exist` <br>Check the existence of a cookie
- `the element "([^"]*)?" is( not)* ([\d]+)px (broad|tall)` <br>Check the width/height of a element
- `the element "([^"]*)?" is( not)* positioned at ([\d]+)px on the (x|y) axis` <br>Check the position of a element
- `I have a screen that is ([\d]+) by ([\d]+) pixels` <br>Set the browser size to a given size
- `I have closed all but the first (window|tab)` <br>Close all but the first browser window/tab
- `a (alertbox|confirmbox|prompt) is( not)* opened` <br>Check if a modal is opened

## Then steps

- `I expect that the title is( not)* "([^"]*)?"` <br>Check the title of the current browser window/tab
- `I expect that element "([^"]*)?" is( not)* visible` <br>Check if a certain element is visible
- `I expect that element "([^"]*)?" becomes( not)* visible` <br>Check if a certain element becomes visible
- `I expect that element "([^"]*)?" is( not)* within the viewport` <br>Check if a certain element is within the current viewport
- `I expect that element "([^"]*)?" does( not)* exist` <br>Check if a certain element exists
- `I expect that element "([^"]*)?"( not)* contains the same text as element "([^"]*)?"` <br>Compare the text of two elements
- `I expect that (element|inputfield) "([^"]*)?"( not)* contains the text "([^"]*)?"` <br>Check if a element or input field contains the given text
- `I expect that (element|inputfield) "([^"]*)?"( not)* contains any text` <br>Check if a element or input field contains any text
- `I expect that (element|inputfield) "([^"]*)?" is( not)* empty` <br>Check if a element or input field is empty
- `I expect that the url is( not)* "([^"]*)?"` <br>Check if the the URL of the current browser window/tab is a certain string
- `I expect that the path is( not)* "([^"]*)?"` <br>Check if the path of the URL of the current browser window/tab is a certain string
- `I expect the url to( not)* contain "([^"]*)?"` <br>Check if the URL of the current browser window/tab contains a certain string
- `I expect that the( css)* attribute "([^"]*)?" from element "([^"]*)?" is( not)* "([^"]*)?"` <br>Check the value of a element's (css) attribute
- `I expect that checkbox "([^"]*)?" is( not)* checked` <br>Check if a check-box is (not) checked
- `I expect that element "([^"]*)?" is( not)* selected` <br>Check if a element is (not) selected
- `I expect that element "([^"]*)?" is( not)* enabled` <br>Check if a element is (not) enabled
- `I expect that cookie "([^"]*)?"( not)* contains "([^"]*)?"` <br>Check if a cookie with a certain name contains a certain value
- `I expect that cookie "([^"]*)?"( not)* exists` <br>Check if a cookie with a certain name exist
- `I expect that element "([^"]*)?" is( not)* ([\d]+)px (broad|tall)` <br>Check the width/height of an element
- `I expect that element "([^"]*)?" is( not)* positioned at ([\d]+)px on the (x|y) axis` <br>Check the position of an element
- `I expect that element "([^"]*)?" (has|does not have) the class "([^"]*)?"` <br>Check if a element has a certain class
- `I expect a new (window|tab) has( not)* been opened` <br>Check if a new window/tab has been opened
- `I expect the url "([^"]*)?" is opened in a new (tab|window)` <br>Check if a URL is opened in a new browser window/tab
- `I expect that element "([^"]*)?" is( not)* focused` <br>Check if a element has the focus
- `I wait on element "([^"]*)?"( for (\d+)ms)*( to( not)* (be checked|be enabled|be selected|be visible|contain a text|contain a value|exist))*` <br>Wait for a element to be checked, enabled, selected, visible, contain a certain value or text or to exist
- `I expect that a (alertbox|confirmbox|prompt) is( not)* opened` <br>Check if a modal is opened
- `I expect that a (alertbox|confirmbox|prompt)( not)* contains the text "$text"` <br>Check the text of a modal

## When steps

- `I (click|doubleclick) on the (link|button|element) "([^"]*)?"` <br>(Double)click a link, button or element
- `I (add|set) "([^"]*)?" to the inputfield "([^"]*)?"` <br>Add or set the content of an input field
- `I clear the inputfield "([^"]*)?"` <br>Clear an input field
- `I drag element "([^"]*)?" to element "([^"]*)?"` <br>Drag a element to another element
- `I submit the form "([^"]*)?"` <br>Submit a form
- `I pause for (\d+)ms` <br>Pause for a certain number of milliseconds
- `I set a cookie "([^"]*)?" with the content "([^"]*)?"` <br>Set the content of a cookie with the given name to  the given string
- `I delete the cookie "([^"]*)?"` <br>Delete the cookie with the given name
- `I press "([^"]*)?"` <br>Press a given key. You’ll find all supported characters [here](https://w3c.github.io/webdriver/webdriver-spec.html#keyboard-actions). To do that, the value has to correspond to a key from the table.
- `I (accept|dismiss) the (alertbox|confirmbox|prompt)` <br>Accept or dismiss a modal window
- `I enter "([^"]*)?" into the prompt` <br>Enter a given text into a modal prompt
- `I scroll to element "([^"]*)?"` <br>Scroll to a given element
- `I close the last opened (window|tab)` <br>Close the last opened browser window/tab
- `I focus the last opened (window|tab)` <br>Focus the last opened browser window/tab
- `I log in to site with username "([^"]*)?" and password "([^"]*)?"` <br>Login to a site with the given username and password
- `I select the (\d+)(st|nd|rd|th) option for element "([^"]*)?"` <br>Select a option based on it's index
- `I select the option with the (name|value|text) "([^"]*)?" for element "([^"]*)?"` <br>Select a option based on it's name, value or visible text
- `I move to element "([^"]*)?"( with an offset of (\d+),(\d+))` <br>Move the mouse by an (optional) offset of the specified element

## Pa11y Accessibility Checker

./node_modules/.bin/pa11y-ci -j > ./reports/wcag2aa_report.json

## Drush commands

docker exec -it par_beta_web /var/www/html/vendor/bin/drush --root=/var/www/html/web cr
docker exec -it par_beta_web /var/www/html/vendor/bin/drush --root=/var/www/html/web cim
docker exec -it par_beta_web bash -c 'php composer.phar install' && docker exec -it par_beta_web /var/www/html/vendor/bin/drush --root=/var/www/html/web config-import && docker exec -it par_beta_web /var/www/html/vendor/bin/drush --root=/var/www/html/web cr
