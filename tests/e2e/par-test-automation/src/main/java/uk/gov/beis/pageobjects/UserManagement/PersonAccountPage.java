package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DashboardPage;

public class PersonAccountPage extends BasePageObject {
	public PersonAccountPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-account-new")
	private WebElement inviteUserRadioBtn;
	
	@FindBy(xpath = "//input[contains(@id,'edit-account-')]")
	private List<WebElement> radioButtons;
	
	@FindBy(xpath = "//label[contains(text(),'Use the existing account:')]")
	private WebElement existingAccountRadial;
	
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
		for(WebElement radio : radioButtons) {
			if(radio != createUserAccountRadioBtn) {
				radio.click();
			}
		}
	}
	
	public void selectCreateAccount() {
		createUserAccountRadioBtn.click();
	}
	
	public void selectUseExistingAccount() {
		existingAccountRadial.click();
	}
	
	public PersonMembershipPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, PersonMembershipPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
