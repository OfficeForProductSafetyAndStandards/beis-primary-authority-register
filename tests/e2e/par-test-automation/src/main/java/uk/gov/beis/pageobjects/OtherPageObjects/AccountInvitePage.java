package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AccountInvitePage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-next")
	private WebElement inviteBtn;
	
	public AccountInvitePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickInviteButton() {
		inviteBtn.click();
	}
}
