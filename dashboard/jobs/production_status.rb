SCHEDULER.every('2s', first_in: '1s') {
  version = 59
  health = 'ok'

  last_load = rand(100)
  current_load = rand(100)

  send_event('production_build_version', { current: version })
  send_event('production_health', { status: health })
  send_event('production_load',   { current: current_load, last: last_load })
}