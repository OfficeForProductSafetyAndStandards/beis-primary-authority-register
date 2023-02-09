package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class TradingPage extends BasePageObject {
	public TradingPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-trading-name")
	WebElement tradingName;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

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
}
