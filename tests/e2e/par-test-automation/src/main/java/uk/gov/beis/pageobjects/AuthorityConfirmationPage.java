package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class AuthorityConfirmationPage extends BasePageObject {

	public AuthorityConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	String authAddress1 = "//div/p[contains(text(),'?')]";
	String authPCode = "//div/p[contains(text(),'?')]";
	String authorityName = "//div/p[contains(text(),'?')]";
	String authorityType = "//div/p[contains(text(),'?')]";
	String ons = "//div/p[contains(text(),'?')]";
	String regFunc = "//div/p[contains(text(),'?')]";


	public boolean checkAuthorityDetails() {
		WebElement authAddressLine1 = driver
				.findElement(By.xpath(authAddress1.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_ADDRESSLINE1))));
		WebElement authPCode1 = driver
				.findElement(By.xpath(authPCode.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_POSTCODE))));
		WebElement authName = driver.findElement(
				By.xpath(authorityName.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_NAME))));
		WebElement authType = driver.findElement(
				By.xpath(authorityType.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE))));
		WebElement onscode = driver.findElement(
				By.xpath(ons.replace("?", DataStore.getSavedValue(UsableValues.ONS_CODE))));
		WebElement regFunc1 = driver.findElement(
				By.xpath(regFunc.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION))));

		return (authAddressLine1.isDisplayed() && authPCode1.isDisplayed()
				&& authName.isDisplayed() && authType.isDisplayed() && onscode.isDisplayed()
				&& regFunc1.isDisplayed());
	}
	
	public AuthorityDashboardPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}
	
	@FindBy(linkText = "Change the authority name")
	WebElement authorityNameLink;
	
	@FindBy(linkText = "Change the authority type")
	WebElement authorityTypeLink;
	
	@FindBy(linkText = "Change the ONS code")
	WebElement ONSCode;
	
	@FindBy(linkText = "Change the regulatory functions")
	WebElement regFunctions;

	public AuthorityNamePage editAuthorityName() {
		authorityNameLink.click();
		return PageFactory.initElements(driver, AuthorityNamePage.class);
	}
	
	public AuthorityDashboardPage editAuthorityType() {
		authorityTypeLink.click();
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}
	
	public AuthorityDashboardPage editONSCode() {
		ONSCode.click();
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}
	
	public AuthorityDashboardPage editRegFunction() {
		regFunctions.click();
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}
}
