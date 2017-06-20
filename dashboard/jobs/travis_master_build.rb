require 'travis'

SCHEDULER.every('10s', first_in: '1s') {
  repo = Travis::Repository.find("TransformCore/beis-par-beta")

  build = repo.branch('master')

  health = build.state
  info = "[#{build.branch_info}]"
  number = build.number

  send_event('master_build_status', { text: health.capitalize })
  send_event('master_build_info', { text: info })
  send_event('master_build_version', { current: number })
}