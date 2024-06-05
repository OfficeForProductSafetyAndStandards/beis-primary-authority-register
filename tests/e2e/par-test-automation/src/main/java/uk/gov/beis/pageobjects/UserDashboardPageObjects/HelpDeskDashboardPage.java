package uk.gov.beis.pageobjects.UserDashboardPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.AuthorityPageObjects.AuthoritiesSearchPage;
import uk.gov.beis.pageobjects.DeviationRequestPageObjects.DeviationSearchPage;
import uk.gov.beis.pageobjects.EnforcementNoticePageObjects.EnforcementSearchPage;
import uk.gov.beis.pageobjects.GeneralEnquiryPageObjects.EnquiriesSearchPage;
import uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects.InspectionFeedbackSearchPage;
import uk.gov.beis.pageobjects.NewsLetterSubscriptionPageObjects.NewsLetterSubscriptionPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.OrganisationsSearchPage;
import uk.gov.beis.pageobjects.OtherPageObjects.PARReportingPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipAdvancedSearchPage;
import uk.gov.beis.pageobjects.UserManagement.ManagePeoplePage;

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
	
	public PARReportingPage selectViewAllStatistics() {
		viewStatisticsLink.click();
		return PageFactory.initElements(driver, PARReportingPage.class);
	}
	
	public PartnershipAdvancedSearchPage selectSearchPartnerships() {
		searchPartnershipBtn.click();
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}
	
	public AuthoritiesSearchPage selectManageAuthorities() {
		mangeAuthoritiesBtn.click();
		return PageFactory.initElements(driver, AuthoritiesSearchPage.class);
	}
	
	public OrganisationsSearchPage selectManageOrganisations() {
		mangeOrganisationsBtn.click();
		return PageFactory.initElements(driver, OrganisationsSearchPage.class);
	}
	
	public ManagePeoplePage selectManagePeople() {
		managePeopleBtn.click();
		return PageFactory.initElements(driver, ManagePeoplePage.class);
	}
	
	public NewsLetterSubscriptionPage selectManageSubscriptions() {
		manageSubscriptionsBtn.click();
		return PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
	}
	
	public EnforcementSearchPage selectManageEnforcementNotices() {
		manageEnforcementNoticesBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
	
	public DeviationSearchPage selectManageDeviationRequests() {
		manageDeviationRequestLink.click();
		return PageFactory.initElements(driver, DeviationSearchPage.class);
	}
	
	public InspectionFeedbackSearchPage selectManageInspectionFeedback() {
		manageInspectionFeedbackLink.click();
		return PageFactory.initElements(driver, InspectionFeedbackSearchPage.class);
	}
	
	public EnquiriesSearchPage selectManageGeneralEnquiry() {
		manageGeneralEnquiryLink.click();
		return PageFactory.initElements(driver, EnquiriesSearchPage.class);
	}
}
