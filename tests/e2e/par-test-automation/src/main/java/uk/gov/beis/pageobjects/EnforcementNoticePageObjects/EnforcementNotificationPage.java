package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.GeneralEnquiryPageObjects.EnquiryContactDetailsPage;

public class EnforcementNotificationPage extends BasePageObject {
	
	@FindBy(id = "edit-enquire")
	private WebElement discussEnforcementBtn;
	
	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	private WebElement continueBtn;
	
	public EnforcementNotificationPage() throws ClassNotFoundException, IOException {
		super();
	}

	public EnquiryContactDetailsPage selectDiscussEnforcement() {
		discussEnforcementBtn.click();
		return PageFactory.initElements(driver, EnquiryContactDetailsPage.class);
	}
	
	public EnforcementContactDetailsPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementContactDetailsPage.class);
	}
}
