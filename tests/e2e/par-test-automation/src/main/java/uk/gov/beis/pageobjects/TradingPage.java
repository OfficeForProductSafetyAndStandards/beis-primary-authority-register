package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class TradingPage extends BasePageObject{
	public TradingPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[@type='text']")
	WebElement tradingName;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public LegalEntityPage enterBusinessName(String name) {
		if (tradingName.isDisplayed()) {
			tradingName.clear();
			tradingName.sendKeys(name);
		}
		if (continueBtn.isDisplayed())
			continueBtn.click();

		return PageFactory.initElements(driver, LegalEntityPage.class);
	}
}
