package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class AuthorityConfirmationPage extends BasePageObject {

	@FindBy(linkText = "Change the authority name")
	private WebElement authorityNameLink;
	
	@FindBy(linkText = "Change the authority type")
	private WebElement authorityTypeLink;
	
	@FindBy(linkText = "Change the ons code")
	private WebElement ONSCode;
	
	@FindBy(linkText = "Change the regulatory functions")
	private WebElement regFunctions;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public AuthorityConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	String authAddress1 = "//div/p[contains(text(),'?')]";
	String authPCode = "//div/p[contains(text(),'?')]";
	String authorityName = "//div/p[contains(text(),'?')]";
	String authorityType = "//div/p[contains(text(),'?')]";
	String ons = "//div/p[contains(text(),'?')]";
	String regFunc = "//div/p[contains(text(),'?')]";

	public boolean checkAuthorityDetails() {
		WebElement authAddressLine1 = driver.findElement(By.xpath(authAddress1.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_ADDRESSLINE1))));
		WebElement authPCode1 = driver.findElement(By.xpath(authPCode.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_POSTCODE))));
		WebElement authName = driver.findElement(By.xpath(authorityName.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_NAME))));
		WebElement authType = driver.findElement(By.xpath(authorityType.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE))));
		WebElement onscode = driver.findElement(By.xpath(ons.replace("?", DataStore.getSavedValue(UsableValues.ONS_CODE))));
		WebElement regFunc1 = driver.findElement(By.xpath(regFunc.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION))));
		
		return (authAddressLine1.isDisplayed() && authPCode1.isDisplayed() && authName.isDisplayed() && authType.isDisplayed() && onscode.isDisplayed() && regFunc1.isDisplayed());
	}
	
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
	
	public AuthorityDashboardPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}
}
