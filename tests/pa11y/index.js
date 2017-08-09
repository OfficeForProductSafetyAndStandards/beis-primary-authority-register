// An example of running Pa11y programmatically
'use strict';

var pa11y = require('pa11y');
var url = 'http://localhost:8111/dv/partnership-dashboard';

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
            document.getElementById("edit-name").value = "par_helpdesk@example.com";
            document.getElementById("edit-pass").value = "TestPassword";
            document.getElementById("edit-submit").click();
        }, function () {
            waitUntil(function () {
                // Wait until the login has been success and the /news.html has loaded
                return window.location.href === 'http://localhost:8111/user/65';
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



DV-transition-journey-3

PAO-transition-journey-1

Video Demonstrations

Test Reports

Ways of Working

Weekend warriors

Beta NFRs

Beta Project Walls - Birmingham and London

Beta Reporting and Analytics

Beta Stakeholder engagement for 'Transition'

    Beta User Research

Draft Statutory Guidance for Primary Authority from 1st October 2017

Glossary of terms

PAR UX

Transition project

File lists

How to print at 1VS and Victoria House in Birmingham

Maps to show PA partnership numbers

PAR Alpha

Provided research

Pages to delete

    User Administration

Meeting notes (2)

Final Consultation Document 7th August 2017

PAR Beta...The User Journeysrestrictions.none Watching Watching
The User Journeys
User icon
Paul Littlebury
Last modified just a moment ago
Feature tests

Feature tests
LikeBe the first to like this
No labelsEdit Labels
User icon
Write a comment…
