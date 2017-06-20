SCHEDULER.every '2s' do
  version = 59
  health = 'UP'

  last_load = rand(100)
  current_load = rand(100)

  send_event('production_build_version', { current: version })
  send_event('production_health', { text: health.capitalize })
  send_event('production_load',   { current: current_load, last: last_load })
end