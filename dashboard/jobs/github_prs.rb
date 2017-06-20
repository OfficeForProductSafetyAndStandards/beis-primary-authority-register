require 'octokit'

SCHEDULER.every '10s', :first_in => 0 do |job|
  client = Octokit::Client.new(:access_token => "6b2ea5805304d73feff275fce90d59f0c5e2a8b7")
  my_organization = "TransformCore"
  repos = client.organization_repositories(my_organization).map { |repo| repo.name }

  open_pull_requests = repos.inject([]) { |pulls, repo|
    client.pull_requests("#{my_organization}/#{repo}", :state => 'open').each do |pull|
      pulls.push({
        title: pull.title,
        repo: repo,
        updated_at: pull.updated_at.strftime("%b %-d %Y, %l:%m %p"),
        creator: "@" + pull.user.login,
        })
    end
    pulls[0..4]
  }

  send_event('github_latest_prs', { header: "Open Pull Requests", pulls: open_pull_requests })
end
