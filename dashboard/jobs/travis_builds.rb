require 'travis'

SCHEDULER.every('2m', first_in: '1s') {
  client = Travis::Client.new
  repo  = client.repo("TransformCore/beis-par-beta")
  recent_builds = []

  repo.each_build do |build|
      recent_builds.push({
        number: "##{build.number}",
        branch: "[#{build.branch_info}]",
        value: "#{build.state} in #{build.duration}s",
        state: build.state
      })
  end

  send_event('travis_latest_builds', { items: recent_builds[0..5] })

  client.clear_cache
}
