#usage: ./generate_files_from_jtl.sh [file-prefix]
#example: ./generate_files_from_jtl.sh my_test
# See http://jmeter-plugins.org/wiki/JMeterPluginsCMD/ for documentation

cmd="java -jar apache-jmeter-3.3/lib/ext/CMDRunner.jar --tool Reporter --input-jtl report.jtl"
x
png="$cmd --generate-png"
$cmd --generate-csv results/$1_aggregate-report.csv --plugin-type AggregateReport
$png ../reports/jmeter/$1_bytes-throughput-over-time.png --plugin-type BytesThroughputOverTime
$png ../reports/jmeter/$1_hits-per-second.png --plugin-type HitsPerSecond
$png ../reports/jmeter/$1_latencies-over-time.png --plugin-type LatenciesOverTime
$png ../reports/jmeter/$1_response-times-distribution.png --plugin-type ResponseTimesDistribution
$png ../reports/jmeter/$1_response-times-percentiles.png --plugin-type ResponseTimesPercentiles
$png ../reports/jmeter/$1_throughput-vs-threads.png --plugin-type ThroughputVsThreads
$png ../reports/jmeter/$1_response-codes-per-second.png --plugin-type ResponseCodesPerSecond
$png ../reports/jmeter/$1_transactions-per-second.png --plugin-type TransactionsPerSecond
