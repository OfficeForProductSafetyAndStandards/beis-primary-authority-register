package uk.gov.beis.helper;

import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

/**
 * Class to load property files for SUT on a per environment basis - if no
 * configuration passed, default environment used will be DEFAULT_ENVIRONMENT
 * variable
 */
public class PropertiesUtil {
	
	private final static String DEFAULT_ENVIRONMENT = "par";
	
	private static Properties configProperties = new Properties();
	private static Properties sharedProperties = new Properties();
	private static Properties buildProperties = new Properties();
	
	private final static String sharedConfigFile = "shared-driver.properties";
	private final static String buildPropertyFile = "filtered/maven-build.properties";
	
	private static String configFile;
	public static String environment = "";
	
	static {
		environment = (System.getProperty("env") != null ? System.getProperty("env") : DEFAULT_ENVIRONMENT);
		configFile = environment + "-config.properties";
		LOG.info("Loading environment config file:" + configFile);
		ClassLoader loader = Thread.currentThread().getContextClassLoader();
		loadResource(loader, configFile, configProperties);
		loadResource(loader, sharedConfigFile, sharedProperties);
	}
	
	private static void loadResource(ClassLoader loader, String file, Properties destination) {
		try (InputStream resourceStream = loader.getResourceAsStream(file)) {
			destination.load(resourceStream);
		} catch (IOException e) {
			throw new ExceptionInInitializerError(file + " failed to load.");
		} catch (NullPointerException nul) {
			throw new ExceptionInInitializerError(file + " not found. Please create this configuration file");
		}
	}

	public static String getConfigPropertyValue(String key) {
		String prop = configProperties.getProperty(key);
		if (prop == null) {
			throw new NullPointerException("No information has been configured for '" + key + "' in the configuration file: " + configFile);
		}
		return prop.trim();
	}
	
	public static String getCredentials(String account) {
		return getConfigPropertyValue(account);
	}
	
	public static String getSharedPropertyValue(String key) {
		return sharedProperties.getProperty(key);
	}

}
