package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class GivePersonAccountPage extends BasePageObject {
	public GivePersonAccountPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-account-new")
	private WebElement inviteUserRadioBtn;
	
	@FindBy(xpath = "//input[contains(@id,'edit-account-')]")
	private List<WebElement> radioButtons;
	
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
	
	public ChoosePersonMembershipPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, ChoosePersonMembershipPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
