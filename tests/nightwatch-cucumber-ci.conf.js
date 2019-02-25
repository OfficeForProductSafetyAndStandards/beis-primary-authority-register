const seleniumServer = require('selenium-server')
const chromedriver = require('chromedriver')
const electron = require('electron-prebuilt');

require('nightwatch-cucumber')({
    cucumberArgs: [
            '--require', 'step-definitions', 
            '--require', 'support', 
            '--require', 'page_objects', 
            '--format', 'node_modules/cucumber-pretty', 
            '--format', 'json:reports/cucumber.json', 
            'features'
        ]
})

module.exports = {
    output_folder: 'reports/nightwatch',
    custom_assertions_path: 'step-definitions/assertions',
    page_objects_path: 'step-definitions/page_objects',
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
            launch_url: 'http://par.localhost',
            selenium_port: 4444,
            // selenium_host: 'localhost',
            screenshots : {
                enabled : true,
                on_failure : true,
                path: 'reports/nightwatch/screenshots'
            },
            desiredCapabilities: {
                browserName: 'chrome',
                chromeOptions : {
                    binary: '/usr/bin/google-chrome',
                    args: ['--headless', '--no-sandbox', '--disable-gpu', '--window-size=1280,1280'],
                },
                javascriptEnabled: true,
                acceptSslCerts: true
            },
            selenium: {
                cli_args: {
                    'webdriver.chrome.driver': chromedriver.path
                }
            }
        }
    }
}
