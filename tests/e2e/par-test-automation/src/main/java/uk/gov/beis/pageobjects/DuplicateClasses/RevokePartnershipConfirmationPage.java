package uk.gov.beis.pageobjects.DuplicateClasses;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipRevokedPage;

public class RevokePartnershipConfirmationPage extends BasePageObject {

	public RevokePartnershipConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Revoke')]")
	WebElement revokeBtn;

	public PartnershipRevokedPage enterRevokeReason(String desc) throws Throwable {
		if (descriptionBox.isDisplayed()) {
			descriptionBox.clear();
			descriptionBox.sendKeys(desc);
		}
		if (revokeBtn.isDisplayed())
			revokeBtn.click();

		return PageFactory.initElements(driver, PartnershipRevokedPage.class);
	}
}