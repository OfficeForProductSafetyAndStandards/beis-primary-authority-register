package uk.gov.beis.test;

import org.junit.BeforeClass;
import org.junit.runner.RunWith;

import io.cucumber.junit.Cucumber;
import io.cucumber.junit.CucumberOptions;

@RunWith(Cucumber.class)
@CucumberOptions(dryRun = false, features = { "classpath:features" }, glue = { "uk.gov.beis.stepdefs" }, tags = "@happypath", plugin = { "json:target/cucumber-report/report.json" })
public class PARTestRunner {
	@BeforeClass
	public static void setUp() {
		
	}
}