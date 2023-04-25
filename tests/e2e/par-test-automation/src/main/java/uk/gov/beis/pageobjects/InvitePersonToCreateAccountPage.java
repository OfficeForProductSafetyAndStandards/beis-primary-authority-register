package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class InvitePersonToCreateAccountPage extends BasePageObject {
	public InvitePersonToCreateAccountPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(id = "edit-next")
	private WebElement inviteBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public ProfileReviewPage clickInviteButton() {
		inviteBtn.click();
		return PageFactory.initElements(driver, ProfileReviewPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
