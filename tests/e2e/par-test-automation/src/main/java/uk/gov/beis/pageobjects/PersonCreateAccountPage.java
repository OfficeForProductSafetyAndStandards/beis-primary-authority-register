package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PersonCreateAccountPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement inviteBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public PersonCreateAccountPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public UserProfileConfirmationPage clickInviteButton() {
		inviteBtn.click();
		return PageFactory.initElements(driver, UserProfileConfirmationPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
