package uk.gov.beis.stepdefs;

import java.time.Duration;
import java.util.List;

import org.openqa.selenium.WebDriver;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import cucumber.api.Scenario;
import cucumber.api.java.After;
import cucumber.api.java.Before;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.supportfactory.WebdriverFactory;

public class Hooks {

	public static WebDriver driver;

	private Logger LOG;
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
		
		driver.manage().timeouts().pageLoadTimeout(Duration.ofSeconds(10));
		driver.manage().timeouts().implicitlyWait(Duration.ofSeconds(10));
		driver.manage().window().maximize();
		
		ScenarioContext.lastDriver = driver;
	}

	@After
	public void closeBrowser(Scenario scenario) throws Exception, Throwable {
		if (scenario.isFailed()) {
			LOG.info("Doing After Method routine");
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
