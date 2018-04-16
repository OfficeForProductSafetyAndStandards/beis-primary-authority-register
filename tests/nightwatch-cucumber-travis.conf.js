const seleniumServer = require('selenium-server')
const chromedriver = require('chromedriver')
const electron = require('electron-prebuilt');

require('nightwatch-cucumber')({
    cucumberArgs: ['--require', 'step-definitions', '--require', 'support', '--require', 'page_objects', '--format', 'node_modules/cucumber-pretty', '--format', 'json:reports/cucumber.json', 'features']
})

module.exports = {
    output_folder: 'reports',
    custom_assertions_path: '',
    page_objects_path: 'page_objects',
    live_output: false,
    disable_colors: false,
    selenium: {
        start_process: true,
        server_path: seleniumServer.path,
        log_path: '',
        // host: 'localhost',
        port: 4444
    },
    test_settings: {
        default: {
            launch_url: 'http://127.0.0.1:80',
            selenium_port: 4444,
            // selenium_host: 'localhost',
            screenshots : {
                enabled : true,
                on_failure : true,
                path: 'reports/screenshots'
            },
            desiredCapabilities: {
                browserName: 'chrome',
                chromeOptions : {
                    binary: '/usr/bin/google-chrome',
                    args: ['--headless', '--no-sandbox', '--disable-gpu', '--window-size=1200,2000']
                },
                javascriptEnabled: true,
                acceptSslCerts: true
            },
            selenium: {
                cli_args: {
                    'webdriver.chrome.driver': chromedriver.path
                }
            }
        },
        firefox: {
            desiredCapabilities: {
                browserName: 'firefox',
                javascriptEnabled: true,
                acceptSslCerts: true
            }
        }
    }
}