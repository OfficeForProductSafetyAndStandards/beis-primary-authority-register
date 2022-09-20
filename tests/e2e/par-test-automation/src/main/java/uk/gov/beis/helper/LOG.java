package uk.gov.beis.helper;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class LOG {
	private Logger LOGGER = LoggerFactory.getLogger(LOG.class);
	private static LOG instance;
	public LOG() {
		
	}
	
	private static LOG instance() {
		return instance == null ? (instance = new LOG()) : instance;
	}
	
	public static void info(String toLog) {
		instance().LOGGER.info(addDots(toLog));
	}
	
	public static void error(String toLog) {
		instance().LOGGER.error(addDots(toLog));
	}
	
	private static String addDots(String toLog) {
		return "... "+toLog+" ...";
	}
}
