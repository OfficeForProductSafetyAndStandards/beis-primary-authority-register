// An example of running Pa11y programmatically
'use strict';

module.exports = {
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
            document.open('http://localhost:8111/user/login');
            document.getElementById("edit-name").value = "par_helpdesk@example.com";
            document.getElementById("edit-pass").value = "TestPassword";
            document.getElementById("edit-submit").click();
        }, function () {
            waitUntil(function () {
                // Wait until the login has been success and the /news.html has loaded
                return window.location.href === 'http://localhost:8111/welcome-reminder';
            }, 20, next);
        });
    }
}
