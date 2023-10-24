package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;
import uk.gov.beis.pageobjects.UserManagement.ProfileReviewPage;

public class AccountInvitePage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-next")
	private WebElement inviteBtn;
	
	public AccountInvitePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public PartnershipConfirmationPage sendInvite() {
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public ProfileReviewPage clickInviteButton() {
		inviteBtn.click();
		return PageFactory.initElements(driver, ProfileReviewPage.class);
	}
}
