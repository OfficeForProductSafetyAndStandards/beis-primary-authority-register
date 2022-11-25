package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class BusinessInvitePage extends BasePageObject {

	public BusinessInvitePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PartnershipConfirmationPage sendInvite() {
		if (continueBtn.isDisplayed())
			continueBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
