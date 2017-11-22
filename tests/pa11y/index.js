// An example of running Pa11y programmatically
'use strict';

var pa11y = require('pa11y');
var url = process.env.PA11Y_URL;
var async = require('async');

// Create a test instance with some default options
var test = pa11y({
    options: {
        reporter: 'csv'
    },
    // actions: [
    //     'set field #edit-name to par_helpdesk@example.com',
    //     'set field #edit-pass to TestPassword',
    //     'click element #edit-submit',
    //     'wait for path to not be /user/login',
    //     'click element a.button-start',
    //     'wait for path to not be /welcome-reminder'
    // ],
    // Log what's happening to the console
    log: {
        debug: console.log.bind(console),
        error: console.error.bind(console),
        info: console.log.bind(console)
    },

    beforeScript: function (page, options, next) {
        var waitUntil = function (condition, retries, waitOver) {
            page.evaluate(condition, function (error, result) {
                if (result || retries < 1) {
                    waitOver();
                } else {
                    retries -= 1;
                    setTimeout(function () {
                        waitUntil(condition, retries, waitOver);
                    }, 200);
                }
            });
        };

        page.evaluate(function () {
            window.open("http://localhost:8111/user/login");
            document.getElementById("edit-name").value = "par_authority@example.com";
            document.getElementById("edit-pass").value = "TestPassword";
            document.getElementById("edit-submit").click();
        }, function () {
            waitUntil(function () {
                // Wait until the login has been success and the /news.html has loaded
                return window.location.href === 'http://localhost:8111/dashboard';
            }, 20, next);
        });
    }
});

async.series({

    // Test the first url
    login: test.run.bind(test, 'http://localhost:8111/user/login'),

    // Test second url
    dashboard: test.run.bind(test, 'http://localhost:8111/dashboard'),

    // Test second url
    partnerships: test.run.bind(test, 'http://localhost:8111/partnerships')

}, function(error, results) {
    if (error) {
        return console.error(error.message);
    }
    //console.log(results.login);
    console.log(results.dashboard);
    console.log(results.partnerships);

});
