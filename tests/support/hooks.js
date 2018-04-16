const { client } = require('nightwatch-cucumber')
const reporter = require('cucumber-html-reporter');
var path = require('path');
var {After, AfterAll, Before, BeforeAll} = require('cucumber');

BeforeAll(function () {
    return client
        .url(client.launch_url + '/user/login')
        .setValue('#edit-name', 'dadmin')
        .setValue('#edit-pass', 'TestPassword')
        .click('#edit-submit')
        .url(client.launch_url + '/admin/par-data-test-reset')
        .url(client.launch_url + '/user/logout')
});

After(function () {
    return client
        .deleteCookies(function() {
            client.end();
          });});
