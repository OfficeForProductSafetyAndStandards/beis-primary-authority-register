package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class UserSubscriptionPage extends BasePageObject {

	public UserSubscriptionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public UserNotificationPreferencesPage selectContinue() {
		if (continueBtn.isDisplayed())
			continueBtn.click();
		return PageFactory.initElements(driver, UserNotificationPreferencesPage.class);
	}

}