package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARPartnershipCompletionPage extends BasePageObject {

	public PARPartnershipCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//a[contains(@class,'button')]")
	WebElement doneBtn;

	public PARDashboardPage completeApplication() {
		if (doneBtn.isDisplayed()) {
			doneBtn.click();
		}
		return PageFactory.initElements(driver, PARDashboardPage.class);
	}
}
