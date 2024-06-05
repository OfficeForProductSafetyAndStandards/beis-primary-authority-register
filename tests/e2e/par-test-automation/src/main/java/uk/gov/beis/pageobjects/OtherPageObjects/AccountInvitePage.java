package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipInformationPage;
import uk.gov.beis.pageobjects.UserManagement.ProfileReviewPage;
import uk.gov.beis.pageobjects.UserManagement.UserAccountInvitationReviewPage;

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
	
	public PartnershipInformationPage sendInvite() {
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipInformationPage.class);
	}
	
	public ProfileReviewPage clickInviteButton() {
		inviteBtn.click();
		return PageFactory.initElements(driver, ProfileReviewPage.class);
	}
	
	public UserAccountInvitationReviewPage goToInvitationReviewPage() {
		inviteBtn.click();
		return PageFactory.initElements(driver, UserAccountInvitationReviewPage.class);
	}
}
