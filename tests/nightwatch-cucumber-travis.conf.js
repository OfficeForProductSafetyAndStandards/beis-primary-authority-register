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
        cli_args : {
            "webdriver.chrome.driver": "node_modules/.bin/chromedriver"
        },
        log_path: '',
        host: '127.0.0.1',
        port: 4444
    },
    test_settings: {
        default: {
            launch_url: 'http://localhost:80',
            selenium_port: 4444,
            selenium_host: '127.0.0.1',
            screenshots : {
                enabled : true,
                on_failure : true,
                path: 'reports/screenshots'
            },
            desiredCapabilities: {
                browserName: 'chrome',
                chromeOptions : {
                //     // binary: electron,
                    args: ['--headless', '--disable-gpu', '--window-size=1280,1280'],
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