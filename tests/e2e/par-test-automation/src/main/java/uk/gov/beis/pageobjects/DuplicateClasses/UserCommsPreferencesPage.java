package uk.gov.beis.pageobjects.DuplicateClasses;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class UserCommsPreferencesPage extends BasePageObject {

	public UserCommsPreferencesPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	private WebElement continueBtn;

	public UserSubscriptionPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, UserSubscriptionPage.class);
	}

}
