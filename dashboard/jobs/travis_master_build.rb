require 'travis'

SCHEDULER.every('10s', first_in: '1s') {
  repo = Travis::Repository.find("TransformCore/beis-par-beta")

  build = repo.branch('master')

  if build.green?
    health = 'ok'
  elsif build.yellow?
    health = 'warning'
  else
    health = 'critical'
  end

  info = "[#{build.branch_info}]"
  number = build.number

  send_event('master_build_status', { status: health })
  send_event('master_build_info', { text: info })
  send_event('master_build_version', { text: "##{number}" })
}