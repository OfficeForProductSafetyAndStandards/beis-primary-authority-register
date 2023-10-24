package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.AuthorityPageObjects.AuthorityDashboardPage;
import uk.gov.beis.pageobjects.DeviationRequestPageObjects.DeviationSearchPage;
import uk.gov.beis.pageobjects.EnforcementNoticePageObjects.EnforcementSearchPage;
import uk.gov.beis.pageobjects.GeneralEnquiryPageObjects.EnquiriesSearchPage;
import uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects.InspectionFeedbackSearchPage;
import uk.gov.beis.pageobjects.NewsLetterSubscriptionPageObjects.NewsLetterSubscriptionPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.AuthorityPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipAdvancedSearchPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipSearchPage;
import uk.gov.beis.pageobjects.UserManagement.ManagePeoplePage;
import uk.gov.beis.pageobjects.UserManagement.ContactRecordsPage;

public class DashboardPage extends BasePageObject {
	
	@FindBy(xpath = "//div[@id='block-par-theme-page-title']")
	private WebElement dashBoard;
	
	@FindBy(linkText  = "View all statistics")
	private WebElement viewStatisticsLink;

	@FindBy(linkText = "Apply for a new partnership")
	WebElement applyPartnershipBtn;
	
	@FindBy(linkText = "See your enforcement notices")
	WebElement seeEnforcementNoticesBtn;
	
	@FindBy(linkText = "Manage enforcement notices")
	WebElement manageEnforcementNoticesBtn;

	@FindBy(linkText = "See your partnerships")
	WebElement viewPartnershipBtn;

	@FindBy(linkText = "Search partnerships")
	WebElement searchPartnershipBtn;
	
	@FindBy(linkText = "Search for a partnership")
	WebElement searchPartnershipBtn1;

	@FindBy(linkText = "Manage authorities")
	WebElement mangeAuthoritiesBtn;
	
	@FindBy(linkText = "Manage organisations")
	WebElement mangeOrganisationsBtn;
	
	@FindBy(linkText = "See your inspection feedback")
	WebElement inspectionFeedbackNoticeBtn;
	
	@FindBy(linkText = "Manage your colleagues")
	WebElement manageYourColleaguesBtn;
	
	@FindBy(linkText = "Manage people")
	WebElement managePeopleBtn;
	
	@FindBy(linkText = "Manage general enquiries")
	WebElement manageGeneralEnquiriesBtn;
	
	@FindBy(linkText = "Manage your profile details")
	WebElement manageYourProfileDetailsBtn;
	
	@FindBy(linkText = "Manage subscriptions")
	WebElement helpDeskManageSubscriptionsBtn;
	
	@FindBy(linkText = "See your deviation requests")
	WebElement deviationReqBtn;
	
	@FindBy(linkText = "Manage deviation requests")
	WebElement manageDeviationRequestLink;
	
	@FindBy(linkText = "Manage inspection feedback")
	WebElement manageInspectionFeedbackLink;
	
	@FindBy(linkText = "Manage general enquiries")
	WebElement manageGeneralEnquiryLink;
	
	@FindBy(partialLinkText = "general enquiries")
	WebElement generalEnquiriesBtn;
	
	public DashboardPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PARReportingPage selectViewAllStatistics() {
		viewStatisticsLink.click();
		return PageFactory.initElements(driver, PARReportingPage.class);
	}
	
	public OrganisationDashboardPage selectManageOrganisations() {
		mangeOrganisationsBtn.click();
		return PageFactory.initElements(driver, OrganisationDashboardPage.class);
	}
	
	public AuthorityPage selectApplyForNewPartnership() {
		applyPartnershipBtn.click();
		return PageFactory.initElements(driver, AuthorityPage.class);
	}
	
	public EnforcementSearchPage selectSeeEnforcementNotices() {
		seeEnforcementNoticesBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
	
	public EnforcementSearchPage selectManageEnforcementNotices() {
		manageEnforcementNoticesBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
	
	public InspectionFeedbackSearchPage selectSeeInspectionFeedbackNotices() {
		inspectionFeedbackNoticeBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackSearchPage.class);
	}
	
	public InspectionFeedbackSearchPage selectManageInspectionFeedback() {
		manageInspectionFeedbackLink.click();
		return PageFactory.initElements(driver, InspectionFeedbackSearchPage.class);
	}
	
	public EnquiriesSearchPage selectGeneralEnquiries() {
		generalEnquiriesBtn.click();
		return PageFactory.initElements(driver, EnquiriesSearchPage.class);
	}
	
	public EnquiriesSearchPage selectManageGeneralEnquiry() {
		manageGeneralEnquiryLink.click();
		return PageFactory.initElements(driver, EnquiriesSearchPage.class);
	}
	
	public DeviationSearchPage selectSeeDeviationRequests() {
		deviationReqBtn.click();
		return PageFactory.initElements(driver, DeviationSearchPage.class);
	}
	
	public DeviationSearchPage selectManageDeviationRequests() {
		manageDeviationRequestLink.click();
		return PageFactory.initElements(driver, DeviationSearchPage.class);
	}

	public PartnershipSearchPage selectSeePartnerships() {
		viewPartnershipBtn.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}
	
	public AuthorityDashboardPage selectManageAuthorities() {
		mangeAuthoritiesBtn.click();
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}

	public PartnershipAdvancedSearchPage selectSearchPartnerships() {
		searchPartnershipBtn.click();
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}
	
	public PartnershipSearchPage selectSearchforPartnership() {
		searchPartnershipBtn1.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}
	
	public ManagePeoplePage selectManageColleagues() {
		manageYourColleaguesBtn.click();
		return PageFactory.initElements(driver, ManagePeoplePage.class);
	}
	
	public ManagePeoplePage selectManagePeople() {
		managePeopleBtn.click();
		return PageFactory.initElements(driver, ManagePeoplePage.class);
	}
	
	public ContactRecordsPage selectManageProfileDetails() {
		manageYourProfileDetailsBtn.click();
		return PageFactory.initElements(driver, ContactRecordsPage.class);
	}
	
	public NewsLetterSubscriptionPage selectManageSubscriptions() {
		helpDeskManageSubscriptionsBtn.click();
		return PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
	}

	public String checkPage() {
		return dashBoard.getText();
	}

	public DashboardPage checkAndAcceptCookies() {
		driver.manage().deleteAllCookies();
		
		if(!driver.findElements(By.id("block-cookiebanner")).isEmpty()) {
			driver.findElement(By.xpath("//button[contains(text(),'Accept')]")).click();
		}
		
		//try {
		//	driver.findElement(By.xpath("//button[contains(text(),'Accept')]")).click();
		//} catch (NoSuchElementException e) {
			// do nothing
		//}
		return PageFactory.initElements(driver, DashboardPage.class);
	}

}
