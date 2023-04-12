package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AuthorityGiveUserAccountPage extends BasePageObject {
	public AuthorityGiveUserAccountPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-account-19278") // ID of the current Authority Signed In.
	private WebElement existingAccountRadioBtn;
	
	@FindBy(id = "edit-account-none")
	private WebElement createUserAccountRadioBtn;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public void selectExistingAccount() {
		existingAccountRadioBtn.click();
	}
	
	public void selectCreateAccount() {
		createUserAccountRadioBtn.click();
	}
	
	public AuthorityChooseMembershipPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityChooseMembershipPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
