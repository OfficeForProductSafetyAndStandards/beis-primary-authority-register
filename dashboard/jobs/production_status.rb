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

def time_diff(start, finish)
   (finish - start) * 1000.0
end

concurrent_users = 0
response_time = 0
endpoint = 'https://par-beta-test.cloudapps.digital'

SCHEDULER.every('4s', first_in: '1s') {
  conn = Faraday.new(:url => endpoint || 'http://par.localhost') do |c|
    c.use DashboardHttpErrors
    c.use Faraday::Adapter::NetHttp
  end

  time = Benchmark.realtime do |health|
    response = conn.get '/health'
  end
  new_response_time = (time*1000).to_i

  response = conn.get '/health'
  if response && response.success? && !response.body.nil?
    health = 'ok'
  elsif
    health = 'critical'
  end

  response = conn.get '/build_version.txt'
  if response && response.success? && !response.body.nil?
    version = response.body
  end

  new_concurrent_users = rand(100)

  send_event('production_build_version', { text: version })
  send_event('production_health', { status: health })
  send_event('production_load',   { current: new_concurrent_users, last: concurrent_users })
  send_event('production_response_time',   { current: new_response_time, last: response_time })

  concurrent_users = new_concurrent_users
  response_time = new_response_time
}