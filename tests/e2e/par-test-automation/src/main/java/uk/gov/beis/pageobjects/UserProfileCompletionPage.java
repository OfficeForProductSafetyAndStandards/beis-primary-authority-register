package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class UserProfileCompletionPage extends BasePageObject {

	public UserProfileCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//a[contains(@class,'button')]")
	WebElement doneBtn;

	public DashboardPage completeApplication() {
		if (doneBtn.isDisplayed()) {
			doneBtn.click();
		}
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}