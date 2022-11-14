package uk.gov.beis.utility;

import org.apache.commons.lang3.RandomStringUtils;

public class RandomStringGenerator {

	public static String getBusinessName(int length) {
		String BUSINESSID = "Test Business ";
		String randomNumber = RandomStringUtils.random(length, "123456789");
		return BUSINESSID+randomNumber;
	}
	
	public static String getEmail(int length) {
		String EMAILID = "testemail";
		String randomNumber = RandomStringUtils.random(length, "123456789");
		return EMAILID+randomNumber+"@gmail.com";
	}

	public static String getRandomAlpahNumericString(int length) {
		String randomString = RandomStringUtils.random(length, "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
		return randomString;
	}

	public static String getRandomString(int length) {
		return RandomStringUtils.random(length, "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	}

}
