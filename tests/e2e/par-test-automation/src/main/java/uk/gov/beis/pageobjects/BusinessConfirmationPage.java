package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.AuthorityPageObjects.AuthorityDashboardPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.BusinessDetailsPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.SICCodePage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.TradingPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.BusinessPage;
import uk.gov.beis.utility.DataStore;

public class BusinessConfirmationPage extends BasePageObject{
	
	public BusinessConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	String orgName = "//div/p[contains(text(),'?')]";
	String orgDescription = "//div/p[contains(text(),'?')]";
	String tradeName = "//div/p[contains(text(),'?')]";
	String scCode = "//div/p[contains(text(),'?')]";


	public boolean checkAuthorityDetails() {
		WebElement orgName1 = driver
				.findElement(By.xpath(orgName.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME))));
		WebElement orgDescription1 = driver
				.findElement(By.xpath(orgDescription.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_DESC))));
		WebElement tradeName1 = driver.findElement(
				By.xpath(tradeName.replace("?", DataStore.getSavedValue(UsableValues.TRADING_NAME))));
		WebElement scCode1 = driver.findElement(
				By.xpath(scCode.replace("?", DataStore.getSavedValue(UsableValues.SIC_CODE))));

		return (orgName1.isDisplayed() && orgDescription1.isDisplayed()
				&& tradeName1.isDisplayed() && scCode1.isDisplayed());
	}
	
	public AuthorityDashboardPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}
	
	@FindBy(linkText = "Change the organisation name")
	WebElement organisationNameLink;
	
	@FindBy(linkText = "Change the description")
	WebElement organisationDescLink;
	
	@FindBy(linkText = "Change the trading names")
	WebElement tradingName;
	
	@FindBy(linkText = "Change the SIC codes")
	WebElement sicCode;

	public BusinessPage editOrganisationName() {
		organisationNameLink.click();
		return PageFactory.initElements(driver, BusinessPage.class);
	}
	
	public BusinessDetailsPage editOrganisationDesc() {
		organisationDescLink.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
	
	public TradingPage editTradingName() {
		tradingName.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
	
	public SICCodePage editSICCode() {
		sicCode.click();
		return PageFactory.initElements(driver, SICCodePage.class);
	}
}
