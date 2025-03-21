package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class EnforcementNotificationPage extends BasePageObject {
	
	@FindBy(id = "edit-enquire")
	private WebElement discussEnforcementBtn;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public EnforcementNotificationPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectDiscussEnforcement() {
		discussEnforcementBtn.click();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
