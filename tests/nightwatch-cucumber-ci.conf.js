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
            screenshots: {
                enabled: true,
                on_failure: true,
                on_error: true,
                path: 'reports/nightwatch/screenshots'
            },
            desiredCapabilities: {
                browserName: 'chrome',
                resolution: '1280x3000',
                chromeOptions: {
                    binary: '/usr/bin/google-chrome',
                    args: ['--verbose', '--headless', '--no-sandbox', '--disable-gpu', '--disable-dev-shm-usage', '--window-size=1280,3000'],
                },
                javascriptEnabled: true,
                acceptSslCerts: true
            },
            selenium: {
                cli_args: {
                    'webdriver.chrome.driver': '/usr/local/bin/chromedriver'
                }
            }
        }
    }
}
