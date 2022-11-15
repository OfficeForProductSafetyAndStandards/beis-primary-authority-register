package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARBusinessDetailsPage extends BasePageObject {

	public PARBusinessDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PARBusinessAddressDetailsPage enterPartnershipDescription() throws Throwable {
		if (descriptionBox.isDisplayed()) {
			descriptionBox.clear();
			descriptionBox.sendKeys("Test business description");
		}
		if (continueBtn.isDisplayed())
			continueBtn.click();

		return PageFactory.initElements(driver, PARBusinessAddressDetailsPage.class);
	}
}
