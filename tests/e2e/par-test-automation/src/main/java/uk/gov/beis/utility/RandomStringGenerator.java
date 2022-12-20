package uk.gov.beis.utility;

import org.apache.commons.lang3.RandomStringUtils;

public class RandomStringGenerator {

	public static String getBusinessName(int length) {
		String BUSINESSID = "Test Business ";
		String randomNumber = RandomStringUtils.random(length, "123456789");
		return BUSINESSID+randomNumber;
	}
	
	public static String getAuthorityName(int length) {
		String AUTHID = "Test Authority ";
		String randomNumber = RandomStringUtils.random(length, "123456789");
		return AUTHID+randomNumber;
	}
	
	public static String getEmail(int length) {
		String EMAILID = "testemail";
		String randomNumber = RandomStringUtils.random(length, "123456789");
		return EMAILID+randomNumber+"@gmail.com";
	}
	
	public static String getLegalEntityName(int length) {
		String ent = "Test Legal Entity";
		String randomNumber = RandomStringUtils.random(length, "123456789");
		return ent+randomNumber;
	}

	public static String getRandomNumericString(int length) {
		String randomString = RandomStringUtils.random(length, "123456789");
		return randomString;
	}

	public static String getRandomString(int length) {
		return RandomStringUtils.random(length, "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	}

}
