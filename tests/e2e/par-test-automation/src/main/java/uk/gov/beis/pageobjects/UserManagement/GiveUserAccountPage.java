package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class GiveUserAccountPage extends BasePageObject {
	
	@FindBy(id = "edit-account-new")
	private WebElement inviteUserRadioBtn;
	
	@FindBy(xpath = "//label[contains(text(),'Use the existing account:')]")
	private WebElement existingAccountRadial;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public GiveUserAccountPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectInviteUserToCreateAccount() {
		inviteUserRadioBtn.click();
	}
	
	public void selectUseExistingAccount() {
		existingAccountRadial.click();
	}
	
	public UserMembershipPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, UserMembershipPage.class);
	}
}
