package uk.gov.beis.pageobjects.UserDashboardPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

public class DashboardPage extends BaseDashboardPage {
	
	@FindBy(linkText = "See your partnerships")
	private WebElement viewPartnershipBtn;
	
	@FindBy(linkText = "Search for a partnership")
	private WebElement searchPartnershipBtn;
	
	@FindBy(linkText = "Apply for a new partnership")
	private WebElement applyPartnershipBtn;
	
	@FindBy(linkText = "See your enforcement notices")
	private WebElement seeEnforcementNoticesBtn;
	
	@FindBy(linkText = "See your deviation requests")
	private WebElement deviationReqBtn;
	
	@FindBy(linkText = "See your inspection feedback")
	private WebElement inspectionFeedbackNoticeBtn;
	
	@FindBy(partialLinkText = "general enquiries")
	private WebElement generalEnquiriesBtn;
	
	@FindBy(linkText = "Manage your colleagues")
	private WebElement manageYourColleaguesBtn;
	
	public DashboardPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectSeePartnerships() {
		viewPartnershipBtn.click();
	}
	
	public void selectSearchforPartnership() {
		searchPartnershipBtn.click();
	}
	
	public void selectApplyForNewPartnership() {
		applyPartnershipBtn.click();
	}
	
	public void selectSeeEnforcementNotices() {
		seeEnforcementNoticesBtn.click();
	}
	
	public void selectSeeDeviationRequests() {
		deviationReqBtn.click();
	}
	
	public void selectSeeInspectionFeedbackNotices() {
		inspectionFeedbackNoticeBtn.click();
	}
	
	public void selectGeneralEnquiries() {
		generalEnquiriesBtn.click();
	}
	
	public void selectManageColleagues() {
		manageYourColleaguesBtn.click();
	}
}
