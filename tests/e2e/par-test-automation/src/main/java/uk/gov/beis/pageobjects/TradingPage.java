package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class TradingPage extends BasePageObject {
	
	@FindBy(xpath = "//div/input[@class='form-text form-control govuk-input']")
	private WebElement tradingName;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public TradingPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public BasePageObject enterTradingName(String name) {
		tradingName.clear();
		tradingName.sendKeys(name);
		
		try {
			driver.findElement(By.id("edit-next")).click();
			return PageFactory.initElements(driver, LegalEntityPage.class);
		} catch (Exception e) {
			driver.findElement(By.id("edit-save")).click();
			return PageFactory.initElements(driver, BusinessConfirmationPage.class);
		}
	}
	
	public PartnershipConfirmationPage editTradingName(String name) {
		tradingName.clear();
		tradingName.sendKeys(name);
		
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public LegalEntityTypePage addTradingNameForMember(String name) {
		tradingName.clear();
		tradingName.sendKeys(name);
		
		continueBtn.click();
		return PageFactory.initElements(driver, LegalEntityTypePage.class);
	}
}
