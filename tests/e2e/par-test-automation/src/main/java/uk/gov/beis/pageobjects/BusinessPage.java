package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class BusinessPage extends BasePageObject {

	public BusinessPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[@type='text']")
	WebElement businessName;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public BusinessAddressDetailsPage enterBusinessName(String name) {
		businessName.clear();
		businessName.sendKeys(name);
		continueBtn.click();

		return PageFactory.initElements(driver, BusinessAddressDetailsPage.class);
	}
}
