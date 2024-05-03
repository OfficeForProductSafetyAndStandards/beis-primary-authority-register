package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class UserAccountInvitationReviewPage extends BasePageObject {

	@FindBy(id = "edit-save")
	private WebElement inviteBtn;
	
	public UserAccountInvitationReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void clickSendInviationButton() {
		inviteBtn.click();
	}
}
