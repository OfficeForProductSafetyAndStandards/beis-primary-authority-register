package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class LegalEntityReviewPage extends BasePageObject {

	public LegalEntityReviewPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PartnershipConfirmationPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}