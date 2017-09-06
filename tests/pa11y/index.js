// An example of running Pa11y programmatically
'use strict';

var pa11y = require('pa11y');

console.log();

var url = 'http://localhost:8111/dv/primary-authority-partnerships/33361/document/2797';

// Create a test instance with some default options
var test = pa11y({
    parameters: {
        reporter: 'html'

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
            document.getElementById("edit-name").value = "par_authority@example.com";
            document.getElementById("edit-pass").value = "TestPassword";
            document.getElementById("edit-submit").click();
        }, function () {
            waitUntil(function () {
                // Wait until the login has been success and the /news.html has loaded
                return window.location.href === 'http://localhost:8111/dv/rd-dashboard';
            }, 20, function () {
                // Redirect to the page test page
                page.evaluate(function () {
                    window.location.href = url;
                });
                waitUntil(function () {
                    // Wait until the page has been loaded before running pa11y
                    return window.location.href === url;
                }, 20, next);
            });
        });
    }
});

// Test http://example.com/
test.run('localhost:8111/user/login', function (error, result) {
    if (error) {
        return console.error(error.message);
    }
    console.log(result);
});
