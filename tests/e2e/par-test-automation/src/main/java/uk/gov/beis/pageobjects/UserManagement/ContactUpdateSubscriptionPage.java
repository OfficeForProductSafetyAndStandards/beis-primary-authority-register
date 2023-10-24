package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ContactUpdateSubscriptionPage extends BasePageObject {
	
	@FindBy(id = "edit-subscriptions-par-news")
	private WebElement parNewsCheckbox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public ContactUpdateSubscriptionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void subscribeToPARNews() {
		if (!parNewsCheckbox.isSelected())
			parNewsCheckbox.click();
	}

	public void unsubscribeFromPARNews() {
		if (parNewsCheckbox.isSelected())
			parNewsCheckbox.click();
	}

	public ProfileReviewPage selectContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, ProfileReviewPage.class);
	}
}
