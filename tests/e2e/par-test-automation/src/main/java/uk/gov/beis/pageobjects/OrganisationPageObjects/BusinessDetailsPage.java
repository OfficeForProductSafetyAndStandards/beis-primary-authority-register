package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class BusinessDetailsPage extends BasePageObject{
	
	@FindBy(linkText = "Change the organisation name")
	private WebElement organisationNameLink;
	
	@FindBy(linkText = "Change the description")
	private WebElement organisationDescLink;
	
	@FindBy(linkText = "Change the trading names")
	private WebElement tradingName;
	
	@FindBy(linkText = "Change the SIC codes")
	private WebElement sicCode;
	
	@FindBy(xpath = "//input[contains(@value,'Save')]")
	private WebElement saveBtn;

	private String organisationNameLocator = "//div/p[contains(text(),'?')]";
	private String organisationDescriptionLocator = "//div/p[contains(text(),'?')]";
	private String tradingNameLocator = "//div/p[contains(text(),'?')]";
	private String sicCodeLocator = "//div/p[contains(text(),'?')]";
	
	public BusinessDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkAuthorityDetails() {
		WebElement orgName = driver.findElement(By.xpath(organisationNameLocator.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME))));
		WebElement orgDescription = driver.findElement(By.xpath(organisationDescriptionLocator.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_DESC))));
		WebElement tradeName = driver.findElement(By.xpath(tradingNameLocator.replace("?", DataStore.getSavedValue(UsableValues.TRADING_NAME))));
		WebElement scCode = driver.findElement(By.xpath(sicCodeLocator.replace("?", DataStore.getSavedValue(UsableValues.SIC_CODE))));

		return orgName.isDisplayed() && orgDescription.isDisplayed() && tradeName.isDisplayed() && scCode.isDisplayed();
	}
	
	public void editOrganisationName() {
		organisationNameLink.click();
	}
	
	public void  editOrganisationDesc() {
		organisationDescLink.click();
	}
	
	public void  editTradingName() {
		tradingName.click();
	}
	
	public void  editSICCode() {
		sicCode.click();
	}
	
	public void  saveChanges() {
		saveBtn.click();
	}
}
