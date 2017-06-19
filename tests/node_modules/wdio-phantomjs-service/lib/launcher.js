'use strict'
const phantomjs = require('phantomjs-prebuilt')
const paramCase = require('change-case').paramCase

const dict = {
  diskCacheEnabled: 'disk-cache',
  autoLoadImages: 'load-images',
  offlineStoragePath: 'local-storage-path',
  offlineStorageDefaultQuota: 'local-storage-quota',
  localToRemoteUrlAccessEnabled: 'local-to-remote-url-access',
  webSecurityEnabled: 'web-security',
  printDebugMessages: 'debug'
}

module.exports = class PhantomJSLauncher {
  onPrepare (config) {
    const opts = Object.assign({ webdriver: 4444 }, config.phantomjsOpts)
    const args = Object.keys(opts)
      .map(key => `--${dict[key] || paramCase(key)}=${opts[key]}`)

    return phantomjs.run.apply(phantomjs, args).then(p => (this.process = p))
  }

  onComplete () {
    if (this.process) this.process.kill()
  }
}
