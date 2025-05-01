package uk.gov.beis.supportfactory;

import org.openqa.selenium.remote.CapabilityType;
import org.openqa.selenium.remote.DesiredCapabilities;
import uk.gov.beis.enums.Platform;
import uk.gov.beis.helper.LOG;
import uk.gov.beis.helper.PropertiesUtil;


/**
 * This class captures all the capabilities specific to the platform for which the webdriver instance is used
 */
public class PlatformFactory {

    public static String desiredPlatformVersion = "platformVersion";
    public static String platform = System.getProperty("platform", Platform.valueOf(PropertiesUtil.getConfigPropertyValue("platform")).toString());
    
    public static DesiredCapabilities selectPlatform(DesiredCapabilities caps) {

        if (platform.equals("Android")) {
            caps.setCapability("platform", "ANDROID");
            caps.setCapability("deviceOrientation", "landscape");
        } 
        else if (platform.equals("Windows")) {
            LOG.info(" Running tests locally on windows");
            LOG.info(" Setting Windows options: ");
            
            //caps.setCapability(CapabilityType.ACCEPT_SSL_CERTS, true);
            caps.setCapability(CapabilityType.ACCEPT_INSECURE_CERTS, true);
        } 
        else if (platform.equals("Linux")) {
            LOG.info(" Running tests locally on linux");
            LOG.info(" Setting Linux options: ");
            
            caps.setCapability("acceptInsecureCerts", true);
        } 
        else {
            LOG.info(" Platform not specified");
        }
        return caps;
    }
}