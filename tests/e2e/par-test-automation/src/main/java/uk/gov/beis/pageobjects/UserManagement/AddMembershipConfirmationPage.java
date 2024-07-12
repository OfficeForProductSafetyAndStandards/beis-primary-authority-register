package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AddMembershipConfirmationPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public AddMembershipConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
