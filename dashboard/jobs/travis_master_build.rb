require 'travis'
require 'aws-sdk'
require 'json'

aws_access_key = ENV['AWS_ACCESS_KEY']
aws_secret_key = ENV['SECRET_ACCESS_KEY']

s3 = AWS::S3.new(
  :access_key_id => aws_access_key,
  :secret_access_key => aws_secret_key
)
bucket = s3.buckets['transform-par-beta-artifacts']

SCHEDULER.every('30s', first_in: '1s') {
  client = Travis::Client.new
  repo  = client.repo("TransformCore/beis-par-beta")

  test_results = []

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
  send_event('master_build_version', { text: "##{number}" })

  functional_tests = bucket.objects['tests/' + "#{number}" + '/report.json']
  if (functional_tests)
    functional_test_results = JSON.parse(functional_tests.read)
    test_results.push({
      heading: "Functional Tests",
      count: functional_test_results['suites'].count | 0,
      passed: functional_test_results['state']['passed'],
      failed: functional_test_results['state']['failed'],
      skipped: functional_test_results['state']['skipped'],
    })
  end

  send_event('master_test_results', { results: test_results })

  client.clear_cache
}