package uk.gov.beis.test;

import org.junit.BeforeClass;
import org.junit.runner.RunWith;

import cucumber.api.CucumberOptions;
import cucumber.api.junit.Cucumber;

@RunWith(Cucumber.class)
@CucumberOptions(strict = false, features = { "classpath:features" }, glue = { "uk.gov.beis.stepdefs" }, 
				tags = { "@legalEntities" }, plugin = { "json:target/cucumber-report/report.json" })
public class PARTestRunner {
	@BeforeClass
	public static void setUp() {
		
	}
}