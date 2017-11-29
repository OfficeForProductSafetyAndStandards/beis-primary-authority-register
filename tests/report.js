const report = require('cucumber-html-report');
report.create({
    source:       './reports/report.json',      // source json
    dest:         './reports/html',                   // target directory (will create if not exists)
    name:         'report.html',                 // report file name (will be index.html if not exists)
    //template:     'mytemplate.html',             // your custom mustache template (uses default if not specified)
    title:        'Cucumber Report',             // Title for default template. (default is Cucumber Report)
    component:    'My Component',                // Subtitle for default template. (default is empty)
    //logo:         './logos/cucumber-logo.svg',   // Path to the displayed logo.
    screenshots:  './errorShots',               // Path to the directory of screenshots. Optional.
    dateformat:   'YYYY MM DD',                  // default is YYYY-MM-DD hh:mm:ss
    maxScreenshots: 100                           // Max number of screenshots to save (default is 1000)
})
    .then(console.log)
    .catch(console.error);
