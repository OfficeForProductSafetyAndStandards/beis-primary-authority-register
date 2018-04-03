const seleniumServer = require('selenium-server')
const chromedriver = require('chromedriver')

require('nightwatch-cucumber')({
    cucumberArgs: ['--require', 'step-definitions', '--require', 'support', '--require', 'page_objects', '--format', 'node_modules/cucumber-pretty', '--format', 'json:reports/cucumber.json', 'features']
})

module.exports = {
    output_folder: 'reports',
    custom_assertions_path: '',
    page_objects_path: 'page_objects',
    live_output: false,
    disable_colors: false,
    test_workers: {
        enabled: true,
        workers: 'auto'
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
            launch_url: 'http://dev.tikkie.me',
            selenium_port: 4444,
            selenium_host: '127.0.0.1',
            screenshots : {
                enabled : true,
                on_failure : true,
                path: 'reports/screenshots'
            },
            desiredCapabilities: {
                browserName: 'chrome',
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