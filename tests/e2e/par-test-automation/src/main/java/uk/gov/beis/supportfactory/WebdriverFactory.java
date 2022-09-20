package uk.gov.beis.supportfactory;

import java.net.URL;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebDriverException;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.remote.RemoteWebDriver;

import uk.gov.beis.helper.LOG;
import uk.gov.beis.helper.PropertiesUtil;

/**
 * This class determines whether selenium is going to be run remotely i.e.
 * selenium server OR run locally
 */
public class WebdriverFactory {

	// create the webdriver instance

	public static WebDriver createWebdriver() {
		caps = new DesiredCapabilities();
		String seleniumEnv = PropertiesUtil.getConfigPropertyValue("seleniumEnv");

		// if selenium run locally, set the appropriate capabilities
		if (seleniumEnv.equals("local")) {
			LOG.info(" Calling select_local_browser routine");
			caps = PlatformFactory.selectPlatform(caps);
			return BrowserFactory.selectLocalBrowser(caps);

			// if selenium run on remote server, set the appropriate capabilities
		} else {
			if (seleniumEnv.equals("browserstack")) {
				// do browserstack stuf
			}
			PlatformFactory.selectPlatform(caps);
			BrowserFactory.selectBrowser(caps);
			WebdriverFactory.caps.merge(additionalCapabilities);
			String seleniumHub = "seleniumhub";
			try {
				return new RemoteWebDriver(new URL(seleniumHub), caps);
			} catch (WebDriverException e) {
				// don't care for now
			} catch (Exception e) {
				// don't care for now
			} finally {
				// don't care for now
			}
		}
		return null;
	}

	public static void checkBrowserRequired(String driverChange) {
		if(driverChange != null) {
			BrowserFactory.changeDriverInstance(driverChange);
		}else {
			BrowserFactory.resetDriverInstance();
		}
	}

	public static DesiredCapabilities caps;
	public static DesiredCapabilities additionalCapabilities = new DesiredCapabilities();
}
