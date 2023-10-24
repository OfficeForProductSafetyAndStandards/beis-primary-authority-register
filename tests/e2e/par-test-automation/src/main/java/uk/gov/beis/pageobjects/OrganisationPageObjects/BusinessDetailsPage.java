package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.OrganisationDashboardPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.BusinessNamePage;
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
	
	public BusinessNamePage editOrganisationName() {
		organisationNameLink.click();
		return PageFactory.initElements(driver, BusinessNamePage.class);
	}
	
	public AboutTheOrganisationPage editOrganisationDesc() {
		organisationDescLink.click();
		return PageFactory.initElements(driver, AboutTheOrganisationPage.class);
	}
	
	public TradingPage editTradingName() {
		tradingName.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
	
	public SICCodePage editSICCode() {
		sicCode.click();
		return PageFactory.initElements(driver, SICCodePage.class);
	}
	
	public OrganisationDashboardPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, OrganisationDashboardPage.class);
	}
}
