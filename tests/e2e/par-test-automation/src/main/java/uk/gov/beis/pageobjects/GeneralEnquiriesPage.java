package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class GeneralEnquiriesPage extends BasePageObject {
	public GeneralEnquiriesPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(xpath = "//td[@class='views-field views-field-par-label']")
	private WebElement enquiriesName;
	
	@FindBy(linkText = "back to dashboard")
	private WebElement dashboardBtn;
	
	// To make the search more dynamic, using a loop which compares the "Test Business" name to a parameter would be better
	public EnforcementNotificationActionReceivedPage chooseGeneralEnquiry(String business) {
		
		if(enquiriesName.getText().contains(business)) {
			WebElement viewGeneralEnquiry = driver.findElement(By.linkText("View general enquiry"));
			viewGeneralEnquiry.click();
		}
		
		return PageFactory.initElements(driver, EnforcementNotificationActionReceivedPage.class);
	}
	
	public DashboardPage clickDashboardButton() {
		dashboardBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
