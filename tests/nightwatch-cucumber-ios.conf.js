const seleniumServer = require('selenium-server')
const chromedriver = require('chromedriver')

require('nightwatch-cucumber')({
    cucumberArgs: [
            '--require', 'step-definitions', 
            '--require', 'support', 
            '--require', 'page_objects', 
            '-- --tag', 'ci',
            '--format', 'node_modules/cucumber-pretty', 
            '--format', 'json:reports/cucumber.json', 
            'features/0-create-partnerships.feature'
        ]
})

module.exports = {
    output_folder: 'reports',
    custom_assertions_path: 'step-definitions/assertions',
    page_objects_path: 'step-definitions/page_objects',
    globals_path : 'step-definitions/globals/globalModules.js',
    live_output: false,
    disable_colors: false,
    selenium: {
        start_process: false,
        server_path: seleniumServer.path,
        log_path: '',
        host: '127.0.0.1',
        port: 4444
    },
    appium: {
        start_process: true
    },
    test_settings: {
        default: {
            launch_url: 'http://localhost:8111',
            selenium_start_process: true,
            selenium_port: 4723,
            selenium_host: '127.0.0.1',
            silent: true,
            desiredCapabilities : {
              browserName : 'Safari',
              automationName: 'XCUITest',
              platformName : 'iOS',
              platformVersion : '11.2',
              deviceName : 'iPad Air 2',
            //   noReset: true
              // "app": APP_PATH + "ios/PieDrive.app", // path for the ios app you want to test
            },
            screenshots : {
                enabled : true,
                on_failure : true,
                path: './reports/screenshots'
            },
            selenium: {
                cli_args: {
                    'webdriver.chrome.driver': chromedriver.path
                }
            }
        },
    }
}