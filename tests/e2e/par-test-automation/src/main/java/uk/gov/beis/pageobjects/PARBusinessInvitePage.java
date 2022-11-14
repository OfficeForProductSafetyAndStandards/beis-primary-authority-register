package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARBusinessInvitePage extends BasePageObject {

	public PARBusinessInvitePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PARPartnershipConfirmationPage sendInvite() {
		if (continueBtn.isDisplayed())
			continueBtn.click();
		return PageFactory.initElements(driver, PARPartnershipConfirmationPage.class);
	}
}
