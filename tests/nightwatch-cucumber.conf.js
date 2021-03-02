const seleniumServer = require('selenium-server')
const chromedriver = require('chromedriver')
const electron = require('electron-prebuilt');

require('nightwatch-cucumber')({
    cucumberArgs: [
            '--require', 'step-definitions-v1',
            '--require', 'support',
            '--format', 'node_modules/cucumber-pretty',
            '--format', 'json:reports/cucumber.json',
            'features-v1'
        ]
})

module.exports = {
    output_folder: 'reports',
    custom_assertions_path: 'step-definitions-v1/assertions',
    page_objects_path: 'step-definitions-v1/page_objects',
    live_output: false,
    disable_colors: false,
    selenium: {
        start_process: true,
        server_path: seleniumServer.path,
        log_path: '',
        host: '127.0.0.1',
        port: 4444
    },
    test_settings: {
        default: {
            launch_url: 'http://localhost:8111',
            selenium_port: 4444,
            selenium_host: '127.0.0.1',
            screenshots : {
                enabled : true,
                on_failure : true,
                path: './reports/screenshots'
            },
            desiredCapabilities: {
                browserName: 'chrome',
                resolution: '1280x3000',
                chromeOptions : {
                //  binary: electron,
                    args: ['--headless', '--window-size=1280,3000'],
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
        },
    }
}
