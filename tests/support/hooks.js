const { client } = require('nightwatch-cucumber')
const reporter = require('cucumber-html-reporter');
var path = require('path');
var {After, AfterAll, Before, BeforeAll} = require('cucumber');

// Before({tags: "@api"}, function () {
//     this.apickli = new apickli.Apickli('http', 'httpbin.org');
//     this.apickli.addRequestHeader('Cache-Control', 'no-cache');
// });

// AfterAll(function () {
//     const options = {
//         theme: 'bootstrap',
//         jsonFile: 'reports/cucumber.json',
//         output: 'reports/index.html',
//         reportSuiteAsScenarios: true,
//         launchReport: false,
//     };
//     reporter.generate(options);
// });

After(function () {
    return client
        .deleteCookies(function() {
            client.end();
          });
});
