package uk.gov.beis.stepdefs;

import java.lang.reflect.Field;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Optional;

import org.openqa.selenium.Dimension;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import cucumber.api.Scenario;
import cucumber.api.java.After;
import cucumber.api.java.Before;
import cucumber.runtime.ScenarioImpl;
import gherkin.formatter.model.Result;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.supportfactory.WebdriverFactory;

public class Hooks {

	public static WebDriver driver;

	private Logger LOG;
	private Date testCaseStartTime;
	private List<String> tag;
	public static Scenario scenario;

	@Before
	public void testSetUp(Scenario scenario) throws Exception {
		Hooks.scenario = scenario;
		LOG = LoggerFactory.getLogger(Hooks.class);
		tag = (List<String>) scenario.getSourceTagNames();
		WebdriverFactory.checkBrowserRequired(isDifferentDriverRequired());
		LOG.info("... Doing BeforeMethod createdriver routine...");
		driver = WebdriverFactory.createWebdriver();
		driver.manage().window().setSize(new Dimension(1840, 1200));
		ScenarioContext.lastDriver = driver;
	}

	@After
	public void closeBrowser(Scenario scenario) throws Exception, Throwable {
		if (scenario.isFailed()) {
			LOG.info("... Doing After Method routine (clear session cookies user from DMS as result of/ failure)...");
			driver.quit();
		} else {
			LOG.info("... Shutting down gracefully...");
			driver.quit();
		}
	}

	private String isDifferentDriverRequired() {
		if (tag.contains("@IEDriver")) {
			LOG.info("IE Driver is required for this test");
			return "IE";
		} else if (tag.contains("@ChromeDriver")) {
			LOG.info("ChromeDriver is required for this test");
			return "Chrome";
		} else {
			return null;
		}
	}

	public static boolean scenarioHasTag(String tag) {
		return scenario.getSourceTagNames().contains(tag);
	}
}
