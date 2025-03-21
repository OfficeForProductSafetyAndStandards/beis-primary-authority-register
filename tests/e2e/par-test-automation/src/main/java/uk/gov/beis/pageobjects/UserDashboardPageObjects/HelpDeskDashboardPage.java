package uk.gov.beis.pageobjects.UserDashboardPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

public class HelpDeskDashboardPage extends BaseDashboardPage {
	
	@FindBy(linkText  = "View all statistics")
	private WebElement viewStatisticsLink;
	
	@FindBy(linkText = "Search partnerships")
	private WebElement searchPartnershipBtn;
	
	@FindBy(linkText = "Manage authorities")
	private WebElement mangeAuthoritiesBtn;
	
	@FindBy(linkText = "Manage organisations")
	private WebElement mangeOrganisationsBtn;
	
	@FindBy(linkText = "Manage people")
	private WebElement managePeopleBtn;
	
	@FindBy(linkText = "Manage subscriptions")
	private WebElement manageSubscriptionsBtn;
	
	@FindBy(linkText = "Manage enforcement notices")
	private WebElement manageEnforcementNoticesBtn;
	
	@FindBy(linkText = "Manage general enquiries")
	private WebElement manageGeneralEnquiriesBtn;
	
	@FindBy(linkText = "Manage deviation requests")
	private WebElement manageDeviationRequestLink;
	
	@FindBy(linkText = "Manage inspection feedback")
	private WebElement manageInspectionFeedbackLink;
	
	@FindBy(linkText = "Manage general enquiries")
	private WebElement manageGeneralEnquiryLink;
	
	public HelpDeskDashboardPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectViewAllStatistics() {
		viewStatisticsLink.click();
	}
	
	public void selectSearchPartnerships() {
		searchPartnershipBtn.click();
	}
	
	public void selectManageAuthorities() {
		mangeAuthoritiesBtn.click();
	}
	
	public void selectManageOrganisations() {
		mangeOrganisationsBtn.click();
	}
	
	public void selectManagePeople() {
		managePeopleBtn.click();
	}
	
	public void selectManageSubscriptions() {
		manageSubscriptionsBtn.click();
	}
	
	public void selectManageEnforcementNotices() {
		manageEnforcementNoticesBtn.click();
	}
	
	public void selectManageDeviationRequests() {
		manageDeviationRequestLink.click();
	}
	
	public void selectManageInspectionFeedback() {
		manageInspectionFeedbackLink.click();
	}
	
	public void selectManageGeneralEnquiry() {
		manageGeneralEnquiryLink.click();
	}
}
