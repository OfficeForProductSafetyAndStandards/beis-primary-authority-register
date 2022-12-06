package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class RestorePartnershipConfirmationPage extends BasePageObject {

	public RestorePartnershipConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PartnershipRestoredPage proceed() throws Throwable {
		if (continueBtn.isDisplayed())
			continueBtn.click();

		return PageFactory.initElements(driver, PartnershipRestoredPage.class);
	}
}