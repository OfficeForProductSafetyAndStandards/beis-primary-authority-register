package uk.gov.beis.pageobjects.DuplicateClasses;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.UserManagement.ProfileReviewPage;

public class UserNotificationPreferencesPage extends BasePageObject{

	public UserNotificationPreferencesPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public ProfileReviewPage selectContinue() {
			continueBtn.click();
		return PageFactory.initElements(driver, ProfileReviewPage.class);
	}

}
