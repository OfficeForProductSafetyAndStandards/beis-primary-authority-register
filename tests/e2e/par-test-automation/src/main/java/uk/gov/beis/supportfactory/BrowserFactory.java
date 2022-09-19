package uk.gov.beis.supportfactory;

import java.util.Arrays;
import java.util.HashMap;
import java.util.List;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebDriverException;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.ie.InternetExplorerDriver;
import org.openqa.selenium.ie.InternetExplorerOptions;
import org.openqa.selenium.remote.DesiredCapabilities;

import uk.gov.beis.enums.Browser;
import uk.gov.beis.helper.LOG;
import uk.gov.beis.helper.PropertiesUtil;

/**
 * This class captures all the capabilities specific to the browser for the
 * webdriver instance is used
 */
public class BrowserFactory {

	public static String CHROME_OPTIONS_KEY = "chrome.options";
	// public static Properties DRIVER_PROPERTIES =
	// ReadSharedPropertyFile.loadSharedDriverProperties();
	public static String LINUX_CHROME_EXECUTABLE = PropertiesUtil.getSharedPropertyValue("linux.chrome.executable");
	public static Browser browser = Browser.valueOf(System.getProperty("browser", PropertiesUtil.getConfigPropertyValue("browser")));
	static String desiredBrowserVersion = "browserVersion";
	private static String CHROMEDRIVER_LINUX = PropertiesUtil.getSharedPropertyValue("linux.chrome.driver.path");

	private static String CHROMEDRIVER_WINDOWS = PropertiesUtil.getSharedPropertyValue("windows.chrome.driver.path");
	
	private static String IEDRIVER_WINDOWS = PropertiesUtil.getSharedPropertyValue("windows.internetexplorer.driver.path");

	public static DesiredCapabilities selectBrowser(DesiredCapabilities caps) {
		switch (browser) {
		case Chrome:
			caps.setCapability("browserName", "chrome");
			break;
		case Firefox:
			caps.setCapability("browserName", "firefox");
			break;
		case IE:
			caps.setCapability("browserName", "internet_explorer");
		default:
			throw new WebDriverException("No browser specified");
		}
		caps.setCapability("version", desiredBrowserVersion);
		return caps;
	}

	public static WebDriver selectLocalBrowser(DesiredCapabilities caps) {
		switch (browser) {
		case Chrome:
			System.setProperty("webdriver.chrome.driver", CHROMEDRIVER_WINDOWS);
			return new ChromeDriver(caps);
		case Chromeheadless:
			ChromeOptions options = new ChromeOptions();
			if(PlatformFactory.platform.equals("Windows")) {
				LOG.info("... Chrome headless: Windows ...");
				System.setProperty("webdriver.chrome.driver", CHROMEDRIVER_WINDOWS);
				options.addArguments(getChromeOptions());
				return new ChromeDriver(options);
			}else {
				LOG.info("... Disabling download prompt and setting download path to project workspace ...");
				HashMap<String, Object> chromePrefs = new HashMap<String, Object>();
				chromePrefs.put("profile.default_content_settings.popups", 0);
				chromePrefs.put("download.default_directory", System.getProperty("user.dir"));
				options.setBinary(LINUX_CHROME_EXECUTABLE);
				List<String> chromeOptions = getChromeOptions();
				options.addArguments(chromeOptions);
				options.setExperimentalOption("prefs", chromePrefs);
				caps.setCapability(ChromeOptions.CAPABILITY, options);
				System.setProperty("webdriver.chrome.driver", CHROMEDRIVER_LINUX);
			}
			return new ChromeDriver(caps);
		case Firefox:
//			return new FirefaoxDriver();
		case IE:
			InternetExplorerOptions ieOptions = new InternetExplorerOptions();
			System.setProperty("webdriver.ie.driver", IEDRIVER_WINDOWS);
			ieOptions = ieOptions.merge(caps);
			ieOptions.setCapability(InternetExplorerDriver.INTRODUCE_FLAKINESS_BY_IGNORING_SECURITY_DOMAINS, true);
			return new InternetExplorerDriver(ieOptions);
		default:
			throw new WebDriverException("No browser specified");
		}
	}

	private static List<String> getChromeOptions() {
		// optionsStr should be a comma separated list
		String OptionsStr = PropertiesUtil.getSharedPropertyValue(CHROME_OPTIONS_KEY);
		// Split string separated by zero or more whitespace
		// followed by comma
		// followed by zero or more whitespace
		return Arrays.asList(OptionsStr.split("\\s*,\\s*"));
	}
	
	public static void changeDriverInstance(String newBrowser) {
		browser = Browser.valueOf(newBrowser);
	}
	
	public static void resetDriverInstance() {
		browser = Browser.valueOf(System.getProperty("browser", PropertiesUtil.getConfigPropertyValue("browser")));
	}
	
	
}
