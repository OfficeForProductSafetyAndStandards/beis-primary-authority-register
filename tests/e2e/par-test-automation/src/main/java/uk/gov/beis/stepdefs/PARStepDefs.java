package uk.gov.beis.stepdefs;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertFalse;
import static org.junit.Assert.assertTrue;

import java.io.IOException;
import java.util.Map;

import org.apache.commons.lang3.RandomStringUtils;
import org.junit.Assert;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;

import cucumber.api.DataTable;
import cucumber.api.java.en.Given;
import cucumber.api.java.en.Then;
import cucumber.api.java.en.When;
import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.helper.LOG;
import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;

import uk.gov.beis.pageobjects.*;
import uk.gov.beis.pageobjects.HomePageLinkPageObjects.*;
import uk.gov.beis.pageobjects.UserManagement.*;
import uk.gov.beis.pageobjects.AuthorityPageObjects.*;
import uk.gov.beis.pageobjects.OrganisationPageObjects.*;
import uk.gov.beis.pageobjects.LegalEntityPageObjects.*;
import uk.gov.beis.pageobjects.PartnershipPageObjects.*;
import uk.gov.beis.pageobjects.TransferPartnerships.*;
import uk.gov.beis.pageobjects.UserDashboardPageObjects.*;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.*;
import uk.gov.beis.pageobjects.AdvicePageObjects.*;
import uk.gov.beis.pageobjects.EnforcementNoticePageObjects.*;
import uk.gov.beis.pageobjects.DeviationRequestPageObjects.*;
import uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects.*;
import uk.gov.beis.pageobjects.GeneralEnquiryPageObjects.*;
import uk.gov.beis.pageobjects.NewsLetterSubscriptionPageObjects.*;

import uk.gov.beis.utility.DataStore;
import uk.gov.beis.utility.RandomStringGenerator;

public class PARStepDefs {

	public static WebDriver driver;
	
	// PAR Home Page
	private HomePage homePage;
	private LocalRegulationPrimaryAuthorityPage localRegulationPrimaryAuthorityPage;
	private PrimaryAuthorityDocumentsPage primaryAuthorityDocumentsPage;
	private TermsAndConditionsPage termsAndConditionsPage;
	private CookiesPage cookiesPage;
	private OPSSPrivacyNoticePage opssPrivacyNoticePage;
	private AccessibilityStatementPage accessibilityStatementPage;
	private OpenGovernmentLicencePage openGovernmentLicencePage;
	private CrownCopyrightPage crownCopyrightPage;
	
	// Login
	private LoginPage loginPage;
	private PasswordPage passwordPage;
	private MailLogPage mailLogPage;
	
	// Dash-board
	private DashboardPage dashboardPage;
	private HelpDeskDashboardPage helpDeskDashboardPage;
	
	// Statistics
	private PARReportingPage parReportingPage;
	
	// User Management
	private ManagePeoplePage managePeoplePage;
	private ContactDetailsPage contactDetailsPage;
	private UserProfilePage userProfilePage;
	private GiveUserAccountPage giveUserAccountPage;
	private UserMembershipPage userMembershipPage;
	private UserRoleTypePage userTypePage;
	private ProfileReviewPage profileReviewPage;
	private ProfileCompletionPage profileCompletionPage;
	private ChoosePersonToAddPage choosePersonToAddPage;
	private AddMembershipConfirmationPage addMembershipConfirmationPage;
	
	// Contact Record
	private ContactRecordsPage contactRecordsPage;
	private ContactCommunicationPreferencesPage contactCommunicationPreferencesPage;
	private ContactUpdateSubscriptionPage contactUpdateSubscriptionPage;
	
	// Legal Entity
	private LegalEntityTypePage legalEntityTypePage;
	private LegalEntityReviewPage legalEntityReviewPage;
	private UpdateLegalEntityPage updateLegalEntityPage;
	private ConfirmThisAmendmentPage confirmThisAmendmentPage;
	private AmendmentCompletedPage amendmentCompletedPage;
	
	// Partnership
	private PartnershipTermsPage parPartnershipTermsPage;
	private PartnershipTypePage parPartnershipTypePage;
	private PartnershipDescriptionPage parPartnershipDescriptionPage;
	private RegulatoryFunctionPage regulatoryFunctionPage;
	private NumberOfEmployeesPage employeesPage;
	private CheckPartnershipInformationPage checkPartnershipInformationPage;
	private PartnershipInformationPage partnershipInformationPage;
	private PartnershipCompletionPage parPartnershipCompletionPage;
	private PartnershipApprovalPage partnershipApprovalPage;
	private PartnershipRevokedPage partnershipRevokedPage;
	private PartnershipRestoredPage partnershipRestoredPage;
	
	// Coordinated Partnership
	private MemberListPage memberListPage;
	private MembersListTypePage membersListTypePage;
	private MemberListCountPage memberListCountPage;
	private MembersListUpToDatePage membersListUpToDatePage;
	private UploadListOfMembersPage uploadListOfMembersPage;
	private ConfirmMemberUploadPage confirmMemberUploadPage;
	private MemberListUploadedPage memberListUploadedPage;
	private InspectionPlanCoveragePage inspectionPlanCoveragePage;
	private MemberOrganisationSummaryPage memberOrganisationSummaryPage;
	private MemberOrganisationAddedConfirmationPage memberOrganisationAddedConfirmationPage;
	private MembershipCeasedPage membershipCeasedPage;
	
	// Partnerships Transfer
	private AuthorityTransferSelectionPage authorityTransferSelectionPage;
	private PartnershipMigrationSelectionPage partnershipMigrationSelectionPage;
	private ConfirmThisTranferPage confirmThisTranferPage;
	private TransferCompletedPage transferCompletedPage;
	
	// Authority
	private AuthoritiesSearchPage authoritiesSearchPage;
	private AuthorityPage parAuthorityPage;
	private AuthorityNamePage authorityNamePage;
	private ONSCodePage onsCodePage;
	private AuthorityConfirmationPage authorityConfirmationPage;
	private AuthorityAddressDetailsPage authorityAddressDetailsPage;
	private AuthorityTypePage authorityTypePage;
	
	// Business
	private OrganisationsSearchPage organisationsSearchPage;
	private BusinessNamePage businessNamePage;
	private AddOrganisationNamePage addOrganisationNamePage;
	private AboutTheOrganisationPage aboutTheOrganisationPage;
	private SICCodePage sicCodePage;
	private TradingPage tradingPage;
	private BusinessDetailsPage businessDetailsPage;
	
	// Search Pages
	public PublicRegistrySearchPage publicRegistrySearchPage;
	private PartnershipSearchPage partnershipSearchPage;
	private PartnershipAdvancedSearchPage partnershipAdvancedSearchPage;
	private InspectionPlanSearchPage inspectionPlanSearchPage;
	private AdviceNoticeSearchPage adviceNoticeSearchPage;
	private EnforcementSearchPage enforcementSearchPage;
	private DeviationSearchPage deviationSearchPage;
	private InspectionFeedbackSearchPage inspectionFeedbackSearchPage;
	private EnquiriesSearchPage enquiriesSearchPage;
	
	// Inspection Plan
	private UploadInspectionPlanPage uploadInspectionPlanPage;
	private InspectionPlanReviewPage inspectionPlanReviewPage;
	private InspectionPlanDetailsPage inspectionPlanDetailsPage;
	
	// Advice
	private UploadAdviceNoticePage uploadAdviceNoticePage;
	private AdviceNoticeDetailsPage adviceNoticeDetailsPage;
	private AdviceArchivePage adviceArchivePage;
	
	// Enforcement Notice
	private ProposedEnforcementPage proposedEnforcementPage;
	private EnforcementReviewPage enforcementReviewPage;
	private EnforcementNotificationPage enforcementNotificationPage;
	private EnforcementCompletionPage enforcementCompletionPage;
	private EnforcementActionPage enforcementActionPage;
	private EnforcementDetailsPage enforcementDetailsPage;
	private EnforceLegalEntityPage enforceLegalEntityPage;
	private EnforcementOfficerContactDetailsPage enforcementOfficerContactDetailsPage;
	private RemoveEnforcementPage removeEnforcementPage;
	
	// Deviation Request
	private RequestDeviationPage requestDeviationPage;
	private DeviationCompletionPage deviationCompletionPage;
	private DeviationReviewPage deviationReviewPage;
	private DeviationApprovalPage deviationApprovalPage;
	private ReplyDeviationRequestPage replyDeviationRequestPage;
	
	// Inspection Plan Feedback
	private InspectionFeedbackDetailsPage inspectionFeedbackDetailsPage;
	private InspectionFeedbackConfirmationPage inspectionFeedbackConfirmationPage;
	private InspectionFeedbackCompletionPage inspectionFeedbackCompletionPage;
	private ReplyInspectionFeedbackPage replyInspectionFeedbackPage;
	
	// General Enquiry
	private RequestEnquiryPage requestEnquiryPage;
	private EnquiryCompletionPage enquiryCompletionPage;
	private EnquiryReviewPage enquiryReviewPage;
	private ReplyEnquiryPage replyEnquiryPage;
	
	// PAR News Letter
	private NewsLetterSubscriptionPage newsLetterSubscriptionPage;
	private NewsLetterManageSubscriptionListPage newsLetterManageSubscriptionListPage;
	private NewsLetterSubscriptionReviewPage newsLetterSubscriptionReviewPage;
	
	// Shared Pages
	private DeclarationPage declarationPage;
	private AddAddressPage addAddressPage;
	private AccountInvitePage accountInvitePage;
	private EnterTheDatePage enterTheDatePage;
	private CompletionPage completionPage;
	private RevokePage revokePage;
	private ReinstatePage reinstatePage;
	private BlockPage blockPage;
	private RemovePage removePage;
	private DeletePage deletePage;
	
	public PARStepDefs() throws ClassNotFoundException, IOException {
		driver = ScenarioContext.lastDriver;
		
		// PAR Home Page
		homePage = PageFactory.initElements(driver, HomePage.class);
		localRegulationPrimaryAuthorityPage = PageFactory.initElements(driver, LocalRegulationPrimaryAuthorityPage.class);
		primaryAuthorityDocumentsPage = PageFactory.initElements(driver, PrimaryAuthorityDocumentsPage.class);
		termsAndConditionsPage = PageFactory.initElements(driver, TermsAndConditionsPage.class);
		cookiesPage = PageFactory.initElements(driver, CookiesPage.class);
		opssPrivacyNoticePage = PageFactory.initElements(driver, OPSSPrivacyNoticePage.class);
		accessibilityStatementPage = PageFactory.initElements(driver, AccessibilityStatementPage.class);
		openGovernmentLicencePage = PageFactory.initElements(driver, OpenGovernmentLicencePage.class);
		crownCopyrightPage = PageFactory.initElements(driver, CrownCopyrightPage.class);
		
		// Login
		loginPage = PageFactory.initElements(driver, LoginPage.class);
		passwordPage = PageFactory.initElements(driver, PasswordPage.class);
		mailLogPage = PageFactory.initElements(driver, MailLogPage.class);
		
		// Dash-board
		dashboardPage = PageFactory.initElements(driver, DashboardPage.class);
		helpDeskDashboardPage = PageFactory.initElements(driver, HelpDeskDashboardPage.class);
		
		// Statistics
		parReportingPage = PageFactory.initElements(driver, PARReportingPage.class);
		
		// User Management
		profileReviewPage = PageFactory.initElements(driver, ProfileReviewPage.class);
		profileCompletionPage = PageFactory.initElements(driver, ProfileCompletionPage.class);
		contactDetailsPage = PageFactory.initElements(driver, ContactDetailsPage.class);
		managePeoplePage = PageFactory.initElements(driver, ManagePeoplePage.class);
		userMembershipPage = PageFactory.initElements(driver, UserMembershipPage.class);
		giveUserAccountPage = PageFactory.initElements(driver, GiveUserAccountPage.class);
		userTypePage = PageFactory.initElements(driver, UserRoleTypePage.class);
		userProfilePage = PageFactory.initElements(driver, UserProfilePage.class);
		choosePersonToAddPage  = PageFactory.initElements(driver, ChoosePersonToAddPage.class);
		addMembershipConfirmationPage  = PageFactory.initElements(driver, AddMembershipConfirmationPage.class);
		
		// Contact Record
		contactUpdateSubscriptionPage = PageFactory.initElements(driver, ContactUpdateSubscriptionPage.class);
		contactRecordsPage = PageFactory.initElements(driver, ContactRecordsPage.class);
		contactCommunicationPreferencesPage = PageFactory.initElements(driver,ContactCommunicationPreferencesPage.class);
		
		// Legal Entity
		legalEntityTypePage = PageFactory.initElements(driver, LegalEntityTypePage.class);
		legalEntityReviewPage = PageFactory.initElements(driver, LegalEntityReviewPage.class);
		updateLegalEntityPage = PageFactory.initElements(driver, UpdateLegalEntityPage.class);
		confirmThisAmendmentPage = PageFactory.initElements(driver, ConfirmThisAmendmentPage.class);
		amendmentCompletedPage = PageFactory.initElements(driver, AmendmentCompletedPage.class);
		
		// Partnership
		partnershipInformationPage = PageFactory.initElements(driver, PartnershipInformationPage.class);
		parPartnershipTypePage = PageFactory.initElements(driver, PartnershipTypePage.class);
		parPartnershipDescriptionPage = PageFactory.initElements(driver, PartnershipDescriptionPage.class);
		declarationPage = PageFactory.initElements(driver, DeclarationPage.class);
		employeesPage = PageFactory.initElements(driver, NumberOfEmployeesPage.class);
		partnershipRestoredPage = PageFactory.initElements(driver, PartnershipRestoredPage.class);
		partnershipRevokedPage = PageFactory.initElements(driver, PartnershipRevokedPage.class);
		partnershipApprovalPage = PageFactory.initElements(driver, PartnershipApprovalPage.class);
		regulatoryFunctionPage = PageFactory.initElements(driver, RegulatoryFunctionPage.class);
		parPartnershipCompletionPage = PageFactory.initElements(driver, PartnershipCompletionPage.class);
		parPartnershipTermsPage = PageFactory.initElements(driver, PartnershipTermsPage.class);
		checkPartnershipInformationPage = PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
		
		// Coordinated Partnership
		memberListPage = PageFactory.initElements(driver, MemberListPage.class);
		membersListTypePage = PageFactory.initElements(driver, MembersListTypePage.class);
		memberListCountPage = PageFactory.initElements(driver, MemberListCountPage.class);
		membersListUpToDatePage = PageFactory.initElements(driver, MembersListUpToDatePage.class);
		membershipCeasedPage = PageFactory.initElements(driver, MembershipCeasedPage.class);
		uploadListOfMembersPage = PageFactory.initElements(driver, UploadListOfMembersPage.class);
		confirmMemberUploadPage = PageFactory.initElements(driver, ConfirmMemberUploadPage.class);
		memberListUploadedPage = PageFactory.initElements(driver, MemberListUploadedPage.class);
		inspectionPlanCoveragePage = PageFactory.initElements(driver, InspectionPlanCoveragePage.class);
		memberOrganisationSummaryPage = PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
		memberOrganisationAddedConfirmationPage = PageFactory.initElements(driver, MemberOrganisationAddedConfirmationPage.class);
		
		// Partnerships Transfer
		authorityTransferSelectionPage = PageFactory.initElements(driver, AuthorityTransferSelectionPage.class);
		partnershipMigrationSelectionPage = PageFactory.initElements(driver, PartnershipMigrationSelectionPage.class);
		confirmThisTranferPage = PageFactory.initElements(driver, ConfirmThisTranferPage.class);
		transferCompletedPage = PageFactory.initElements(driver, TransferCompletedPage.class);
		
		// Authority
		authoritiesSearchPage = PageFactory.initElements(driver, AuthoritiesSearchPage.class);
		parAuthorityPage = PageFactory.initElements(driver, AuthorityPage.class);
		authorityTypePage = PageFactory.initElements(driver, AuthorityTypePage.class);
		authorityAddressDetailsPage = PageFactory.initElements(driver, AuthorityAddressDetailsPage.class);
		authorityNamePage = PageFactory.initElements(driver, AuthorityNamePage.class);
		onsCodePage = PageFactory.initElements(driver, ONSCodePage.class);
		authorityConfirmationPage = PageFactory.initElements(driver, AuthorityConfirmationPage.class);
		
		// Business
		organisationsSearchPage = PageFactory.initElements(driver, OrganisationsSearchPage.class);
		addOrganisationNamePage = PageFactory.initElements(driver, AddOrganisationNamePage.class);
		tradingPage = PageFactory.initElements(driver, TradingPage.class);
		sicCodePage = PageFactory.initElements(driver, SICCodePage.class);
		aboutTheOrganisationPage = PageFactory.initElements(driver, AboutTheOrganisationPage.class);
		businessNamePage = PageFactory.initElements(driver, BusinessNamePage.class);
		businessDetailsPage = PageFactory.initElements(driver, BusinessDetailsPage.class);
		
		// Search Pages
		publicRegistrySearchPage = PageFactory.initElements(driver, PublicRegistrySearchPage.class);
		partnershipSearchPage = PageFactory.initElements(driver, PartnershipSearchPage.class);
		partnershipAdvancedSearchPage = PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
		inspectionPlanSearchPage = PageFactory.initElements(driver, InspectionPlanSearchPage.class);
		adviceNoticeSearchPage = PageFactory.initElements(driver, AdviceNoticeSearchPage.class);
		enforcementSearchPage = PageFactory.initElements(driver, EnforcementSearchPage.class);
		deviationSearchPage = PageFactory.initElements(driver, DeviationSearchPage.class);
		inspectionFeedbackSearchPage = PageFactory.initElements(driver, InspectionFeedbackSearchPage.class);
		enquiriesSearchPage = PageFactory.initElements(driver, EnquiriesSearchPage.class);
		
		// Inspection Plan
		uploadInspectionPlanPage = PageFactory.initElements(driver, UploadInspectionPlanPage.class);
		inspectionPlanDetailsPage = PageFactory.initElements(driver, InspectionPlanDetailsPage.class);
		inspectionPlanReviewPage = PageFactory.initElements(driver, InspectionPlanReviewPage.class);
		
		// Advice
		adviceNoticeDetailsPage = PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
		uploadAdviceNoticePage = PageFactory.initElements(driver, UploadAdviceNoticePage.class);
		adviceArchivePage = PageFactory.initElements(driver, AdviceArchivePage.class);
		
		// Enforcement Notice
		proposedEnforcementPage = PageFactory.initElements(driver, ProposedEnforcementPage.class);
		enforcementActionPage = PageFactory.initElements(driver, EnforcementActionPage.class);
		enforcementDetailsPage = PageFactory.initElements(driver, EnforcementDetailsPage.class);
		enforceLegalEntityPage = PageFactory.initElements(driver, EnforceLegalEntityPage.class);
		enforcementNotificationPage = PageFactory.initElements(driver, EnforcementNotificationPage.class);
		enforcementReviewPage = PageFactory.initElements(driver, EnforcementReviewPage.class);
		enforcementCompletionPage = PageFactory.initElements(driver, EnforcementCompletionPage.class);
		removeEnforcementPage = PageFactory.initElements(driver, RemoveEnforcementPage.class);
		
		// Deviation Request
		requestDeviationPage = PageFactory.initElements(driver, RequestDeviationPage.class);
		deviationReviewPage = PageFactory.initElements(driver, DeviationReviewPage.class);
		deviationCompletionPage = PageFactory.initElements(driver, DeviationCompletionPage.class);
		deviationApprovalPage = PageFactory.initElements(driver, DeviationApprovalPage.class);
		replyDeviationRequestPage = PageFactory.initElements(driver, ReplyDeviationRequestPage.class);
		
		// Inspection Plan Feedback
		inspectionFeedbackDetailsPage = PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
		inspectionFeedbackCompletionPage = PageFactory.initElements(driver, InspectionFeedbackCompletionPage.class);
		inspectionFeedbackConfirmationPage = PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
		replyInspectionFeedbackPage = PageFactory.initElements(driver, ReplyInspectionFeedbackPage.class);
		
		// General Enquiry
		requestEnquiryPage = PageFactory.initElements(driver, RequestEnquiryPage.class);
		enquiryReviewPage = PageFactory.initElements(driver, EnquiryReviewPage.class);
		enquiryCompletionPage = PageFactory.initElements(driver, EnquiryCompletionPage.class);
		replyEnquiryPage = PageFactory.initElements(driver, ReplyEnquiryPage.class);
		
		// PAR News Letter
		newsLetterSubscriptionPage = PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
		newsLetterManageSubscriptionListPage = PageFactory.initElements(driver,NewsLetterManageSubscriptionListPage.class);
		newsLetterSubscriptionReviewPage = PageFactory.initElements(driver, NewsLetterSubscriptionReviewPage.class);
		
		// Shared Pages
		enforcementOfficerContactDetailsPage = PageFactory.initElements(driver, EnforcementOfficerContactDetailsPage.class);
		addAddressPage = PageFactory.initElements(driver, AddAddressPage.class);
		accountInvitePage = PageFactory.initElements(driver, AccountInvitePage.class);
		enterTheDatePage = PageFactory.initElements(driver, EnterTheDatePage.class);
		completionPage = PageFactory.initElements(driver, CompletionPage.class);
		revokePage = PageFactory.initElements(driver, RevokePage.class);
		reinstatePage = PageFactory.initElements(driver, ReinstatePage.class);
		blockPage = PageFactory.initElements(driver, BlockPage.class);
		deletePage = PageFactory.initElements(driver, DeletePage.class);
		removePage = PageFactory.initElements(driver, RemovePage.class);
	}

	@Given("^the user is on the PAR home page$")
	public void the_user_is_on_the_PAR_home_page() throws Throwable {
		LOG.info("Navigating to PAR Home page but first accepting cookies if present");
		homePage.navigateToUrl();
	}

	@Given("^the user is on the PAR login page$")
	public void the_user_is_on_the_PAR_login_page() throws Throwable {
		LOG.info("Navigating to PAR login page - logging out user first if already logged in");
		loginPage.navigateToUrl();
	}

	@Given("^the user visits the login page$")
	public void the_user_wants_to_login() throws Throwable {
		homePage.selectLogin();
	}

	@When("^the user logs in with the \"([^\"]*)\" user credentials$")
	public void the_user_logs_in_with_the_user_credentials(String user) throws Throwable {
		DataStore.saveValue(UsableValues.LOGIN_USER, user);
		String pass = PropertiesUtil.getConfigPropertyValue(user);
		
		LOG.info("Logging in user with credentials; username: " + user + " and password " + pass);
		loginPage.enterLoginDetails(user, pass);
		loginPage.clickSignIn();
	}

	@Then("^the user is on the dashboard page$")
	public void the_user_is_on_the_dashboard_page() throws Throwable {
		LOG.info("Verify the user is on the Dashboard Page.");
		Assert.assertTrue("Failed: Dashboard Header was not found.", dashboardPage.checkPage());
	}
	
	@When("^the user accepts the analytics cookies$")
	public void the_user_accepts_the_analytics_cookies() throws Throwable {
	    dashboardPage.acceptCookies();
	}

	@Then("^analytical cookies have been accepted successfully$")
	public void analytical_cookies_have_been_accepted_successfully() throws Throwable {
		LOG.info("Verifying the Analytical Cookies have been Accepted.");
		Assert.assertTrue("Failed: Analytics Cookies have not been Accepted.", dashboardPage.checkCookiesAccepted());
		
		dashboardPage.hideCookieBanner();
		
		LOG.info("Verifying the Cookie Banner is not Displayed.");
		Assert.assertTrue("Failed: The Cookie Banner is still Displayed.", dashboardPage.checkCookieBannerExists());
	}

	@When("^the user creates a new \"([^\"]*)\" partnership application with the following details:$")
	public void the_user_creates_a_new_partnership_application_with_the_following_details(String type, DataTable details) throws Throwable {
		String authority = "";
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			authority = data.get("Authority");
			DataStore.saveValue(UsableValues.PARTNERSHIP_TYPE, type);
			DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, data.get("Partnership Info"));
			DataStore.saveValue(UsableValues.BUSINESS_NAME, RandomStringGenerator.getBusinessName(4));
			
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE1, data.get("AddressLine1"));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE2, data.get("AddressLine2"));
			
			DataStore.saveValue(UsableValues.BUSINESS_TOWN, data.get("Town"));
			DataStore.saveValue(UsableValues.BUSINESS_COUNTY, data.get("County"));
			DataStore.saveValue(UsableValues.BUSINESS_COUNTRY, data.get("Country"));
			DataStore.saveValue(UsableValues.BUSINESS_NATION, data.get("Nation"));
			DataStore.saveValue(UsableValues.BUSINESS_POSTCODE, data.get("Postcode"));
		}
		
		ScenarioContext.secondJourneyPart = false;
		
		LOG.info("Select apply new partnership");
		dashboardPage.selectApplyForNewPartnership();
		
		LOG.info("Choose authority");
		parAuthorityPage.selectAuthority(authority);
		
		LOG.info("Select partnership type");
		parPartnershipTypePage.selectPartnershipType(type);
		
		LOG.info("Accepting terms");
		parPartnershipTermsPage.acceptTerms();
		
		LOG.info("Entering partnership description");
		parPartnershipDescriptionPage.enterDescription(DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO));
		parPartnershipDescriptionPage.gotToBusinessNamePage();
		
		LOG.info("Entering business/organisation name");
		businessNamePage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		businessNamePage.goToAddressPage();
		
		LOG.info("Enter address details");
		addAddressPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY),
				DataStore.getSavedValue(UsableValues.BUSINESS_NATION), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		addAddressPage.goToAddContactDetailsPage();
		
		LOG.info("Enter contact details");
		contactDetailsPage.addContactDetails(details);
		contactDetailsPage.goToInviteUserAccountPage();
		
		LOG.info("Send invitation to user");
		accountInvitePage.sendInvite();
	}

	@Then("^the first part of the partnership application is successfully completed$")
	public void the_first_part_of_the_partnership_application_is_successfully_completed() throws Throwable {
		LOG.info("Verifying Partnership Details on the Review Page.");
		
		Assert.assertTrue("About the Partnership is not Displayed.", checkPartnershipInformationPage.verifyAboutThePartnership());
		Assert.assertTrue("Organisation Name is not Displayed.", checkPartnershipInformationPage.verifyOrganisationName());
		Assert.assertTrue("Organisation Address is not Displayed.", checkPartnershipInformationPage.verifyOrganisationAddress());
		Assert.assertTrue("Organisation Contact is not Displayed.", checkPartnershipInformationPage.verifyContactAtTheOrganisation());
		Assert.assertTrue("Primary Authority name is not Displayed.", checkPartnershipInformationPage.verifyPrimaryAuthorityName());
		
		LOG.info("Complete Partnership Application.");
		checkPartnershipInformationPage.completeApplication();
		parPartnershipCompletionPage.clickDoneButton();
	}
	
	@When("^the user searches for the last created partnership$")
	public void the_user_searches_for_the_last_created_partnership() throws Throwable {
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
		case ("senior_administrator@example.com"):
		case ("secretary_state@example.com"):
			LOG.info("Selecting Search partnerships");
			helpDeskDashboardPage.selectSearchPartnerships();
			partnershipAdvancedSearchPage.searchPartnerships();
			break;
		case ("par_enforcement_officer@example.com"):
			LOG.info("Selecting Search for partnerships");
			dashboardPage.selectSearchforPartnership();
			partnershipSearchPage.searchPartnerships();
			break;
		case ("par_business_manager@example.com"):
		case ("par_business@example.com"):
			LOG.info("Selecting See your partnerships");
			dashboardPage.selectSeePartnerships();
			partnershipSearchPage.searchPartnerships();
			partnershipSearchPage.selectBusinessNameLinkFromPartnership();
			break;
		default:
			LOG.info("Search partnerships");
			dashboardPage.selectSeePartnerships();
			LOG.info("Select organisation link details");
			partnershipSearchPage.searchPartnerships();

			// select business/organisation link if still first part of journey
			if (!ScenarioContext.secondJourneyPart)
				partnershipSearchPage.selectBusinessNameLink();

			// select authority link if in second part of journey
			if (ScenarioContext.secondJourneyPart)
				partnershipSearchPage.selectAuthority(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		}
	}

	@When("^the user completes the partnership application with the following details:$")
	public void the_user_completes_the_partnership_application_with_the_following_details(DataTable details) throws Throwable {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.BUSINESS_DESC, data.get("Business Description"));
			DataStore.saveValue(UsableValues.CONTACT_NOTES, data.get("ContactNotes"));
			DataStore.saveValue(UsableValues.SIC_CODE, data.get("SIC Code"));
			
			switch (DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase()) {
			case ("direct"):
				DataStore.saveValue(UsableValues.NO_EMPLOYEES, data.get("No of Employees"));
				break;

			case ("co-ordinated"):
				DataStore.saveValue(UsableValues.MEMBERLIST_SIZE, data.get("Member List Size"));
				break;
			}
			
			DataStore.saveValue(UsableValues.TRADING_NAME, data.get("Trading Name"));
			DataStore.saveValue(UsableValues.ENTITY_NAME, data.get("Legal Entity Name"));
			DataStore.saveValue(UsableValues.ENTITY_TYPE, data.get("Legal entity Type"));
			DataStore.saveValue(UsableValues.ENTITY_NUMBER, data.get("Company number"));
		}
		
		LOG.info("Accepting terms");
		declarationPage.selectConfirmCheckbox();
		declarationPage.goToBusinessDetailsPage();
		
		LOG.info("Add business description");
		aboutTheOrganisationPage.enterDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		aboutTheOrganisationPage.clickContinueButton();
		
		LOG.info("Confirming address details");
		addAddressPage.goToAddContactDetailsPage();
		
		LOG.info("Confirming contact details");
		contactDetailsPage.selectPreferredEmail();
		contactDetailsPage.selectPreferredWorkphone();
		contactDetailsPage.selectPreferredMobilephone();
		contactDetailsPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		contactDetailsPage.goToSICCodePage();
		
		LOG.info("Selecting SIC Code");
		sicCodePage.selectSICCode(DataStore.getSavedValue(UsableValues.SIC_CODE));
		
		switch (DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase()) {

		case ("direct"):
			LOG.info("Selecting No of Employees");
			employeesPage.selectNoEmployees(DataStore.getSavedValue(UsableValues.NO_EMPLOYEES));
			break;

		case ("co-ordinated"):
			LOG.info("Selecting Membership List size");
			memberListPage.selectMemberSize(DataStore.getSavedValue(UsableValues.MEMBERLIST_SIZE));
			break;
		}

		LOG.info("Entering business trading name");
		tradingPage.enterTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
		tradingPage.goToLegalEntityTypePage();
		
		legalEntityTypePage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		legalEntityTypePage.clickContinueButton();
		
		legalEntityReviewPage.goToCheckPartnershipInformationPage();
		
		LOG.info("Set second part of journey part to true");
		ScenarioContext.secondJourneyPart = true;
	}
	
	@Then("^the second part of the partnership application is successfully completed$")
	public void the_second_part_of_the_partnership_application_is_successfully_completed() throws Throwable {
		LOG.info("Check and confirm changes");
		
		Assert.assertTrue("About the Organisation is not Displayed.", checkPartnershipInformationPage.verifyAboutTheOrganisation());
		Assert.assertTrue("Organisation Name is not Displayed.", checkPartnershipInformationPage.verifyOrganisationName());
		Assert.assertTrue("Organisation Address is not Displayed.", checkPartnershipInformationPage.verifyOrganisationAddress());
		Assert.assertTrue("Organisation Contact is not Displayed.", checkPartnershipInformationPage.verifyContactAtTheOrganisation());
		
		Assert.assertTrue("Primary SIC Code is not Displayed.", checkPartnershipInformationPage.verifyPrimarySICCode());
		
		switch (DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase()) {
			case ("direct"):
				LOG.info("Checking Employee Size.");
				Assert.assertTrue("Number of Employees is not Displayed.", checkPartnershipInformationPage.verifyNumberOfEmployees());
				break;
			case ("co-ordinated"):
				LOG.info("Checking Members Size.");
				Assert.assertTrue("Members Size is not Displayed.", checkPartnershipInformationPage.verifyMemberSize());
				break;
		}
		
		Assert.assertTrue("Legal Entity is not Displayed.", checkPartnershipInformationPage.verifyLegalEntity());
		Assert.assertTrue("Trading Name is not Displayed.", checkPartnershipInformationPage.verifyTradingName());
		
		LOG.info("Complete Partnership Application.");
		checkPartnershipInformationPage.confirmApplication();
		parPartnershipCompletionPage.clickDoneButton();
	}
	
	@Then("^the partnership application is completed successfully$")
	public void the_partnership_application_is_completed_successfully() throws Throwable {
		LOG.info("Verifying Partnership Information is Displayed.");
		
		assertTrue("Failed: The Organisation Name is not Correct.", partnershipInformationPage.verifyOrganisationName());
		assertTrue("Failed: The Primary Authority name is not Correct.", partnershipInformationPage.verifyPrimaryAuthorityName());
		assertTrue("Failed: About the Partnership is not Correct.", partnershipInformationPage.verifyAboutThePartnership());
		
		assertTrue("Failed: Organisation Address is not Correct.", partnershipInformationPage.checkOrganisationAddress());
		assertTrue("Failed: About the Organisation is not Correct.", partnershipInformationPage.checkAboutTheOrganisation());
		assertTrue("Failed The SIC Code is not Correct.", partnershipInformationPage.checkSICCode());
		assertTrue("Failed: The Legal Entity is not Correct.", partnershipInformationPage.verifyLegalEntity("Confirmed by the Organisation"));
		assertTrue("Failed: The Trading Name is not Correct.", partnershipInformationPage.verifyTradingName());
		assertTrue("Failed: The Organisation Contact is not Correct.", partnershipInformationPage.verifyContactAtTheOrganisation());
	}
	
	@When("^the user approves the partnership$")
	public void the_user_approves_the_partnership() throws Throwable {
		LOG.info("Approving last created partnership");
		partnershipAdvancedSearchPage.selectApproveBusinessNameLink();
		
		declarationPage.selectAuthorisedCheckbox();
		declarationPage.goToRegulatoryFunctionsPage();
		
		regulatoryFunctionPage.selectNormalOrSequencedFunctions();
		regulatoryFunctionPage.goToPartnershipApprovedPage();
		
		partnershipApprovalPage.completeApplication();
	}

	@When("^the user searches again for the last created partnership$")
	public void the_user_searches_again_for_the_last_created_partnership() throws Throwable {
		LOG.info("Searching for last created partnership");
		partnershipAdvancedSearchPage.searchPartnerships();
	}
	
	@When("^the user revokes the partnership$")
	public void the_user_revokes_the_partnership() throws Throwable {
		LOG.info("Revoking last created partnership");
		partnershipAdvancedSearchPage.selectRevokeBusinessNameLink();
		
		revokePage.enterReasonForRevocation("Test Revoke.");
		revokePage.goToPartnershipRevokedPage();
		
		partnershipRevokedPage.goToAdvancedPartnershipSearchPage();
	}

	@When("^the user restores the partnership$")
	public void the_user_restores_the_partnership() throws Throwable {
		LOG.info("Restoring last revoked partnership");
		partnershipAdvancedSearchPage.selectRestoreBusinessNameLink();
		
		reinstatePage.goToPartnershipRestoredPage();
		partnershipRestoredPage.goToAdvancedPartnershipSearchPage();
	}

	@Then("^the partnership is displayed with Status \"([^\"]*)\" and Actions \"([^\"]*)\"$")
	public void the_partnership_is_displayed_with_Status_and_Actions(String status, String action) throws Throwable {
		LOG.info("Check status of partnership is: " + status + " and action is: " + action);
		partnershipAdvancedSearchPage.checkPartnershipDetails(status, action);
	}
	
	@When("^the user searches for the last created partnership Authority$")
	public void the_user_searches_for_the_last_created_partnership_Authority() throws Throwable {
		LOG.info("Searching for and selecting the latest Partnerships Primary Authority.");
		
		helpDeskDashboardPage.selectSearchPartnerships();
		
		partnershipAdvancedSearchPage.searchPartnerships();
		partnershipAdvancedSearchPage.selectPrimaryAuthorityLink();
	}
	
	@When("^the user updates the About the Partnership and Regulatory Functions:$")
	public void the_user_updates_the_About_the_Partnership_and_Regulatory_Functions(DataTable details) throws Throwable {
		LOG.info("Updating about the Partnership and Regulatory Functions.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, data.get("About the Partnership"));
		}
		
		partnershipInformationPage.editAboutPartnership();
		parPartnershipDescriptionPage.enterDescription(DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO));
		parPartnershipDescriptionPage.clickSave();
		
		partnershipInformationPage.editRegulatoryFunctions();
		regulatoryFunctionPage.updateRegFunction();
	}

	@Then("^the About the Partnership and Regulatory Functions are updated Successfully$")
	public void the_About_the_Partnership_and_Regulatory_Functions_are_updated_Successfully() throws Throwable {
		LOG.info("Verifying About the Partnership and Regulatory Functions have been updated Successfully.");
		
		assertTrue(partnershipInformationPage.verifyAboutThePartnership());
		assertTrue(partnershipInformationPage.checkRegulatoryFunctions());
		
		partnershipInformationPage.clickSave();
	}
	
	@When("^the user searches for the last created partnership Organisation$")
	public void the_user_searches_for_the_last_created_partnership_Organisation() throws Throwable {
		LOG.info("Searching for and selecting the latest Partnerships Organisation.");
		
		partnershipAdvancedSearchPage.searchPartnerships();
		partnershipAdvancedSearchPage.selectOrganisationLink();
	}
	
	@When("^the user updates the Partnerships details with the following:$")
	public void the_user_updates_the_Partnerships_details_with_the_following(DataTable details) throws Throwable {
		LOG.info("Updating all the remaining Partnership details.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE1, data.get("Address1"));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE2, data.get("Address2"));
			DataStore.saveValue(UsableValues.BUSINESS_TOWN, data.get("Town"));
			DataStore.saveValue(UsableValues.BUSINESS_COUNTY, data.get("County"));
			DataStore.saveValue(UsableValues.BUSINESS_COUNTRY, data.get("Country"));
			DataStore.saveValue(UsableValues.BUSINESS_NATION, data.get("Nation Value"));
			DataStore.saveValue(UsableValues.BUSINESS_POSTCODE, data.get("Post Code"));
			DataStore.saveValue(UsableValues.BUSINESS_DESC, data.get("About the Organisation"));
			DataStore.saveValue(UsableValues.SIC_CODE, data.get("SIC Code"));
			DataStore.saveValue(UsableValues.TRADING_NAME, data.get("Trading Name"));
		}
		
		partnershipInformationPage.editOrganisationAddress();
		addAddressPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY), 
				DataStore.getSavedValue(UsableValues.BUSINESS_NATION), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		addAddressPage.clickSaveButton();
		
		LOG.info("Selected Country: " + DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY));
		LOG.info("Selected Nation: " + DataStore.getSavedValue(UsableValues.BUSINESS_NATION));
		
		partnershipInformationPage.editAboutTheOrganisation();
		parPartnershipDescriptionPage.updateBusinessDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		parPartnershipDescriptionPage.clickSave();
		
		partnershipInformationPage.editSICCode();
		sicCodePage.editSICCode(DataStore.getSavedValue(UsableValues.SIC_CODE));
		
		partnershipInformationPage.editTradingName();
		tradingPage.editTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
	}

	@Then("^all of the Partnership details have been updated successfully$")
	public void all_of_the_Partnership_details_have_been_updated_successfully() throws Throwable {
		LOG.info("Verifying all the remaining Partnership details have been updated Successfully.");

		assertTrue(partnershipInformationPage.checkOrganisationAddress());
		assertTrue(partnershipInformationPage.checkAboutTheOrganisation());
		assertTrue(partnershipInformationPage.checkSICCode());
		assertTrue(partnershipInformationPage.verifyTradingName());
		
		partnershipInformationPage.clickSave();
	}
	
	@When("^the user Amends the legal entities with the following details:$")
	public void the_user_Amends_the_legal_entities_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Creating the Legal Entity Amendment as the Authority User.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENTITY_TYPE, data.get("Entity Type"));
			DataStore.saveValue(UsableValues.ENTITY_NAME, data.get("Entity Name"));
		}
		
		partnershipInformationPage.selectAmendLegalEntitiesLink();
		
		legalEntityTypePage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		legalEntityTypePage.clickContinueButton();
		legalEntityReviewPage.goToConfirmThisAmendmentPage();
		
		confirmThisAmendmentPage.confirmLegalEntityAmendments();
		
		amendmentCompletedPage.goToPartnershipDetailsPage();
	}
	
	@Then("^the user verifies the amendments are created successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_amendments_are_created_successfully_with_status(String status) throws Throwable {
		LOG.info("Verify the Legal Entity was Created Successfully.");
		assertTrue(partnershipInformationPage.verifyLegalEntity(status));
	}

	@When("^the user confirms the legal entity amendments$")
	public void the_user_confirms_the_legal_entity_amendments() throws Throwable {
		LOG.info("Confirm the Legal Entity as the Business User.");
		
		partnershipInformationPage.selectConfirmLegalEntitiesLink();
		confirmThisAmendmentPage.confirmLegalEntityAmendments();
		amendmentCompletedPage.goToDashBoardPage();
	}
	
	@Then("^the user verifies the amendments are confirmed successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_amendments_are_confirmed_successfully_with_status(String status) throws Throwable {
		LOG.info("Search for the Partnership to Verify the Amendment.");
		dashboardPage.selectSeePartnerships();
		partnershipSearchPage.searchPartnerships();
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		
		LOG.info("Verify the Legal Entity was Confirmed Successfully.");
		assertTrue(partnershipInformationPage.verifyLegalEntity(status));
	}

	@When("^the user nominates the legal entity amendments$")
	public void the_user_nominates_the_legal_entity_amendments() throws Throwable {
		LOG.info("Nominate the Legal Entity as the Help Desk User.");
		
		partnershipInformationPage.selectNominateLegalEntitiesLink();
		confirmThisAmendmentPage.confirmLegalEntityAmendments();
		amendmentCompletedPage.goToPartnershipDetailsPage();
	}
	
	@Then("^the user verifies the amendments are nominated successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_amendments_are_nominated_successfully_with_status(String status) throws Throwable {
		LOG.info("Verify the Legal Entity was Nominated Successfully.");
		assertTrue(partnershipInformationPage.verifyLegalEntity(status));
	}
	
	@When("^the user revokes the legal entity with the reason \"([^\"]*)\"$")
	public void the_user_revokes_the_legal_entity_with_the_reason(String reason) throws Throwable {
		LOG.info("Revoke the Legal Entity as the Help Desk User.");
		
		partnershipInformationPage.selectRevokeLegalEntitiesLink();
		
		revokePage.enterReasonForRevocation(reason);
		revokePage.goToPartnershipDetailsPage();
	}

	@Then("^the user verifies the legal entity was revoked successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_legal_entity_was_revoked_successfully_with_status(String status) throws Throwable {
		LOG.info("Verify the Legal Entity was Revoked Successfully.");
		assertTrue(partnershipInformationPage.verifyLegalEntity(status));
	}
	
	@When("^the user reinstates the legal entity$")
	public void the_user_reinstates_the_legal_entity() throws Throwable {
		LOG.info("Reinstate the Legal Entity as the Help Desk User.");
		
		partnershipInformationPage.selectReinstateLegalEntitiesLink();
		reinstatePage.goToPartnershipDetailsPage();
	}

	@Then("^the user verifies the legal entity was reinstated successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_legal_entity_was_reinstated_successfully_with_status(String status) throws Throwable {
		LOG.info("Verify the Legal Entity was Reinstated Successfully.");
		assertTrue(partnershipInformationPage.verifyLegalEntity(status));
	}
	
	@When("^the user removes the legal entity$")
	public void the_user_removes_the_legal_entity() throws Throwable {
		LOG.info("Remove the Legal Entity as the Help Desk User.");
		
		partnershipInformationPage.selectRemoveLegalEntitiesLink();
		removePage.goToPartnershipDetailsPage();
	}

	@Then("^the user verifies the legal entity was removed successfully$")
	public void the_user_verifies_the_legal_entity_was_removed_successfully() throws Throwable {
		LOG.info("Verify the Legal Entity was Removed Successfully.");
		assertTrue(partnershipInformationPage.verifyLegalEnityExists());
	}
	
	@When("^the user adds a Primary Authority contact to be Invited with the following details:$")
	public void the_user_adds_a_Primary_Authority_contact_to_be_Invited_with_the_following_details(DataTable details) throws Throwable {
		
		partnershipInformationPage.addAnotherAuthorityContactButton();

		LOG.info("Adding new contact details.");
		contactDetailsPage.setContactDetailsWithRandomName(details);
		
		contactDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		contactDetailsPage.enterFirstName(DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME));
		contactDetailsPage.enterLastName(DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME));
		contactDetailsPage.enterWorkNumber(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		contactDetailsPage.enterMobileNumber(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		contactDetailsPage.enterEmail(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		
		contactDetailsPage.selectRandomPreferredCommunication();
		contactDetailsPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		
		contactDetailsPage.selectContinueButton();
		
		LOG.info("Reviewing the Contact Details..");
		profileReviewPage.clickSaveButton();
	}
	
	@Then("^the new Primary Authority contact is added Successfully$")
	public void the_new_Primary_Authority_contact_is_added_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact is added successfully.");
		assertTrue("Failed: Contact Details are not Displayed Correctly.", partnershipInformationPage.checkContactDetails());
	}
	
	@When("^the user removes the new Primary Authority contact$")
	public void the_user_removes_the_new_Primary_Authority_contact() throws Throwable {
		partnershipInformationPage.removeContactsDetailsButton();
		
		LOG.info("Removing the contact.");
		removePage.goToPartnershipDetailsPage();
	}

	@Then("^the new Primary Authority contact is removed Successfully$")
	public void the_new_Primary_Authority_contact_is_removed_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact was removed successfully.");
		assertTrue("Failed: Contact was not Removed.", partnershipInformationPage.checkContactExists());
	}
	
	@When("^the user adds a new Organisation contact to be Invited with the following details:$")
	public void the_user_adds_a_new_Organisation_contact_to_be_Invited_with_the_following_details(DataTable details) throws Throwable {
		partnershipInformationPage.addAnotherOrganisationContactButton();

		LOG.info("Adding new contact details.");
		contactDetailsPage.setContactDetailsWithRandomName(details);
		
		contactDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		contactDetailsPage.enterFirstName(DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME));
		contactDetailsPage.enterLastName(DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME));
		contactDetailsPage.enterWorkNumber(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		contactDetailsPage.enterMobileNumber(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		contactDetailsPage.enterEmail(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		
		contactDetailsPage.selectRandomPreferredCommunication();
		contactDetailsPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		
		contactDetailsPage.selectContinueButton();
		
		LOG.info("Reviewing the Contact Details.");
		profileReviewPage.clickSaveButton();
	}

	@Then("^the new Organisation contact is added Successfully$")
	public void the_new_Organisation_contact_is_added_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact is added successfully.");
		assertTrue("Failed: Contact Details are not Displayed Correctly.", partnershipInformationPage.checkContactDetails());
	}
	
	@When("^the user removes the new Organisation contact$")
	public void the_user_removes_the_new_Organisation_contact() throws Throwable {
		partnershipInformationPage.removeContactsDetailsButton();
		
		LOG.info("Removing the contact.");
		removePage.goToPartnershipDetailsPage();
	}

	@Then("^the new Organisation contact is removed Successfully$")
	public void the_new_Organisation_contact_is_removed_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact was removed successfully.");
		assertTrue("Failed: Contact was not Removed.", partnershipInformationPage.checkContactExists());
	}
	
	@When("^the user uploads an inspection plan against the partnership with the following details:$")
	public void the_user_uploads_an_inspection_plan_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Upload inspection plan and save details");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_DESCRIPTION, data.get("Description"));
		}
		
		partnershipAdvancedSearchPage.selectPartnershipLink();
		partnershipInformationPage.selectSeeAllInspectionPlans();
		
		inspectionPlanSearchPage.selectUploadLink();
		
		uploadInspectionPlanPage.chooseFile("link.txt");
		uploadInspectionPlanPage.uploadFile();
		
		inspectionPlanDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE));
		inspectionPlanDetailsPage.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
		inspectionPlanDetailsPage.clickSave();
		
		enterTheDatePage.enterDate("ddMMYYYY");
		enterTheDatePage.goToInspectionPlanSearchPage();
	}
	
	@Then("^the inspection plan is uploaded successfully$")
	public void the_inspection_plan_is_uploaded_successfully() throws Throwable {
		LOG.info("Verifying the Inpsection Plan Status is set to Current.");
		Assert.assertTrue("Failed: Inspection Plan Status is not set to Current.", inspectionPlanSearchPage.getPlanStatus().equalsIgnoreCase("Current"));
	}

	@When("^the user updates the last created inspection plan against the partnership with the following details:$")
	public void the_user_updates_the_last_created_inspection_plan_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Edit inspection plan and save details.");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_DESCRIPTION, data.get("Description"));
		}
		
		partnershipAdvancedSearchPage.selectPartnershipLink();
		partnershipInformationPage.selectSeeAllInspectionPlans();
		
		inspectionPlanSearchPage.selectEditLink();
		
		inspectionPlanDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE));
		inspectionPlanDetailsPage.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
		inspectionPlanDetailsPage.clickSave();
		
		enterTheDatePage.goToInspectionPlanSearchPage();
		inspectionPlanSearchPage.selectInspectionPlan();
	}

	@Then("^the inspection plan is updated correctly$")
	public void the_inspection_plan_is_updated_correctly() throws Throwable {
		LOG.info("Verify the Inspection Plan details are correct.");
		Assert.assertTrue("Failed: The Inspection Plan details are not correct.", inspectionPlanReviewPage.checkInspectionPlan());
	}
	
	@When("^the user uploads an advice notice against the partnership with the following details:$")
	public void the_user_uploads_an_advice_notice_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Upload advice notice and save details");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ADVICENOTICE_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_TYPE, data.get("Type of Advice"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_REGFUNCTION, data.get("Reg Function"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_DESCRIPTION, data.get("Description"));
		}
		
		partnershipAdvancedSearchPage.selectPartnershipLink();
		partnershipInformationPage.selectSeeAllAdviceNotices();
		
		adviceNoticeSearchPage.selectUploadLink();
		
		uploadAdviceNoticePage.chooseFile("link.txt");
		uploadAdviceNoticePage.uploadFile();
		
		adviceNoticeDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		adviceNoticeDetailsPage.selectAdviceType(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TYPE));
		adviceNoticeDetailsPage.selectRegulatoryFunction(DataStore.getSavedValue(UsableValues.ADVICENOTICE_REGFUNCTION));
		adviceNoticeDetailsPage.enterDescription(DataStore.getSavedValue(UsableValues.ADVICENOTICE_DESCRIPTION));
		adviceNoticeDetailsPage.clickSave();
	}
	
	@Then("^the advice notice it uploaded successfully and set to active$")
	public void the_advice_notice_it_uploaded_successfully_and_set_to_active() throws Throwable {
		LOG.info("Checking Advice notice status is set to \"Active\"");
		Assert.assertTrue("Failed: Status not set to \"Active\"", adviceNoticeSearchPage.getAdviceStatus().equalsIgnoreCase("Active"));
	}

	@When("^the user selects the edit advice action link$")
	public void the_user_selects_the_edit_advice_action_link() throws Throwable {
		LOG.info("Searching for the newly added Advice notice.");
		
		adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		adviceNoticeSearchPage.selectEditAdviceButton();
	}

	@When("^the user edits the advice notice with the following details:$")
	public void the_user_edits_the_advice_notice_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Editing Advice notice details.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ADVICENOTICE_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_TYPE, data.get("Type of Advice"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_DESCRIPTION, data.get("Description"));
		}
		
		adviceNoticeDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		adviceNoticeDetailsPage.selectAdviceType(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TYPE));
		adviceNoticeDetailsPage.enterDescription(DataStore.getSavedValue(UsableValues.ADVICENOTICE_DESCRIPTION));
		adviceNoticeDetailsPage.clickSave();
	}

	@Then("^the advice notice it updated successfully$")
	public void the_advice_notice_it_updated_successfully() throws Throwable {
		LOG.info("Verifying Advice Notice status is set to Active.");
		Assert.assertTrue("Failed: Status not set to \"Active\"", adviceNoticeSearchPage.getAdviceStatus().equalsIgnoreCase("Active"));
	}

	@When("^the user archives the advice notice with the following reason \"([^\"]*)\"$")
	public void the_user_archives_the_advice_notice_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Archiving Advice Notice.");
		
		adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		adviceNoticeSearchPage.selectArchiveAdviceButton();
		
		adviceArchivePage.enterReasonForArchiving(reason);
	}

	@Then("^the advice notice is archived successfully$")
	public void the_advice_notice_is_archived_successfully() throws Throwable {
		LOG.info("Check Advice notice status is set to \"Archived\"");
		Assert.assertTrue("Failed: Status not set to \"Archived\"", adviceNoticeSearchPage.getAdviceStatus().equalsIgnoreCase("Archived"));
	}
	
	@When("^the user removes the advice notice with the following reason \"([^\"]*)\"$")
	public void the_user_removes_the_advice_notice_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Removing Advice Notice.");
		
		adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		adviceNoticeSearchPage.selectRemoveAdviceButton();
		
		removePage.enterRemoveReason(reason);
		removePage.goToAdviceNoticeSearchPage();
	}

	@Then("^the advice notice is removed successfully$")
	public void the_advice_notice_is_removed_successfully() throws Throwable {
		LOG.info("Check Advice notice was Removed.");
		adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		
		Assert.assertTrue("Failed: Advice Notice was not Removed.", adviceNoticeSearchPage.checkNoResultsReturned());
	}
	
	@When("^the user creates an enforcement notice against the partnership with the following details:$")
	public void the_user_creates_an_enforcement_notice_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENFORCEMENT_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_TYPE, data.get("Enforcement Action"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_DESCRIPTION, data.get("Description"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_REGFUNC, data.get("Regulatory Function"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_FILENAME, data.get("Attachment"));
		}
		
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		
		LOG.info("Create enformcement notification against partnership");
		partnershipInformationPage.createEnforcement();
		enforcementNotificationPage.clickContinue();
		
		enforcementOfficerContactDetailsPage.goToEnforceLegalEntityPage();
		
		enforceLegalEntityPage.enterLegalEntityName(DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		enforceLegalEntityPage.clickContinue();
		
		enforcementDetailsPage.selectEnforcementType(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TYPE));
		enforcementDetailsPage.enterEnforcementDescription(DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION));
		enforcementDetailsPage.clickContinue();
		
		enforcementActionPage.selectRegulatoryFunctions(DataStore.getSavedValue(UsableValues.ENFORCEMENT_REGFUNC));
		enforcementActionPage.chooseFile(DataStore.getSavedValue(UsableValues.ENFORCEMENT_FILENAME));
		enforcementActionPage.enterEnforcementDescription(DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION).toLowerCase());
		enforcementActionPage.enterTitle(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE));
		enforcementActionPage.clickContinue();
	}

	@Then("^all the fields for the enforcement notice are updated correctly$")
	public void all_the_fields_for_the_enforcement_are_updated_correctly() throws Throwable {
		LOG.info("Verify Enforcement Details are Correct.");
		Assert.assertTrue("Failed: Enforcement Details are not Correct.", enforcementReviewPage.checkEnforcementCreation());
		
		enforcementReviewPage.saveChanges();
		enforcementCompletionPage.goToPartnershipConfirmationPage();
	}

	@When("^the user selects the last created enforcement notice$")
	public void the_user_selects_the_last_created_enforcement() throws Throwable {
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_enforcement_officer@example.com"):
			LOG.info("Select last created enforcement");
			dashboardPage.selectSeeEnforcementNotices();
			enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
			enforcementSearchPage.selectEnforcement();
			break;

		case ("par_authority@example.com"):
			LOG.info("Select last created enforcement");
			dashboardPage.selectSeeEnforcementNotices();
			enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
			enforcementSearchPage.selectEnforcement();
			break;

		case ("par_helpdesk@example.com"):
			LOG.info("Searching for an Enforcement Notice.");
			helpDeskDashboardPage.selectManageEnforcementNotices();
			enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
			enforcementSearchPage.selectEnforcement();
			break;

		default:
			// do nothing
		}
	}

	@When("^the user approves the enforcement notice$")
	public void the_user_approves_the_enforcement_notice() throws Throwable {
		LOG.info("Approve the EnforcementNotice.");
		proposedEnforcementPage.selectAllow();
		proposedEnforcementPage.clickContinue();
		
		enforcementReviewPage.saveChanges();
		enforcementCompletionPage.clickDone();
	}

	@Then("^the enforcement notice is set to approved status$")
	public void the_enforcement_notice_is_set_to_approved_status() throws Throwable {
		LOG.info("Verify the Enforcement Notice was Approved.");
		enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		
		Assert.assertTrue("Failed: Enforcement Status is not correct.", enforcementSearchPage.getStatus().equalsIgnoreCase("Approved"));
	}
	
	@When("^the user searches for the last created enforcement notice$")
	public void the_user_searches_for_the_last_created_enforcement_notice() throws Throwable {
		LOG.info("Select last created enforcement");
		helpDeskDashboardPage.selectManageEnforcementNotices();
		enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		enforcementSearchPage.removeEnforcement();
	}
	
	@When("^the user removes the enforcement notice$")
	public void the_user_removes_the_enforcement_notice() throws Throwable {
		LOG.info("Remove the Enforcement Notice.");
		removeEnforcementPage.selectReasonForRemoval("This is a duplicate enforcement");
		removeEnforcementPage.enterReasonForRemoval("Test Remove.");
		removeEnforcementPage.clickContinue();
		
		declarationPage.selectConfirmCheckbox();
		declarationPage.goToEnforcementSearchPage();
	}

	@Then("^the enforcement notice is removed successfully$")
	public void the_enforcement_notice_is_removed_successfully() throws Throwable {
		LOG.info("Verify the Enforcement Notice was Removed Successfully.");
		enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		
		Assert.assertTrue("Failed: Enforcement Notice was not Removed.", enforcementSearchPage.confirmNoReturnedResults());
	}
	
	@When("^the user blocks the enforcement notice with the following reason: \"([^\"]*)\"$")
	public void the_user_blocks_the_enforcement_notice_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Block the Enforcement Notice.");
		proposedEnforcementPage.selectBlock();
		proposedEnforcementPage.enterReasonForBlockingEnforcement(reason);
		proposedEnforcementPage.clickContinue();
		
		enforcementReviewPage.saveChanges();
		enforcementCompletionPage.clickDone();
	}
	
	@Then("^the enforcement notice is set to blocked status$")
	public void the_enforcement_notice_is_set_to_blocked_status() throws Throwable {
		LOG.info("Verify the Enformcement Notice is Blocked.");
		enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		
		Assert.assertTrue("Failed: Enforcement was not Blocked.", enforcementSearchPage.getStatus().equalsIgnoreCase("Blocked"));
	}
	
	@When("^the user submits a deviation request against an inspection plan with the following details:$")
	public void the_user_submits_a_deviation_request_against_an_inspection_plan_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit Deviation Request");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.DEVIATION_DESCRIPTION, data.get("Description"));
		}
		
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		partnershipInformationPage.selectDeviateInspectionPlan();
		enforcementOfficerContactDetailsPage.goToDeviationRequestPage();
		
		requestDeviationPage.enterDescription(DataStore.getSavedValue(UsableValues.DEVIATION_DESCRIPTION));
		requestDeviationPage.chooseFile("link.txt");
		requestDeviationPage.clickContinue();
	}

	@Then("^the Deviation Request is created Successfully$")
	public void the_Deviation_Request_is_created_Successfully() throws Throwable {
		LOG.info("Verify the Deviation Request is created Successfully.");
		
		Assert.assertTrue("Failed: Deviation Request details are not displayed.", deviationReviewPage.checkDeviationCreation());
		deviationReviewPage.saveChanges();
		deviationCompletionPage.complete();
	}
	
	@When("^the user searches for the last created deviation request$")
	public void the_user_searches_for_the_last_created_deviation_request() throws Throwable {
		LOG.info("Search for last created deviation request");
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
			helpDeskDashboardPage.selectManageDeviationRequests();
			deviationSearchPage.selectDeviationRequest();
			break;
		default:
			dashboardPage.selectSeeDeviationRequests();
			deviationSearchPage.selectDeviationRequest();
		}
	}
	
	@When("^the user blocks the deviation request with the following reason: \"([^\"]*)\"$")
	public void the_user_blocks_the_deviation_request_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Block the deviation request.");
		
		deviationApprovalPage.selectBlock();
		deviationApprovalPage.enterReasonForBlocking(reason);
		deviationApprovalPage.clickContinue();
	}

	@Then("^the deviation request is set to blocked status$")
	public void the_deviation_request_is_set_to_blocked_status() throws Throwable {
		LOG.info("Check the Deviation Request is Blocked on the Review Page.");
		
		Assert.assertTrue("Failed: Deviation request status is not Set to Blocked.", deviationReviewPage.checkDeviationStatusBlocked());
		deviationReviewPage.saveChanges();
		deviationCompletionPage.complete();
	}

	@Then("^the user successfully approves the deviation request$")
	public void the_user_successfully_approves_the_deviation_request() throws Throwable {
		LOG.info("Approve the deviation request");
		deviationApprovalPage.selectAllow();
		deviationApprovalPage.clickContinue();
		
		Assert.assertTrue("Failed: Deviation request status not correct", deviationReviewPage.checkDeviationStatusApproved());
		deviationReviewPage.saveChanges();
		deviationCompletionPage.complete();
	}

	@When("^the user submits a response to the deviation request with the following details:$")
	public void the_user_submits_a_response_to_the_deviation_request_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit response to the deviation request");
		deviationSearchPage.selectDeviationRequest();
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1, data.get("Description"));
		}
		
		deviationReviewPage.submitResponse();
		
		replyDeviationRequestPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1));
		replyDeviationRequestPage.chooseFile("link.txt");
		replyDeviationRequestPage.clickSave();
	}
	
	@Then("^the response is displayed successfully$")
	public void the_response_is_displayed_successfully() throws Throwable {
		LOG.info("Verify the Deviation Request Response was Successful.");
		Assert.assertTrue("Failed: Deviation response is not displayed.", deviationReviewPage.checkDeviationResponse());
	}

	@When("^the user sends a reply to the deviation request message with the following details:$")
	public void the_user_sends_a_reply_to_the_deviation_request_message_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit reply to the deviation request");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1, data.get("Description"));
		}
		
		deviationReviewPage.submitResponse();
		
		replyDeviationRequestPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1));
		replyDeviationRequestPage.chooseFile("link.txt");
		replyDeviationRequestPage.clickSave();
	}

	@Then("^the deviation reply received successfully$")
	public void the_deviation_reply_received_successfully() throws Throwable {
		LOG.info("Verify the deviation response");
		Assert.assertTrue("Failed: Deviation Reply is not DIsplayed.", deviationReviewPage.checkDeviationResponse());
	}
	
	@When("^the user submits an inspection feedback against the inspection plan with the following details:$")
	public void the_user_submits_an_inspection_feedback_against_the_inspection_plan_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit inspection feedback against partnership");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION, data.get("Description"));
		}
		
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		partnershipInformationPage.selectSendInspectionFeedbk();
		
		enforcementOfficerContactDetailsPage.goToInspectionFeedbackDetailsPage();
		
		inspectionFeedbackDetailsPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION));
		inspectionFeedbackDetailsPage.chooseFile("link.txt");
		inspectionFeedbackDetailsPage.clickContinue();
	}
	
	@Then("^the inspection feedback is created successfully$")
	public void the_inspection_feedback_is_created_successfully() throws Throwable {
		LOG.info("Verifying the Inspection Feedback Details.");
		Assert.assertTrue("Failed: Inspection Feedback Details are Incorrect.", inspectionFeedbackConfirmationPage.checkInspectionFeedback());
		
		inspectionFeedbackConfirmationPage.goToInspectionFeedbackCompletionPage();
		inspectionFeedbackCompletionPage.complete();
	}

	@When("^the user searches for the last created inspection feedback$")
	public void the_user_searches_for_the_last_created_inspection_feedback() throws Throwable {
		LOG.info("Search for the last created Inspection Feedback.");
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
			helpDeskDashboardPage.selectManageInspectionFeedback();
			inspectionFeedbackSearchPage.selectInspectionFeedbackNotice();
			break;
		default:
			dashboardPage.selectSeeInspectionFeedbackNotices();
			inspectionFeedbackSearchPage.selectInspectionFeedbackNotice();
		}
	}

	@Then("^the user successfully approves the inspection feedback$")
	public void the_user_successfully_approves_the_inspection_feedback() throws Throwable {
		LOG.info("Verify the inspection feedback description");
		Assert.assertTrue("Failed: Inspection feedback description is not correct.", inspectionFeedbackConfirmationPage.checkInspectionFeedback());
	}
	
	@When("^the user submits a response to the inspection feedback with the following details:$")
	public void the_user_submits_a_response_to_the_inspection_feedback_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit response to inspection feedback request");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1, data.get("Description"));
		}

		inspectionFeedbackConfirmationPage.submitResponse();
		
		replyInspectionFeedbackPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1));
		replyInspectionFeedbackPage.chooseFile("link.txt");
		replyInspectionFeedbackPage.clickSave();
	}
	
	@When("^the user sends a reply to the inspection feedback message with the following details:$")
	public void the_user_sends_a_reply_to_the_inspection_feedback_message_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit reply to inspection feedback response");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1, data.get("Description"));
		}
		
		inspectionFeedbackConfirmationPage.submitResponse();
		
		replyInspectionFeedbackPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1));
		replyInspectionFeedbackPage.chooseFile("link.txt");
		replyInspectionFeedbackPage.clickSave();
	}
	
	@Then("^the inspection feedback response is displayed successfully$")
	public void the_inspection_feedback_response_is_displayed_successfully() throws Throwable {
		LOG.info("Verifying the Inspection Plan Feedback Response.");
		Assert.assertTrue("Failed: Inspection Feeback Response is not Displayed.", inspectionFeedbackConfirmationPage.checkInspectionResponse());
	}
	
	@When("^the user submits a general enquiry with the following details:$")
	public void the_user_submits_a_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Send general query");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENQUIRY_DESCRIPTION, data.get("Description"));
		}
		
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		partnershipInformationPage.sendGeneralEnquiry();
		
		enforcementOfficerContactDetailsPage.goToRequestEnquiryPage();
		
		requestEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION));
		requestEnquiryPage.chooseFile("link.txt");
		requestEnquiryPage.clickContinue();
	}
	
	@When("^the user sends a general enquiry for an enforcement notice with the following details:$")
	public void the_user_sends_a_general_enquiry_for_an_enforcement_notice_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Send general query");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENQUIRY_DESCRIPTION, data.get("Description"));
		}
		
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		partnershipInformationPage.createEnforcement();
		
		enforcementNotificationPage.selectDiscussEnforcement();
		
		enforcementOfficerContactDetailsPage.goToRequestEnquiryPage();
		
		requestEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION));
		requestEnquiryPage.chooseFile("link.txt");
		requestEnquiryPage.clickContinue();
	}

	@Then("^the Enquiry is created Successfully$")
	public void the_Enquiry_is_created_Successfully() throws Throwable {
		LOG.info("Verify the enquiry is created.");
		Assert.assertTrue("Failed: Enquiry details are not correct.", enquiryReviewPage.checkEnquiryDescription());
		
		enquiryReviewPage.saveChanges();
		enquiryCompletionPage.complete();
	}

	@When("^the user searches for the last created general enquiry$")
	public void the_user_searches_for_the_last_created_general_enquiry() throws Throwable {
		LOG.info("Search for last created enquiry");
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
			helpDeskDashboardPage.selectManageGeneralEnquiry();
			enquiriesSearchPage.selectEnquiry();
			break;
		default:
			dashboardPage.selectGeneralEnquiries();
			enquiriesSearchPage.selectEnquiry();
		}
	}
	
	@Then("^the general enquiry is recieved successfully$")
	public void the_general_enquiry_is_recieved_successfully() throws Throwable {
		LOG.info("Verifying the General Enquiry is Recieved.");
		Assert.assertTrue("Failed: Enquiry details are not correct.", enquiryReviewPage.checkEnquiryDetails());
	}
	
	@When("^the user submits a response to the general enquiry with the following details:$")
	public void the_user_submits_a_response_to_the_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit reply to the enquiry");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENQUIRY_REPLY, data.get("Description"));
		}
		
		enquiryReviewPage.submitResponse();
		
		replyEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY));
		replyEnquiryPage.chooseFile("link.txt");
		replyEnquiryPage.clickSave();
	}

	@When("^the user sends a reply to the general enquiry with the following details:$")
	public void the_user_sends_a_reply_to_the_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENQUIRY_REPLY, data.get("Description"));
		}
		
		enquiryReviewPage.submitResponse();
		
		replyEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY));
		replyEnquiryPage.chooseFile("link.txt");
		replyEnquiryPage.clickSave();
	}
	
	@Then("^the general enquiry response is displayed successfully$")
	public void the_general_enquiry_response_is_displayed_successfully() throws Throwable {
		LOG.info("Verifying General Enquiry Response.");
		Assert.assertTrue("Failed: General Enquiry Response is not Displayed Correctly.", enquiryReviewPage.checkEnquiryResponse());
	}
	
	@When("^the user revokes the last created inspection plan$")
	public void the_user_revokes_the_last_created_inspection_plan() throws Throwable {
		LOG.info("Revoking the last created Inspection Plan.");
		
		partnershipAdvancedSearchPage.selectPartnershipLink();
		partnershipInformationPage.selectSeeAllInspectionPlans();
		
		inspectionPlanSearchPage.selectRevokeLink();
		
		revokePage.enterReasonForRevocation("Test Revoke.");
		revokePage.goToInspectionPlanSearchPage();
	}

	@Then("^the inspection plan is revoked successfully$")
	public void the_inspection_plan_is_revoked_successfully() throws Throwable {
		LOG.info("Verifying the Inspection Plan was Revoked Successfully.");
		assertEquals("Failed: Inspection Plan was not Revoked.", inspectionPlanSearchPage.getPlanStatus(), "Revoked");
	}
	
	@When("^the user has revoked the last created inspection plan$")
	public void the_user_has_revoked_the_last_created_inspection_plan() throws Throwable {
		LOG.info("Removing the Inspection Plan.");
		inspectionPlanSearchPage.selectRemoveLink();
		
		removePage.enterRemoveReason("Test Remove.");
		removePage.goToInspectionPlanSearchPage();
	}

	@Then("^the inspection plan is successfully removed$")
	public void the_inspection_plan_is_successfully_removed() throws Throwable {
		LOG.info("Verifying the Inspection Plan was Removed Successfully.");
		assertEquals("Failed: Inspection Plan was not Removed.", inspectionPlanSearchPage.getPlanStatus(), "No results returned");
	}
	
	@When("^the user visits the maillog page and extracts the invite link$")
	public void the_user_visits_the_maillog_page_and_extracts_the_invite_link() throws Throwable {
		mailLogPage.navigateToUrl();
		mailLogPage.searchForUserAccountInvite(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		mailLogPage.selectEamilAndGetINviteLink(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
	}

	@When("^the user follows the invitation link$")
	public void the_user_follows_the_invitation_link() throws Throwable {
		loginPage.navigateToInviteLink();
	}

	@When("^the user completes the user creation journey$")
	public void the_user_completes_the_user_creation_journey() throws Throwable {
		LOG.info("Completing the User Creation Journey.");
		
		passwordPage.enterPassword("TestPassword", "TestPassword");
		passwordPage.selectRegister();
		
		declarationPage.selectDataPolicyCheckbox();
		declarationPage.goToContactDetailsPage();
		
		contactDetailsPage.goToContactCommunicationPreferencesPage();
		
		DataStore.saveValue(UsableValues.CONTACT_NOTES, "Test User Creation Note.");
		
		contactCommunicationPreferencesPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		contactCommunicationPreferencesPage.selectContinueButton();
		
		contactUpdateSubscriptionPage.subscribeToPARNews();
		contactUpdateSubscriptionPage.selectContinueButton();
	}

	@Then("^the user journey creation is successful$")
	public void the_user_journey_creation_is_successful() throws Throwable {
		LOG.info("Verify User Details are Correct.");
		
		assertTrue("Failed: Contact Details are not Displayed Correctly.", profileReviewPage.checkContactDetails());
		
		profileReviewPage.goToProfileCompletionPage();
		profileCompletionPage.goToDashboardPage();
	}
	
	@When("^the user Deletes the Partnership with the following reason: \"([^\"]*)\"$")
	public void the_user_Deletes_the_Partnership_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Delete the Partnership.");
		partnershipAdvancedSearchPage.selectDeletePartnershipLink();
		
		deletePage.enterReasonForDeletion(reason);
		deletePage.clickDeleteForPartnership();
		
		completionPage.clickDoneForPartnership();
	}

	@Then("^the Partnership was Deleted Successfully$")
	public void the_Partnership_was_Deleted_Successfully() throws Throwable {
		LOG.info("Verify the Partnership was Deleted Successfully.");
		
		partnershipAdvancedSearchPage.searchPartnerships();
		Assert.assertTrue("Failed: Partnership was not Deleted.", partnershipAdvancedSearchPage.checkPartnershipExists());
	}
	
	@When("^the user adds a single member organisation to the patnership with the following details:$")
	public void the_user_adds_a_single_member_organisation_to_the_patnership_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Add a Single Member Organisation to a Co-ordinated Partnership.");
		
		partnershipAdvancedSearchPage.selectOrganisationLink();
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.MEMBER_ORGANISATION_NAME, data.get("Organisation Name"));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE1, data.get("Address Line 1"));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE2, data.get("Address Line 2"));
			DataStore.saveValue(UsableValues.BUSINESS_TOWN, data.get("Town City"));
			DataStore.saveValue(UsableValues.BUSINESS_COUNTY, data.get("County"));
			DataStore.saveValue(UsableValues.BUSINESS_POSTCODE, data.get("Postcode"));
			DataStore.saveValue(UsableValues.ENTITY_TYPE, data.get("Legal Entity Type"));
			DataStore.saveValue(UsableValues.ENTITY_NAME, data.get("Legal Entity Name"));
		}
		
		partnershipInformationPage.selectShowMembersListLink();
		memberListPage.selectAddAMemberLink();
		
		LOG.info("Entering the Member Organisation's Name.");
		addOrganisationNamePage.enterMemberOrganisationName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		LOG.info("Entering the Member Organisation's Address.");
		authorityAddressDetailsPage.enterMemberOrganisationAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		
		LOG.info("Entering the Member Organisation's Contact Details.");
		contactDetailsPage.enterContactWithRandomName(details);
		contactDetailsPage.clickContinueButtonForMemberContact();
		
		LOG.info("Entering the Member Organisation's Membership Start Date.");
		enterTheDatePage.clickContinueButtonForMembershipBegan();
		
		LOG.info("Entering the Member Organisation's Trading Name.");
		tradingPage.addTradingNameForMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		LOG.info("Entering the Member Organisation's Legal Entity.");
		legalEntityTypePage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		legalEntityTypePage.clickContinueButton();
		legalEntityReviewPage.clickContinueForMember();
		
		LOG.info("Confirming the Member Organisation is covered by the Inspection Plan.");
		inspectionPlanCoveragePage.selectYesRadial();
		inspectionPlanCoveragePage.selectContinueForMember();
		
		LOG.info("Saving the Member Organisation's Details.");
		memberOrganisationSummaryPage.selectSave();
		memberOrganisationAddedConfirmationPage.selectDone();
	}

	@Then("^the user member organistion has been added to the partnership successfully$")
	public void the_user_member_organistion_has_been_added_to_the_partnership_successfully() throws Throwable {
		LOG.info("Verify the Member Organisation was added to the Co-ordinated Partnership Successfully.");
		
		memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		Assert.assertTrue("Failed: Member Organisation was not Created.", memberListPage.checkMemberCreated());
	}
	
	@When("^the user updates a single member organisation of the patnership with the following details:$")
	public void the_user_updates_a_single_member_organisation_of_the_patnership_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Update a Single Member Organisation to a Co-ordinated Partnership.");
		
		partnershipAdvancedSearchPage.selectOrganisationLink();
		partnershipInformationPage.selectShowMembersListLink();
		
		memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		memberListPage.selectMembersName();
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.MEMBER_ORGANISATION_NAME, data.get("Organisation Name"));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE1, data.get("Address Line 1"));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE2, data.get("Address Line 2"));
			DataStore.saveValue(UsableValues.BUSINESS_TOWN, data.get("Town City"));
			DataStore.saveValue(UsableValues.BUSINESS_COUNTY, data.get("County"));
			DataStore.saveValue(UsableValues.BUSINESS_COUNTRY, data.get("Country"));
			DataStore.saveValue(UsableValues.BUSINESS_NATION, data.get("Nation"));
			DataStore.saveValue(UsableValues.BUSINESS_POSTCODE, data.get("Postcode"));
			DataStore.saveValue(UsableValues.ENTITY_TYPE, data.get("Legal Entity Type"));
			DataStore.saveValue(UsableValues.ENTITY_NAME, data.get("Legal Entity Name"));
		}
		
		LOG.info("Updating the Member Organisation's Name.");
		memberOrganisationSummaryPage.selectEditOrganisationName();
		addOrganisationNamePage.editMemberOrganisationName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		LOG.info("Updating the Member Organisation's Address.");
		memberOrganisationSummaryPage.selectEditAddress();
		
		addAddressPage.editAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		
		addAddressPage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Membership Start Date.");
		memberOrganisationSummaryPage.selectEditMembershipStartDate();
		enterTheDatePage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Contact Details.");
		memberOrganisationSummaryPage.selectEditPerson();
		contactDetailsPage.enterContactWithRandomName(details);
		contactDetailsPage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Legal Entity.");
		memberOrganisationSummaryPage.selectAddAnotherLegalEntity();
		updateLegalEntityPage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		updateLegalEntityPage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Trading Name.");
		memberOrganisationSummaryPage.selectEditTradingName();
		tradingPage.editMemberTradingName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		tradingPage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Confirming the Member Organisation is not covered by the Inspection Plan.");
		memberOrganisationSummaryPage.selectEditCoveredByInspectionPlan();
		inspectionPlanCoveragePage.selectNoRadial();
		inspectionPlanCoveragePage.selectSaveForMember();
	}

	@Then("^the member organistion has been updated successfully$")
	public void the_member_organistion_has_been_updated_successfully() throws Throwable {
		LOG.info("Verifying All Member Details are Correct.");
		Assert.assertTrue(memberOrganisationSummaryPage.checkMemberDetails());
		memberOrganisationSummaryPage.selectDone();
		
		LOG.info("Verify the Updated Member Organisation Name is Displayed on the Members List.");
		memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		Assert.assertTrue("Failed: Member Organisation was not Updated.", memberListPage.checkMemberCreated());
	}
	
	@When("^the user Ceases a single member organisation of the patnership with the current date$")
	public void the_user_Ceases_a_single_member_organisation_of_the_patnership_with_the_current_date() throws Throwable {
		LOG.info("Cease a Single Member Organisation to a Co-ordinated Partnership.");
		
		partnershipAdvancedSearchPage.selectOrganisationLink();
		partnershipInformationPage.selectShowMembersListLink();
		
		memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		memberListPage.selectCeaseMembership();
		
		LOG.info("Entering the Current Date for the Cessation to Happen.");
		enterTheDatePage.enterCurrentDate();
		enterTheDatePage.goToMembershipCeasedPage();
		
		membershipCeasedPage.goToMembersListPage();
	}

	@Then("^the member organistion has been Ceased successfully$")
	public void the_member_organistion_has_been_Ceased_successfully() throws Throwable {
		LOG.info("Verify the Member Organisation has been Ceased Successfully.");
		
		memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		Assert.assertTrue("Failed: Links are still present.", memberListPage.checkMembershipActionButtons());
		Assert.assertEquals("Failed: Dates do not match.", DataStore.getSavedValue(UsableValues.MEMBERSHIP_CEASE_DATE), memberListPage.getMembershipCeasedDate());
	}
	
	@When("^the user Uploads a members list to the coordinated partnership with the following file \"([^\"]*)\"$")
	public void the_user_Uploads_a_members_list_to_the_coordinated_partnership_with_the_following_file(String file) throws Throwable {
		LOG.info("Uploading a Members List CSV File to a Co-ordinated Partnership.");
		
		partnershipAdvancedSearchPage.selectOrganisationLink();
		partnershipInformationPage.selectShowMembersListLink();
		
		memberListPage.selectUploadMembersListLink();
		
		LOG.info("Uploading the Members List CSV File..");
		uploadListOfMembersPage.chooseCSVFile();
		uploadListOfMembersPage.selectUpload();
		
		confirmMemberUploadPage.selectUpload();
		
		memberListUploadedPage.selectDone();
	}

	@Then("^the members list is uploaded successfully$")
	public void the_members_list_is_uploaded_successfully() throws Throwable {
		LOG.info("Verify the Members List was Uploaded Successfully.");
		
		memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		Assert.assertTrue("FAILED: Business names are not displayed in the table.", memberListPage.checkMembersListUploaded());
	}
	
	@When("^the user changes the members list type to \"([^\"]*)\"$")
	public void the_user_changes_the_members_list_type_to(String listType) throws Throwable {
		LOG.info("Uploading a Members List CSV File to a Co-ordinated Partnership.");
		
		partnershipAdvancedSearchPage.selectOrganisationLink();
		partnershipInformationPage.selectChangeMembersListTypeLink();
		
		membersListTypePage.selectMemberListType(listType);
		membersListTypePage.clickContinue();
		
		memberListCountPage.clickContinue();
		
		membersListUpToDatePage.selectYesRadial();
		membersListUpToDatePage.clicksave();
	}

	@Then("^the members list type is changed successfully$")
	public void the_members_list_type_is_changed_successfully() throws Throwable {
		LOG.info("Verifying the Members List Type has been Changed Successfully.");
		
		String requestText = "Please request a copy of the Primary Authority Membership List from the co-ordinator. ";
		
		String copyAvailableText = "The co-ordinator must make the copy available as soon as reasonably practicable and, in any event, "
				+ "not later than the third working day after the date of receiving the request at no charge.";
		
		Assert.assertTrue("FAILED: Memebers List Type was not Changed.", partnershipInformationPage.checkMembersListType(requestText + copyAvailableText));
	}
	
	@Given("^the user clicks the PAR Home page link$")
	public void the_user_clicks_the_PAR_Home_page_link() throws Throwable {
		LOG.info("Click PAR header to navigate to the PAR Home Page");
		parAuthorityPage.selectPageHeader();
	}

	@When("^the user is on the search for a partnership page$")
	public void the_user_is_on_the_search_for_a_partnership_page() throws Throwable {
		LOG.info("Click Search Public List of Partnerships to navigate to PAR Search for Partnership Page");
		homePage.selectPartnershipSearchLink();
	}

	@When("^the user can search for a PA Organisation Trading name Company number$")
	public void the_user_can_search_for_a_PA_Organisation_Trading_name_Company_number() throws Throwable {
		LOG.info("Enter business name and click the search button");
		publicRegistrySearchPage.searchForPartnership(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		publicRegistrySearchPage.clickSearchButton();
	}

	@Then("^the user is shown the information for that partnership$")
	public void the_user_is_shown_the_information_for_that_partnership() throws Throwable {
		LOG.info("Verify the Partnership contains the business name");
		assertTrue("Failed: Organisation was not found.", publicRegistrySearchPage.partnershipContains(DataStore.getSavedValue(UsableValues.BUSINESS_NAME)));
	}
	
	@When("^the user creates a new authority with the following details:$")
	public void the_user_creates_a_new_authority_with_the_following_details(DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.AUTHORITY_NAME, RandomStringGenerator.getAuthorityName(3));
			DataStore.saveValue(UsableValues.AUTHORITY_TYPE, data.get("Authority Type"));
			
			DataStore.saveValue(UsableValues.ONS_CODE, data.get("ONS Code"));
			DataStore.saveValue(UsableValues.AUTHORITY_REGFUNCTION, data.get("Regulatory Function"));
			
			DataStore.saveValue(UsableValues.AUTHORITY_ADDRESSLINE1, data.get("AddressLine1"));
			DataStore.saveValue(UsableValues.AUTHORITY_ADDRESSLINE2, data.get("AddressLine2"));
			DataStore.saveValue(UsableValues.AUTHORITY_TOWN, data.get("Town"));
			DataStore.saveValue(UsableValues.AUTHORITY_COUNTY, data.get("County"));
			DataStore.saveValue(UsableValues.AUTHORITY_COUNTRY, data.get("Country"));
			DataStore.saveValue(UsableValues.AUTHORITY_NATION, data.get("Nation"));
			DataStore.saveValue(UsableValues.AUTHORITY_POSTCODE, data.get("Postcode"));
		}
		
		LOG.info("Select manage authorities.");
		helpDeskDashboardPage.selectManageAuthorities();
		
		LOG.info("Select add authority.");
		authoritiesSearchPage.selectAddAuthority();
		
		LOG.info("Provide authority name.");
		authorityNamePage.enterAuthorityName(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		authorityNamePage.clickContinue();
		
		LOG.info("Provide authority type.");
		authorityTypePage.selectAuthorityType(DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE));
		authorityTypePage.clickContinue();
		
		LOG.info("Enter authority contact details.");
		
		addAddressPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.AUTHORITY_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.AUTHORITY_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.AUTHORITY_TOWN), DataStore.getSavedValue(UsableValues.AUTHORITY_COUNTY), DataStore.getSavedValue(UsableValues.AUTHORITY_COUNTRY), 
				DataStore.getSavedValue(UsableValues.AUTHORITY_NATION), DataStore.getSavedValue(UsableValues.AUTHORITY_POSTCODE));
		addAddressPage.goToONSCodePage();
		
		LOG.info("Provide ONS code.");
		onsCodePage.enterONSCode(DataStore.getSavedValue(UsableValues.ONS_CODE));
		onsCodePage.clickContinue();
		
		LOG.info("Select regulatory function.");
		regulatoryFunctionPage.selectRegFunction(DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION));
	}

	@Then("^the authority is created sucessfully$")
	public void the_authority_is_created_sucessfully() throws Throwable {
		LOG.info("On the Authorities Dashboard.");
		Assert.assertTrue("Details don't check out", authorityConfirmationPage.checkAuthorityDetails());
		authorityConfirmationPage.saveChanges();
	}

	@When("^the user searches for the last created authority$")
	public void the_user_searches_for_the_last_created_authority() throws Throwable {
		LOG.info("Search for last created authority");
		authoritiesSearchPage.searchAuthority(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		authoritiesSearchPage.selectManageAuthority();
	}

	@When("^the user updates all the fields for newly created authority$")
	public void the_user_updates_all_the_fields_for_newly_created_authority() throws Throwable {
		LOG.info("Updating all editble fields against selected authority");
		DataStore.saveValue(UsableValues.AUTHORITY_NAME, DataStore.getSavedValue(UsableValues.AUTHORITY_NAME) + " Updated");
		DataStore.saveValue(UsableValues.AUTHORITY_TYPE, "District");
		DataStore.saveValue(UsableValues.ONS_CODE, DataStore.getSavedValue(UsableValues.ONS_CODE) + " Updated");
		DataStore.saveValue(UsableValues.AUTHORITY_REGFUNCTION, "Alphabet learning");
		
		authorityConfirmationPage.editAuthorityName();
		
		authorityNamePage.enterAuthorityName(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		authorityNamePage.clickSave();
		
		authorityConfirmationPage.editAuthorityType();
		
		authorityTypePage.selectAuthorityType(DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE));
		authorityTypePage.clickSave();
		
		authorityConfirmationPage.editONSCode();
		
		onsCodePage.enterONSCode(DataStore.getSavedValue(UsableValues.ONS_CODE));
		onsCodePage.clickSave();
		
		authorityConfirmationPage.editRegFunction();
		
		regulatoryFunctionPage.editRegFunction(DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION));
	}

	@Then("^the update for the authority is successful$")
	public void the_update_for_the_authority_is_successful() throws Throwable {
		LOG.info("Check all updated changes check out");
		
		Assert.assertTrue("Failed: Authority was not Updated.", authorityConfirmationPage.checkAuthorityDetails());
		authorityConfirmationPage.saveChanges();
	}
	
	@When("^the user searches for an Authority with the same Regulatory Functions \"([^\"]*)\"$")
	public void the_user_searches_for_an_Authority_with_the_same_Regulatory_Functions(String authority) throws Throwable {
		LOG.info("Search for the Authority.");
		helpDeskDashboardPage.selectManageAuthorities();
		authoritiesSearchPage.searchAuthority(authority);
		DataStore.saveValue(UsableValues.PREVIOUS_AUTHORITY_NAME, authority);
		
		authoritiesSearchPage.selectTransferPartnerships();
	}

	@When("^the user completes the partnership transfer process$")
	public void the_user_completes_the_partnership_transfer_process() throws Throwable {
		LOG.info("Transferring a Partnership to the new Authority.");
		
		authorityTransferSelectionPage.searchAuthority(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		partnershipMigrationSelectionPage.selectPartnership(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		
		LOG.info("Confirm the Partnership Transfer.");
		enterTheDatePage.goToConfirmThisTranferPage();
		confirmThisTranferPage.confirmPartnershipTransfer();
		transferCompletedPage.selectDoneButton();
	}

	@Then("^the partnership is transferred to the new authority successfully$")
	public void the_partnership_is_transferred_to_the_new_authority_successfully() throws Throwable {
		LOG.info("Search for the Partnership with the New Authority.");
		
		authoritiesSearchPage.goToHelpDeskDashboard();
		
		helpDeskDashboardPage.selectSearchPartnerships();
		partnershipAdvancedSearchPage.searchPartnershipsPrimaryAuthority();
		partnershipAdvancedSearchPage.selectPrimaryAuthorityLink();
		
		LOG.info("Verify the Partnership Displays the Previously Known as Text.");
		Assert.assertTrue("FAILED: Previously Known as text is not Displayed", partnershipInformationPage.checkPreviouslyKnownAsText());
	}
	
	@When("^the user searches for the last created organisation$")
	public void the_user_searches_for_the_last_created_organisation() throws Throwable {
		LOG.info("Search and select last created organisation");
		helpDeskDashboardPage.selectManageOrganisations();
		organisationsSearchPage.searchOrganisation();
		organisationsSearchPage.selectOrganisation();
	}
	
	@When("^the user updates all the fields for last created organisation$")
	public void the_user_updates_all_the_fields_for_last_created_organisation() throws Throwable {
		LOG.info("Update all the organisation fields.");
		
		DataStore.saveValue(UsableValues.BUSINESS_NAME, DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + " Updated");
		DataStore.saveValue(UsableValues.BUSINESS_DESC, DataStore.getSavedValue(UsableValues.BUSINESS_DESC) + " Updated");
		DataStore.saveValue(UsableValues.TRADING_NAME, DataStore.getSavedValue(UsableValues.TRADING_NAME) + " Updated");
		DataStore.saveValue(UsableValues.SIC_CODE, "allow people to eat");
		
		businessDetailsPage.editOrganisationName();
		businessNamePage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		businessNamePage.goToBusinessConfirmationPage();
		
		businessDetailsPage.editOrganisationDesc();
		aboutTheOrganisationPage.enterDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		aboutTheOrganisationPage.goToBusinessDetailsPage();
		
		businessDetailsPage.editTradingName();
		tradingPage.enterTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
		tradingPage.goToBusinessDetailsPage();
		
		businessDetailsPage.editSICCode();
		sicCodePage.selectPrimarySICCode(DataStore.getSavedValue(UsableValues.SIC_CODE)); 
		sicCodePage.goToBusinessDetailsPage();
	}
	
	@Then("^all the fields are updated correctly$")
	public void all_the_fields_are_updated_correctly() throws Throwable {
		LOG.info("Check all updated changes check out");
		
		Assert.assertTrue("Failed: Organisation Details where not Updated.", businessDetailsPage.checkAuthorityDetails());
		businessDetailsPage.saveChanges();
	}
	
	@When("^the user selects a contact to update$")
	public void the_user_selects_a_contact_to_update() throws Throwable {
		DataStore.saveValue(UsableValues.BUSINESS_EMAIL, DataStore.getSavedValue(UsableValues.LOGIN_USER));
		helpDeskDashboardPage.selectManageProfileDetails();
		
		LOG.info("Selecting a Contact to Update.");
		contactRecordsPage.selectContactToUpdate();
		contactRecordsPage.selectContinueButton();
		
		LOG.info("Click Continue to Accept the Contact Details.");
		contactDetailsPage.goToContactCommunicationPreferencesPage();
		contactCommunicationPreferencesPage.selectContinueButton();
	}

	@Then("^the user can successfully subscribe to PAR News$")
	public void the_user_can_successfully_subscribe_to_PAR_News() throws Throwable {
		LOG.info("Click the Checkbox to Subscribe to the PAR News Letter.");
		contactUpdateSubscriptionPage.subscribeToPARNews();
		contactUpdateSubscriptionPage.selectContinueButton();
		
		LOG.info("Successfully subscribed from PAR news letter.");
		profileReviewPage.goToProfileCompletionPage();
		profileCompletionPage.goToDashboardPage();
	}

	@Then("^the user can successfully unsubscribe from PAR News$")
	public void the_user_can_successfully_unsubscribe_from_PAR_News() throws Throwable {
		LOG.info("Click the Checkbox to Unsubscribe from the PAR News Letter.");
		contactUpdateSubscriptionPage.unsubscribeFromPARNews();
		contactUpdateSubscriptionPage.selectContinueButton();
		
		LOG.info("Successfully unsubscribed from PAR news letter.");
		profileReviewPage.goToProfileCompletionPage();
		profileCompletionPage.goToDashboardPage();
	}
	
	@When("^the user is on the Subscriptions page$")
	public void the_user_is_on_the_Subscriptions_page() throws Throwable {
		LOG.info("Navigate to the Manage Subscriptions Page.");
		helpDeskDashboardPage.selectManageSubscriptions();
	}
	
	@When("^the user searches for the par_authority email$")
	public void the_user_searches_for_the_par_authority_email() throws Throwable {
		newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		newsLetterSubscriptionPage.ClickSearchButton();
		
		LOG.info("Searching for the Authority Email: " + DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
	}
	
	@Then("^the user can verify the email is successfully in the Subscriptions List$")
	public void the_user_can_verify_the_email_is_successfully_in_the_Subscriptions_List() throws Throwable {
		LOG.info("Assert the Email is successfully added to the Subscription List.");
		assertTrue("Failed: Email address was not added to the PAR News Subscription List.", newsLetterSubscriptionPage.verifyTableElementExists());
	}

	@Then("^the user can verify the email is successfully removed from the Subscriptions List$")
	public void the_user_can_verify_the_email_is_successfully_removed_from_the_Subscriptions_List() throws Throwable {
		LOG.info("Assert the Email is removed successfully from the Subscription List.");
		assertTrue("Failed: Email address was not removed from the PAR News Subscription List.", newsLetterSubscriptionPage.verifyTableElementIsNull());
	}

	@When("^the user is on the Manage a subscription list page$")
	public void the_user_is_on_the_Manage_a_subscription_list_page() throws Throwable {
		LOG.info("Navigate to Manage Subscriptions Page.");
		helpDeskDashboardPage.selectManageSubscriptions();
		newsLetterSubscriptionPage.selectManageSubsciptions();
		
		LOG.info("Email with the largest number: " + DataStore.getSavedValue(UsableValues.LAST_PAR_NEWS_EMAIL));
	}

	@When("^the user enters a new email to add to the list \"([^\"]*)\"$")
	public void the_user_enters_a_new_email_to_add_to_the_list(String email) throws Throwable {
		LOG.info("Adding a new email to the subscription list.");
		DataStore.saveValue(UsableValues.PAR_NEWS_EMAIL, email);
		
		newsLetterManageSubscriptionListPage.selectInsertNewEmailRadioButton();
		newsLetterManageSubscriptionListPage.AddNewEmail(email);
		newsLetterManageSubscriptionListPage.clickContinueButton();
		
		newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify the new email was added successfully$")
	public void the_user_can_verify_the_new_email_was_added_successfully() throws Throwable {
		LOG.info("Verify the new email was added to the Subscription list.");
		newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.PAR_NEWS_EMAIL));
		newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue("Failed: Email was not added to the Subscriptions List.", newsLetterSubscriptionPage.verifyTableElementExists());
	}

	@When("^the user enters an email to be removed from the list \"([^\"]*)\"$")
	public void the_user_enters_an_email_to_be_removed_from_the_list(String email) throws Throwable {
		LOG.info("Removing an email from the subscription list.");
		DataStore.saveValue(UsableValues.PAR_NEWS_EMAIL, email);
		
		newsLetterManageSubscriptionListPage.selectRemoveEmailRadioButton();
		newsLetterManageSubscriptionListPage.RemoveEmail(email);
		newsLetterManageSubscriptionListPage.clickContinueButton();
		
		newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify the email was removed successfully$")
	public void the_user_can_verify_the_email_was_removed_successfully() throws Throwable {
		LOG.info("Verify the email was removed from the Subscription list.");
		newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.PAR_NEWS_EMAIL));
		newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue("Failed: Email was not Removed from the Subscriptions List.", newsLetterSubscriptionPage.verifyTableElementIsNull());
	}

	@When("^the user enters a list of new emails to replace the subscription list$")
	public void the_user_enters_a_list_of_new_emails_to_replace_the_subscription_list() throws Throwable {
		LOG.info("Adding a new list of emails to replace the original Subscription List.");
		newsLetterManageSubscriptionListPage.selectReplaceSubscriptionListRadioButton();
		newsLetterManageSubscriptionListPage.clickContinueButton();
		
		newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify an email from the original list was removed successfully$")
	public void the_user_can_verify_an_email_from_the_original_list_was_removed_successfully() throws Throwable {
		LOG.info("Verify the Subscription list was replaced with the new list.");
		newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.LAST_PAR_NEWS_EMAIL));
		newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue("Failed: The new list did not replace the original list.", newsLetterSubscriptionPage.verifyTableElementIsNull());
	}
	
	@When("^the user searches for the \"([^\"]*)\" user account$")
	public void the_user_searches_for_the_user_account(String userEmail) throws Throwable {
		LOG.info("Searching for the user.");
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
		case ("senior_administrator@example.com"):
		case ("secretary_state@example.com"):
			LOG.info("Selecting Manage people.");
			helpDeskDashboardPage.selectManagePeople();
			break;
		case ("par_authority_manager@example.com"):
		case ("par_authority@example.com"):
		case ("par_business_manager@example.com"):
		case ("par_business@example.com"):
			LOG.info("Selecting Manage Colleagues.");
			dashboardPage.selectManageColleagues();
			break;
		}
		
		DataStore.saveValue(UsableValues.PERSON_EMAIL_ADDRESS, userEmail);
		
		managePeoplePage.enterNameOrEmail(userEmail);
		managePeoplePage.clickSubmit();
		
		
	}

	@When("^the user clicks the manage contact link$")
	public void the_user_clicks_the_manage_contact_link() throws Throwable {
		LOG.info("Clicking the Manage Contact link.");
		DataStore.saveValue(UsableValues.PERSON_FULLNAME_TITLE, managePeoplePage.GetPersonName());
		managePeoplePage.clickManageContact();
	}

	@Then("^the user can view the user account successfully$")
	public void the_user_can_view_the_user_account_successfully() throws Throwable {
		LOG.info("Verify the correct user profile is displayed.");
	    assertEquals(DataStore.getSavedValue(UsableValues.PERSON_EMAIL_ADDRESS), userProfilePage.getUserAccountEmail());
	}

	@When("^the user changes the users role to \"([^\"]*)\"$")
	public void the_user_changes_the_users_role_to(String roleType) throws Throwable {
		LOG.info("Selecting the Manage Roles Link.");
		userProfilePage.clickManageRolesLink();
		
		LOG.info("Deselecting all roles.");
		userTypePage.deselectAllMemberships();
		
		LOG.info("Selecting the new role.");
		DataStore.saveValue(UsableValues.ACCOUNT_TYPE, roleType);
		userTypePage.chooseMembershipRole(roleType);
		userTypePage.goToUserProfilePage();
	}

	@Then("^the user role was changed successfully$")
	public void the_user_role_was_changed_successfully() throws Throwable {
		LOG.info("Verify the User Account Type was changed successfully.");
		
	    assertTrue(userProfilePage.checkUserAccountType());
	}
	
	@When("^the user adds a new Authority membership$")
	public void the_user_adds_a_new_Authority_membership() throws Throwable {
		userProfilePage.clickAddMembershipLink();
		
		LOG.info("Choosing the person to add the new membership to.");
		choosePersonToAddPage.choosePerson();
		choosePersonToAddPage.clickContinueButton();
		
		LOG.info("Choosing the new Authority Membership.");
		userMembershipPage.chooseAuthorityMembership(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		userMembershipPage.clickContinueButton();
		
		addMembershipConfirmationPage.clickContinueButton();
	}

	@Then("^the Authority membership was added successfully$")
	public void the_Authority_membership_was_added_successfully() throws Throwable {
		LOG.info("Verify the new Authority membership was added successfully.");
		
		assertTrue(userProfilePage.checkUserMembershipDisplayed());
	}

	@When("^the user removes the last added Authority membership$")
	public void the_user_removes_the_last_added_Authority_membership() throws Throwable {
		LOG.info("Remove the last added Authority Membership.");
		userProfilePage.clickRemoveMembershipLink();
		removePage.goToUserProfilePage();
	}

	@Then("^the Authority membership was removed successfully$")
	public void the_Authority_membership_was_removed_successfully() throws Throwable {
		LOG.info("Verify the Authority membership was removed successfully.");
		
		assertTrue(userProfilePage.checkMembershipRemoved());
	}
	
	@When("^the user blocks the user account$")
	public void the_user_blocks_the_user_account() throws Throwable {
		LOG.info("Block the User Account.");
		userProfilePage.clickBlockUserAccountLink();
		blockPage.goToUserProfilePage();
	}

	@Then("^the user verifies the account was blocked successfully$")
	public void the_user_verifies_the_account_was_blocked_successfully() throws Throwable {
		LOG.info("Verifying the User account was blocked.");
		
		assertTrue(userProfilePage.checkUserAccountIsNotActive());
		assertTrue(userProfilePage.checkReactivateUserAccountLinkIsDisplayed());
	}

	@Then("^the user cannot sign in and receives an error message$")
	public void the_user_cannot_sign_in_and_receives_an_error_message() throws Throwable {
		LOG.info("Verifying the User cannot sign in and receives an error message.");
		
		assertTrue(loginPage.checkErrorSummary("The username national_regulator@example.com has not been activated or is blocked."));
		assertTrue(loginPage.checkErrorMessage("The username national_regulator@example.com has not been activated or is blocked."));
	}

	@When("^the user reinstates the user account$")
	public void the_user_reinstates_the_user_account() throws Throwable {
		LOG.info("Re-activate the User Account.");
		userProfilePage.clickReactivateUserAccountLink();
		reinstatePage.goToUserProfilePage();
	}

	@Then("^the user verifies the account is reinstated successfully$")
	public void the_user_verifies_the_account_is_reinstated_successfully() throws Throwable {
		LOG.info("Verifying the User account has been re-activated.");
		
		assertTrue(userProfilePage.checkLastSignInHeaderIsDisplayed());
		assertTrue(userProfilePage.checkBlockUserAccountLinkIsDisplayed());
	}
	
	@When("^the user creates a new person:$")
	public void the_user_creates_a_new_person(DataTable details) throws Throwable {
		helpDeskDashboardPage.selectManagePeople();
		managePeoplePage.selectAddPerson();
		
		String firstName = RandomStringUtils.randomAlphabetic(8);
		String lastName = RandomStringUtils.randomAlphabetic(8);
		String emailAddress = firstName + "@" + lastName + ".com";

		DataStore.saveValue(UsableValues.PERSON_FIRSTNAME, firstName); 
		DataStore.saveValue(UsableValues.PERSON_LASTNAME, lastName);
		DataStore.saveValue(UsableValues.PERSON_EMAIL_ADDRESS, emailAddress);
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.PERSON_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.PERSON_WORK_NUMBER, data.get("WorkNumber"));
			DataStore.saveValue(UsableValues.PERSON_MOBILE_NUMBER, data.get("MobileNumber"));
		}
		
		LOG.info("Adding a new person.");
		contactDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		contactDetailsPage.enterFirstName(DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME));
		contactDetailsPage.enterLastName(DataStore.getSavedValue(UsableValues.PERSON_LASTNAME));
		contactDetailsPage.enterWorkNumber(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		contactDetailsPage.enterMobileNumber(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		contactDetailsPage.enterEmail(DataStore.getSavedValue(UsableValues.PERSON_EMAIL_ADDRESS));
		
		contactDetailsPage.clickContinueButton();
	}

	@Then("^the user can verify the person was created successfully and can send an account invitation$")
	public void the_user_can_verify_the_person_was_created_successfully_and_can_send_an_account_invitation() throws Throwable {
		
		assertTrue("Failed: Header does not contain the person's fullname and title.", userProfilePage.checkHeaderForName());
		assertTrue("Failed: Cannot find the User account invitation link.", userProfilePage.checkForUserAccountInvitationLink());
		assertTrue("Failed: Contact name field does not contain the person's fullname and title.", userProfilePage.checkContactName());
		assertTrue("Failed: Contact email field does not contain the correct email address.", userProfilePage.checkContactEmail());
		assertTrue("Failed: Contact numbers field does not contain the work and/or mobile phone numbers", userProfilePage.checkContactPhoneNumbers());
		assertTrue("Failed: Contact Locations are displayed.", userProfilePage.checkContactLocationsIsEmpty());
		
		userProfilePage.clickDoneButton();
	}

	@When("^the user searches for an existing person successfully$")
	public void the_user_searches_for_an_existing_person_successfully() throws Throwable {

		String personsName = DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.PERSON_LASTNAME);

		managePeoplePage.enterNameOrEmail(personsName);
		managePeoplePage.clickSubmit();

		managePeoplePage.clickManageContact();

		LOG.info("Found an existing user with the name: " + personsName);
	}

	@When("^the user updates an existing person:$")
	public void the_user_updates_an_existing_person_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Updating an existing person.");
		userProfilePage.clickUpdateUserButton();
		
		String firstName = RandomStringUtils.randomAlphabetic(8);
		String lastName = RandomStringUtils.randomAlphabetic(8);
		String emailAddress = firstName + "@" + lastName + ".com";

		DataStore.saveValue(UsableValues.PERSON_FIRSTNAME, firstName); 
		DataStore.saveValue(UsableValues.PERSON_LASTNAME, lastName);
		DataStore.saveValue(UsableValues.PERSON_EMAIL_ADDRESS, emailAddress);
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.PERSON_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.PERSON_WORK_NUMBER, data.get("WorkNumber"));
			DataStore.saveValue(UsableValues.PERSON_MOBILE_NUMBER, data.get("MobileNumber"));
		}
		
		LOG.info("Updating Contact Details.");
		contactDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		contactDetailsPage.enterFirstName(DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME));
		contactDetailsPage.enterLastName(DataStore.getSavedValue(UsableValues.PERSON_LASTNAME));
		contactDetailsPage.enterWorkNumber(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		contactDetailsPage.enterMobileNumber(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		contactDetailsPage.enterEmail(DataStore.getSavedValue(UsableValues.PERSON_EMAIL_ADDRESS));
		
		contactDetailsPage.clickContinueButton();
	}

	@Then("^the user can verify the person was updated successfully and can send an account invitation$")
	public void the_user_can_verify_the_person_was_updated_successfully_and_can_send_an_account_invitation() throws Throwable {
		assertTrue("Failed: Header does not contain the person's fullname and title.", userProfilePage.checkHeaderForName());
		assertTrue("Failed: Cannot find the User account invitation link.", userProfilePage.checkForUserAccountInvitationLink());
		assertTrue("Failed: Contact name field does not contain the person's fullname and title.", userProfilePage.checkContactName());
		assertTrue("Failed: Contact email field does not contain the correct email address.", userProfilePage.checkContactEmail());
		assertTrue("Failed: Contact numbers field does not contain the work and/or mobile phone numbers", userProfilePage.checkContactPhoneNumbers());
		assertTrue("Failed: Contact Locations are displayed.", userProfilePage.checkContactLocationsIsEmpty());
	}
	
	@When("^the user updates their user account email address to \"([^\"]*)\"$")
	public void the_user_updates_their_user_account_email_address_to(String newEmail) throws Throwable {
		LOG.info("Update the User Account Email Address.");
		dashboardPage.selectManageProfileDetails();
		
		contactRecordsPage.selectContactToUpdate();
		contactRecordsPage.selectContinueButton();
		
		contactDetailsPage.enterEmailAddress(newEmail);
		contactDetailsPage.goToContactCommunicationPreferencesPage();
		
		contactCommunicationPreferencesPage.selectContinueButton();
		contactUpdateSubscriptionPage.selectContinueButton();
		
		profileReviewPage.confirmUserAccountEmail();
		profileReviewPage.goToProfileCompletionPage();
		
		profileCompletionPage.goToDashboardPage();
	}

	@Then("^the user can verify the new email address is displayed on the header$")
	public void the_user_can_verify_the_new_email_address_is_displayed_on_the_header() throws Throwable {
		LOG.info("Verifying the User Account Email was Updated Successfully.");
		
		Assert.assertTrue("Failed: The new User Account email address is not Displayed.", dashboardPage.checkUserAccountEmailAddress());
	}
	
	@When("^the user navigates to the statistics page$")
	public void the_user_navigates_to_the_statistics_page() throws Throwable {
		LOG.info("Navigating to the Statistics Page.");
		helpDeskDashboardPage.selectViewAllStatistics();
	}

	@Then("^the statistics page is dispalyed successfully$")
	public void the_statistics_page_is_dispalyed_successfully() throws Throwable {
		LOG.info("Verifying the Statistics Page is Displayed.");
		Assert.assertTrue("FAILED: Statistics Page is not Displayed!", parReportingPage.checkPageHeaderIsDisplayed());
	}
	
	@When("^the user selects the Read more about Primary Authority link$")
	public void the_user_selects_the_Read_more_about_Primary_Authority_link() throws Throwable {
		LOG.info("Selecting the Read More About Primary Authority Link.");
		
		homePage.selectReadMoreAboutPrimaryAuthorityLink();
	}

	@Then("^the user is taken to the GOV\\.UK Guidance page for Local regulation Primary Authority Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Guidance_page_for_Local_regulation_Primary_Authority_Successfully() throws Throwable {
		LOG.info("Verifying the Local regulation: Primary Authority Page is Displayed.");
		
		Assert.assertTrue("Failed: Local Regulation Primary Authority Header is not Displayed.", localRegulationPrimaryAuthorityPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Access tools and templates for local authorities link$")
	public void the_user_selects_the_Access_tools_and_templates_for_local_authorities_link() throws Throwable {
		LOG.info("Selecting the Access Tools and Templates Link.");
		
		homePage.selectAccessToolsAndTemplatesLink();
	}

	@Then("^the user is taken to the GOV\\.UK Collection page for Primary Authority Documents Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Collection_page_for_Primary_Authority_Documents_Successfully() throws Throwable {
		LOG.info("Verifying the Primary Authority documents page is Displayed.");
		
		Assert.assertTrue("Failed: Primary Authority Documents Header is not Displayed.", primaryAuthorityDocumentsPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Terms and Conditions link$")
	public void the_user_selects_the_Terms_and_Conditions_link() throws Throwable {
		LOG.info("Selecting the Terms and Conditions Link.");
		
		homePage.selectTermsAndConditionsLink();
	}

	@Then("^the user is taken to the GOV\\.UK Guidance page for Primary Authority terms and conditions Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Guidance_page_for_Primary_Authority_terms_and_conditions_Successfully() throws Throwable {
		LOG.info("Verifying the Primary Authority terms and conditions page is Displayed.");
		
		Assert.assertTrue("Failed: Primary Authority Terms and Conditions Header is not Displayed.", termsAndConditionsPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Cookies link$")
	public void the_user_selects_the_Cookies_link() throws Throwable {
		LOG.info("Selecting the Cookies Link from the Footer.");
		
		homePage.selectCookiesFooterLink();
	}

	@Then("^the user is taken to the Cookies page and can accept the Analytics Cookies Successfully$")
	public void the_user_is_taken_to_the_Cookies_page_and_can_accept_the_Analytics_Cookies_Successfully() throws Throwable {
		LOG.info("Verifying the Cookies page is Displayed and the User Accepts the Analytics Cookies.");
		
		Assert.assertTrue("Failed: Cookies Header is not Displayed.", cookiesPage.checkPageHeaderDisplayed());
		
		cookiesPage.acceptCookies();
		cookiesPage.selectSaveButton();
	}

	@When("^the user selects the Privacy link$")
	public void the_user_selects_the_Privacy_link() throws Throwable {
		LOG.info("Selecting the Privacy Link.");
		
		homePage.selectPrivacyLink();
	}

	@Then("^the user is taken to the GOV\\.UK Corporate report OPSS Privacy notice page Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Corporate_report_OPSS_Privacy_notice_page_Successfully() throws Throwable {
		LOG.info("Verifying the OPSS: privacy notice page is Displayed.");
		
		Assert.assertTrue("Failed: The OPSS Privacy Notice Header is not Dispalyed.", opssPrivacyNoticePage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Accessibility link$")
	public void the_user_selects_the_Accessibility_link() throws Throwable {
		LOG.info("Selecting the Accessibility Link.");
		
		homePage.selectAccessibilityLink();
	}

	@Then("^the user is taken to the GOV\\.UK Guidance page for the Primary Authority Register accessibility statement Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Guidance_page_for_the_Primary_Authority_Register_accessibility_statement_Successfully() throws Throwable {
		LOG.info("Verifying the Primary Authority Register: accessibility statement page is Displayed.");
		
		Assert.assertTrue("Failed: Accessibility Statement Header is not Displayed.", accessibilityStatementPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Open Government Licence link$")
	public void the_user_selects_the_Open_Government_Licence_link() throws Throwable {
		LOG.info("Selecting the Open Government Licence Link.");
		
		homePage.selectOpenGovernmentLicenceLink();
	}

	@Then("^the user is taken to the Open Government Licence for public sector information page Successfully$")
	public void the_user_is_taken_to_the_Open_Government_Licence_for_public_sector_information_page_Successfully() throws Throwable {
		LOG.info("Verifying the Open Government Licence page is Displayed.");
		
		Assert.assertTrue("Failed: The Open Government Licence Header is not Displayed.", openGovernmentLicencePage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Crown copyright link$")
	public void the_user_selects_the_Crown_copyright_link() throws Throwable {
		LOG.info("Selecting the Crown copyright Link.");
		
		homePage.selectCrownCopyrightLink();
	}

	@Then("^the user is taken to the Crown copyright page Successfully$")
	public void the_user_is_taken_to_the_Crown_copyright_page_Successfully() throws Throwable {
		LOG.info("Verifying the Crown copyright page is Displayed.");
		
		Assert.assertTrue("Failed: The Crown Copright Header is not Displayed.", crownCopyrightPage.checkPageHeaderDisplayed());
	}
}
