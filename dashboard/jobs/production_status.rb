require 'faraday'
require 'benchmark'

def time_diff(start, finish)
   (finish - start) * 1000.0
end

concurrent_users = 0
response_time = 0

SCHEDULER.every('2s', first_in: '1s') {
  conn = Faraday.new(:url => 'http://par.localhost')
  time = Benchmark.realtime do |health|
    response = conn.get '/health'
  end
  new_response_time = (time*1000).to_i

  response = conn.get '/health'
  if response.body.nil?
    health = 'critical'
  elsif
    health = 'ok'
  end

  response = conn.get '/build_version.txt'
  version = response.body

  new_concurrent_users = rand(100)

  send_event('production_build_version', { text: version })
  send_event('production_health', { status: health })
  send_event('production_load',   { current: new_concurrent_users, last: concurrent_users })
  send_event('production_response_time',   { current: new_response_time, last: response_time })

  concurrent_users = new_concurrent_users
  response_time = new_response_time
}