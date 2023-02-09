package uk.gov.beis.test;

import org.junit.BeforeClass;
import org.junit.runner.RunWith;

import cucumber.api.CucumberOptions;
import cucumber.api.junit.Cucumber;
import uk.gov.beis.helper.ScenarioContext;


@RunWith(Cucumber.class)
@CucumberOptions(strict = false, features = { "classpath:features" }, glue = {
		"uk.gov.beis.stepdefs" }, format = {}, tags = {
				"@leo" }, plugin = { "json:target/cucumber-report/report.json" })

// use this class to trigger all the tests

public class PARTestRunner {
	@BeforeClass
	public static void setUp() {
		
	}
}
