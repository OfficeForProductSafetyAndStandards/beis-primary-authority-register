package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class EnforcementNotificationPage extends BasePageObject {
	
	public EnforcementNotificationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;
	
	public EnforcementContactDetailsPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementContactDetailsPage.class);
	}

}
