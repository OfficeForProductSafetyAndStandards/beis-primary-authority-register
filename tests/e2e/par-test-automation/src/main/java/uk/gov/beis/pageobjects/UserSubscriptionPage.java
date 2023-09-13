package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class UserSubscriptionPage extends BasePageObject {

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	private WebElement continueBtn;
	
	public UserSubscriptionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public UserNotificationPreferencesPage selectContinue() {
		continueBtn.click();
		return PageFactory.initElements(driver, UserNotificationPreferencesPage.class);
	}
}