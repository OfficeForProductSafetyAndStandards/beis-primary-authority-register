require 'faraday'
require 'benchmark'

class DashboardHttpErrors < Faraday::Middleware
  def call(env)
    begin
      @app.call(env)
    rescue Faraday::Error::ConnectionFailed => e
      # Do whatever to handle your error here, maybe raise a more semantic error
    end
  end
end

endpoint = ENV['STAGING_ENDPOINT']

SCHEDULER.every('30s', first_in: '1s') {
  conn = Faraday.new(:url => endpoint || 'http://par.localhost') do |c|
    c.use DashboardHttpErrors
    c.use Faraday::Adapter::NetHttp
  end

  response = conn.get '/build_version.txt'
  if response && response.success? && !response.body.nil?
    version = response.body
  end

  send_event('staging_build_version', { text: version })
}