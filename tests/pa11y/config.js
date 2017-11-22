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
        // actions: [
        //     'set field #edit-name to par_helpdesk@example.com',
        //     'set field #edit-pass to TestPassword',
        //     'click element #edit-submit',
        //     'wait for path to not be /user/login',
        //     'click element a.button-start',
        //     'wait for path to not be /welcome-reminder'
        // ],
        page.evaluate(function () {
            document.getElementsByTagName("Log in").click();
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
}
