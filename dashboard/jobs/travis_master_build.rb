require 'travis'
require 'aws-sdk'
require 'json'
require 'nokogiri'

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
  accessibility_results = []

  build = repo.branch('master')

  if build.green?
    health = 'ok'
  elsif build.yellow?
    health = 'pending'
  else
    health = 'critical'
  end

  info = "[#{build.branch_info}]"
  number = build.number

  send_event('master_build_status', { status: health })
  send_event('master_build_version', { text: "##{number}" })

  # Get cucumber test results.
  begin
    test_file = 'tests/' + "#{number}" + '/report.json'
    if (bucket.objects[test_file].exists?)
      functional_tests = JSON.parse(bucket.objects[test_file].read)
      test_results.push({
        heading: "Functional Tests",
        count: functional_tests['suites'].count | 0,
        passed: functional_tests['state']['passed'],
        failed: functional_tests['state']['failed'],
        skipped: functional_tests['state']['skipped'],
      })
    end
  rescue
    # exists? can raise an error `Aws::S3::Errors::Forbidden`
  end

  # Get phpunit test results.
  begin
    test_file = 'tests/' + "#{number}" + '/phpunit.latest.xml'
    if (bucket.objects[test_file].exists?)
      unit_tests = Nokogiri::XML(bucket.objects[test_file].read)
      unit_tests.xpath("/testsuites/testsuite").each do |testsuite|
        test_results.push({
          heading: "Unit Tests (#{testsuite.attr('name')})",
          count: testsuite.attr('tests'),
          passed: testsuite.attr('assertions'),
          failed: testsuite.attr('failures'),
          skipped: testsuite.attr('errors'),
        })
      end
    end
  rescue
    # exists? can raise an error `Aws::S3::Errors::Forbidden`
  end

  send_event('master_test_results', { results: test_results })

  client.clear_cache

  # Get the Pa11y accessibility reports.
  accessible = 'pending'
  begin
    test_file = 'tests/' + "#{number}" + '/wcag2aa_report.json'
    if (bucket.objects[test_file].exists?)
      accessibility_tests = JSON.parse(bucket.objects[test_file].read)
      if (bucket.objects[test_file].exists?)
        accessible = 'ok'
        accessibility_message = "No errors found on #{accessibility_tests['total']} tests"
      else
        accessible = 'critical'
        accessibility_message = "#{accessibility_tests['errors']} errors found on #{accessibility_tests['total']} tests"
      end
    end
  rescue
    # exists? can raise an error `Aws::S3::Errors::Forbidden`
  end

  send_event('master_accessibility_status', { status: accessible, message: accessibility_message })
}
