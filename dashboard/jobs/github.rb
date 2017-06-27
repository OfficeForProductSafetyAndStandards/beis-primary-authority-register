require 'octokit'

#stack = Faraday::RackBuilder.new do |builder|
#  builder.response :logger
#  builder.use Octokit::Response::RaiseError
#  builder.adapter Faraday.default_adapter
#end
#Octokit.middleware = stack
#Octokit.user 'kalpaitch'

SCHEDULER.every('4m', first_in: '1s') {
  client = Octokit::Client.new()
  organization = "TransformCore"
  repos = ["beis-par-beta"]
  pulls = []
  commits = []

  open_pull_requests = repos.inject([]) { |pulls, repo|
    client.pull_requests("#{organization}/#{repo}", :state => 'open').first(5).each do |pull|
      pulls.push({
        number: pull.number,
        title: pull.title,
        repo: repo,
        created_at: pull.created_at.strftime("%b %-d %Y, %l:%m %p"),
        updated_at: pull.updated_at.strftime("%b %-d %Y, %l:%m %p"),
        creator: "@" + pull.user.login,
        })
    end
    pulls[0..4]
  }

  latest_commits = repos.inject([]) { |commits, repo|
      client.commits("#{organization}/#{repo}", 'master').first(5).each do |commit|

        commits.push({
          number: commit.sha,
          message: commit.commit.message,
          created_at: commit.commit.committer.date,
          creator: "@" + commit.committer.login,
          })
      end
      commits[0..4]
    }

  send_event('github_latest_prs', { header: "Open Pull Requests", pulls: open_pull_requests })
  send_event('github_latest_commits', { header: "Latest Commits", commits: latest_commits })
}
