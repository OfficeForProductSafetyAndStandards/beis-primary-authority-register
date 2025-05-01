package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

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
        waitForElementToBeVisible(By.id("edit-next"), 2000);
        continueBtn.click();
        waitForPageLoad();
	}

	public void clickSaveButton() {
        waitForElementToBeVisible(By.id("edit-save"), 2000);
        saveBtn.click();
        waitForPageLoad();
	}
}
