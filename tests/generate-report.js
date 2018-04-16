const reporter = require('cucumber-html-reporter');
const options = {
        theme: 'bootstrap',
        jsonFile: 'reports/cucumber.json',
        output: 'reports/index.html',
        reportSuiteAsScenarios: true,
        launchReport: false,
};
reporter.generate(options);
