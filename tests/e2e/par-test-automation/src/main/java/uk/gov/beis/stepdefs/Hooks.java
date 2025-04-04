package uk.gov.beis.stepdefs;

import java.util.List;

import org.openqa.selenium.WebDriver;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import Configuration.SeleniumDriverConfig;

import io.cucumber.java.*;

import uk.gov.beis.enums.Browser;
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
		
		driver = new SeleniumDriverConfig(Browser.Chrome, 15, 15).driver;
		
		ScenarioContext.lastDriver = driver;
	}

	@After
	public void closeBrowser(Scenario scenario) throws Exception, Throwable {
		if (scenario.isFailed()) {
			LOG.info("Doing After Method routine");
			driver.close();
			driver.quit();
		} else {
			LOG.info("... Shutting down gracefully...");
			driver.close();
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
