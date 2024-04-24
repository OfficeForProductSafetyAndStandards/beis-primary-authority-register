package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.LegalEntityPageObjects.LegalEntityTypePage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipInformationPage;

public class TradingPage extends BasePageObject {
	
	@FindBy(id= "edit-trading-name")
	private WebElement tradingName;
	
	@FindBy(id= "edit-par-component-trading-name-0-trading-name")
	private WebElement editTradingName;
	
	//@FindBy(xpath = "//div/input[@class='form-text form-control govuk-input']")
	@FindBy(id= "edit-par-component-trading-name-0-trading-name")
	private WebElement memberTradingName;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public TradingPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterTradingName(String name) {
		tradingName.clear();
		tradingName.sendKeys(name);
	}
	
	public void editTradingName(String name) {
		editTradingName.clear();
		editTradingName.sendKeys(name);
	}
	
	public void editMemberTradingName(String name) {
		tradingName.clear();
		tradingName.sendKeys(name);
	}
	
	public void enterTradingNameForMember(String name) {
		memberTradingName.clear();
		memberTradingName.sendKeys(name);
	}
	
	public void clearTradingName() {
		tradingName.clear();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public PartnershipInformationPage goToPartnershipInformationPage(String name) {
		tradingName.clear();
		tradingName.sendKeys(name);
		
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipInformationPage.class);
	}
	
	public BusinessDetailsPage goToBusinessDetailsPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
	
	public LegalEntityTypePage goToLegalEntityTypePage() {
		continueBtn.click();
		return PageFactory.initElements(driver, LegalEntityTypePage.class);
	}
	
	public LegalEntityTypePage addTradingNameForMember(String name) {
		memberTradingName.clear();
		memberTradingName.sendKeys(name);
		
		continueBtn.click();
		return PageFactory.initElements(driver, LegalEntityTypePage.class);
	}
	
	public MemberOrganisationSummaryPage goToMemberOrganisationSummaryPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
}
