package Configuration;

import java.net.MalformedURLException;
import java.net.URL;
import java.time.Duration;
import java.util.Arrays;
import java.util.List;
import java.io.IOException;
import io.cucumber.java.en.*;

import io.github.bonigarcia.wdm.WebDriverManager;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.firefox.FirefoxOptions;
import org.openqa.selenium.ie.InternetExplorerDriver;

import org.openqa.selenium.remote.RemoteWebDriver;
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

       // WebDriverManager.chromedriver().setup();

        driver.manage().timeouts().pageLoadTimeout(Duration.ofSeconds(pageLoadInSeconds));
		driver.manage().timeouts().implicitlyWait(Duration.ofSeconds(implicitWaitInSeconds));

		driver.manage().window().maximize();
	}

	private WebDriver SetDriver(Browser browserType) {
		switch(browserType) {
            case Chrome:
                LOG.info("... Chrome: Windows ...");
                //System.setProperty("webdriver.chrome.driver", CHROMEDRIVER_WINDOWS);

                WebDriverManager.chromedriver().setup();
                ChromeOptions options = new ChromeOptions();
                options.addArguments("--no-sandbox");
                options.addArguments("--disable-dev-shm-usage");
                options.addArguments("--disable-gpu");
                options.addArguments("--headless=new");
                options.addArguments("--window-size=1920,1080");

            WebDriverManager.chromedriver().setup();

            return new ChromeDriver();

            case ChromeDocker:
                LOG.info("... Chrome: Windows ...");

               ChromeOptions options1 = new ChromeOptions();
                options1.addArguments("--no-sandbox");
                options1.addArguments("--disable-dev-shm-usage");
                options1.addArguments("--disable-gpu");
                options1.addArguments("--headless=new");

                //try {
                //  WebDriver ChromeDriver = new RemoteWebDriver(new URL("http://selenium:4444/wd/hub"), options);

                // return new ChromeDriver();
                // } catch (MalformedURLException e) {
                //    throw new RuntimeException("Invalid Selenium URL: ", e);
                //}

              for (int i = 0; i < 5; i++) {
                    try {
                        driver = new RemoteWebDriver(new URL("http://selenium:4444/wd/hub"), new ChromeOptions());


                        break;
                    } catch (Exception e) {
                        System.out.println("Retrying WebDriver connection...");
                        try {
                            Thread.sleep(1000);
                       } catch (InterruptedException ee) {
                            throw new RuntimeException(ee);
                       }
                    }
               }
                return driver;


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

			ChromeOptions chromeoptions = new ChromeOptions();
            chromeoptions.addArguments(getBrowserOptions(PropertiesUtil.getSharedPropertyValue(CHROME_OPTIONS_KEY)));

			return new ChromeDriver(chromeoptions);

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
