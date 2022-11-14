package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARBusinessPage extends BasePageObject {
	public PARBusinessPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[@type='text']")
	WebElement businessName;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PARBusinessAddressDetailsPage enterBusinessName(String name) {
		if (businessName.isDisplayed()) {
			businessName.clear();
			businessName.sendKeys(name);
		}
		if (continueBtn.isDisplayed())
			continueBtn.click();

		return PageFactory.initElements(driver, PARBusinessAddressDetailsPage.class);
	}
}
