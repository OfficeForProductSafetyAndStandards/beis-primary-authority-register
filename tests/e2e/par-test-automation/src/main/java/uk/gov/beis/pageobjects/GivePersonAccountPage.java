package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class GivePersonAccountPage extends BasePageObject {
	public GivePersonAccountPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-account-new")
	private WebElement inviteUserRadioBtn;
	
	@FindBy(id = "edit-account-19278") // ID of the current Authority Signed In.
	private WebElement existingAccountRadioBtn;
	
	@FindBy(id = "edit-account-none")
	private WebElement createUserAccountRadioBtn;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public void selectInviteUserToCreateAccount() {
		inviteUserRadioBtn.click();
	}
	
	public void selectExistingAccount() {
		existingAccountRadioBtn.click();
	}
	
	public void selectCreateAccount() {		// Check as Authority User
		createUserAccountRadioBtn.click();
	}
	
	// Check these steps
	public ChoosePersonMembershipPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, ChoosePersonMembershipPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
