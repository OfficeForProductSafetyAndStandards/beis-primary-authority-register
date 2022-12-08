package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class UserNotificationPreferencesPage extends BasePageObject{

	public UserNotificationPreferencesPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public UserProfileConfirmationPage selectContinue() {
			continueBtn.click();
		return PageFactory.initElements(driver, UserProfileConfirmationPage.class);
	}

}
