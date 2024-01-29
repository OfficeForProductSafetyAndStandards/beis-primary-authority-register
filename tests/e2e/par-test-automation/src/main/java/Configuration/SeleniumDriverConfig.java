package Configuration;

import java.time.Duration;
import java.util.Arrays;
import java.util.List;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.firefox.FirefoxOptions;
import org.openqa.selenium.ie.InternetExplorerDriver;

import uk.gov.beis.enums.Browser;
import uk.gov.beis.helper.LOG;
import uk.gov.beis.helper.PropertiesUtil;

public class SeleniumDriverConfig {
	
	public WebDriver driver;
	
	private static String CHROME_OPTIONS_KEY = "chrome.options";
	private static String FireFox_OPTIONS_KEY = "firefox.options";
	
	private static String CHROMEDRIVER_WINDOWS = PropertiesUtil.getSharedPropertyValue("windows.chrome.driver.path");
	private static String GECKODRIVER_WINDOWS = PropertiesUtil.getSharedPropertyValue("windows.gecko.driver.path");
	private static String IEDRIVER_WINDOWS = PropertiesUtil.getSharedPropertyValue("windows.internetexplorer.driver.path");
	
	public SeleniumDriverConfig(Browser browserType, int pageLoadInSeconds, int implicitWaitInSeconds) {
		
		DriverSetUp(browserType, pageLoadInSeconds, implicitWaitInSeconds);
	}
	
	private void DriverSetUp(Browser browserType, int pageLoadInSeconds, int implicitWaitInSeconds) {
		
		driver = SetDriver(browserType);
		
		driver.manage().timeouts().pageLoadTimeout(Duration.ofSeconds(pageLoadInSeconds));
		driver.manage().timeouts().implicitlyWait(Duration.ofSeconds(implicitWaitInSeconds));
		driver.manage().window().maximize();
	}
	
	private WebDriver SetDriver(Browser browserType) {
		switch(browserType) {
		case Chrome:
			LOG.info("... Chrome: Windows ...");
			System.setProperty("webdriver.chrome.driver", CHROMEDRIVER_WINDOWS);
			
			return new ChromeDriver();
			
		case Firefox:
			LOG.info("... Firefox : Windows ...");
			System.setProperty("webdriver.gecko.driver", GECKODRIVER_WINDOWS);
			return new FirefoxDriver();
			
		case IE:
			LOG.info("... IE headless: Windows ...");
			System.setProperty("webdriver.ie.driver", IEDRIVER_WINDOWS);
			return new InternetExplorerDriver();
			
		case Chromeheadless:
			LOG.info("... Chrome headless: Windows ...");
			System.setProperty("webdriver.chrome.driver", CHROMEDRIVER_WINDOWS);
			
			ChromeOptions options = new ChromeOptions();
			options.addArguments(getBrowserOptions(PropertiesUtil.getSharedPropertyValue(CHROME_OPTIONS_KEY)));
			
			return new ChromeDriver(options);
			
		case Firefoxheadless:
			LOG.info("... Firefox headless: Windows ...");
			System.setProperty("webdriver.gecko.driver", GECKODRIVER_WINDOWS);
			
			FirefoxOptions fireOptions = new FirefoxOptions();
			fireOptions.addArguments(getBrowserOptions(PropertiesUtil.getSharedPropertyValue(FireFox_OPTIONS_KEY)));
			
			return new FirefoxDriver(fireOptions);
			
		default:
			return new ChromeDriver();
		}
	}
	
	private static List<String> getBrowserOptions(String options) {
		// Split string separated by zero or more whitespace, followed by comma, followed by zero or more whitespace.
		return Arrays.asList(options.split("\\s*,\\s*"));
	}
}
