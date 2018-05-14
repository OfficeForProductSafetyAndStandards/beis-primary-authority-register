const seleniumServer = require('selenium-server')
const chromedriver = require('chromedriver')

require('nightwatch-cucumber')({
    cucumberArgs: [
        '--require', 'step-definitions', 
        '--require', 'support', 
        '--format', 'node_modules/cucumber-pretty', 
        '--format', 'json:reports/cucumber.json', 
        'features'
    ]
})

module.exports = {
    output_folder: 'reports',
    custom_assertions_path: 'step-definitions/assertions',
    page_objects_path: 'step-definitions/page_objects',
    live_output: false,
    disable_colors: false,
    test_workers: {
        enabled: true,
        workers: 3
    },
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
                path: 'reports/screenshots'
            },
            desiredCapabilities: {
                browserName: 'chrome',
                chromeOptions : {
                    //  binary: electron,
                        args: ['--headless', '--window-size=1280,1280'],
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