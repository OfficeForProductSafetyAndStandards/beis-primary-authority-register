package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

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
	
	private String addressLocator = "//div/p[contains(text(),'?')]";
	private String authPCode = "//div/p[contains(text(),'?')]";
	
	private String authorityNameLocator = "//div/p[contains(text(),'?')]";
	private String authorityTypeLocator = "//div/p[contains(text(),'?')]";
	private String onsCodeLocator = "//div/p[contains(text(),'?')]";
	private String regulatoryFunctionLocator = "//div/p[contains(text(),'?')]";
	
	public AuthorityConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkAuthorityDetails() {
		WebElement address = driver.findElement(By.xpath(addressLocator.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_ADDRESSLINE1))));
		WebElement postcode = driver.findElement(By.xpath(authPCode.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_POSTCODE))));
		
		WebElement authName = driver.findElement(By.xpath(authorityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_NAME))));
		WebElement authType = driver.findElement(By.xpath(authorityTypeLocator.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE))));
		WebElement onscode = driver.findElement(By.xpath(onsCodeLocator.replace("?", DataStore.getSavedValue(UsableValues.ONS_CODE))));
		WebElement regFunc = driver.findElement(By.xpath(regulatoryFunctionLocator.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION))));
		
		return address.isDisplayed() && postcode.isDisplayed() && authName.isDisplayed() && authType.isDisplayed() && onscode.isDisplayed() && regFunc.isDisplayed();
	}
	
	public void editAuthorityName() {
		authorityNameLink.click();
	}
	
	public void editAuthorityType() {
		authorityTypeLink.click();
	}
	
	public void editONSCode() {
		ONSCode.click();
	}
	
	public void editRegFunction() {
		regFunctions.click();
	}
	
	public void saveChanges() {
		saveBtn.click();
	}
}
