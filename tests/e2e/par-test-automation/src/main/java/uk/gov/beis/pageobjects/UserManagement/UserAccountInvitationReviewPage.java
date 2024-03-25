package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.SharedPageObjects.CompletionPage;

public class UserAccountInvitationReviewPage extends BasePageObject {

	@FindBy(id = "edit-save")
	private WebElement inviteBtn;
	
	public UserAccountInvitationReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public CompletionPage clickSendInviationButton() {
		inviteBtn.click();
		return PageFactory.initElements(driver, CompletionPage.class);
	}
}
