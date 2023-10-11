package uk.gov.beis.stepdefs;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.io.IOException;
import java.util.Map;

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
import uk.gov.beis.pageobjects.AuthorityPageObjects.AuthorityAddressDetailsPage;
import uk.gov.beis.pageobjects.HomePageLinkPageObjects.*;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionContactDetailsPage;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanCoveragePage;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanDetailsPage;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanExpirationPage;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanReviewPage;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanSearchPage;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.RemoveReasonInspectionPlanPage;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.RevokeReasonInspectionPlanPage;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.UploadInspectionPlanPage;
import uk.gov.beis.pageobjects.LegalEntityPageObjects.LegalEntityReviewPage;
import uk.gov.beis.pageobjects.LegalEntityPageObjects.LegalEntityTypePage;
import uk.gov.beis.pageobjects.LegalEntityPageObjects.UpdateLegalEntityPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.AddOrganisationNamePage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.BusinessAddressDetailsPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.BusinessContactDetailsPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.BusinessDetailsPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.DeclarationPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.EmployeesPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MemberListPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MemberOrganisationAddedConfirmationPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MemberOrganisationSummaryPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MembershipCeasedPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.SICCodePage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.TradingPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.AuthorityPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.BusinessInvitePage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.BusinessPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipAdvancedSearchPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipApprovalPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipCompletionPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipDescriptionPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipRestoredPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipRevokedPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipSearchPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipTermsPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipTypePage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.RegulatoryFunctionPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.RestorePartnershipConfirmationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.RevokePartnershipConfirmationPage;
import uk.gov.beis.pageobjects.UserManagement.*;
import uk.gov.beis.utility.DataStore;
import uk.gov.beis.utility.RandomStringGenerator;

public class PARStepDefs {

	public static WebDriver driver;
	
	// PAR Home Page
	private HomePage parHomePage;
	private LocalRegulationPrimaryAuthorityPage localRegulationPrimaryAuthorityPage;
	private PrimaryAuthorityDocumentsPage primaryAuthorityDocumentsPage;
	private TermsAndConditionsPage termsAndConditionsPage;
	private CookiesPage cookiesPage;
	private OPSSPrivacyNoticePage opssPrivacyNoticePage;
	private AccessibilityStatementPage accessibilityStatementPage;
	private OpenGovernmentLicencePage openGovernmentLicencePage;
	private CrownCopyrightPage crownCopyrightPage;
	
	// User Management
	private MergeContactRecordsPage mergeContactRecordsPage;
	private MergeContactRecordsConfirmationPage mergeContactRecordsConfirmationPage;
	
	// Legal Entity
	private UpdateLegalEntityPage updateLegalEntityPage;
	
	// Partnership
	private MembershipCeasedPage membershipCeasedPage;
	
	// Next Section
	private RevokeReasonInspectionPlanPage revokeReasonInspectionPlanPage;
	private RequestEnquiryPage requestEnquiryPage;
	private DeviationSearchPage deviationSearchPage;
	private InspectionPlanReviewPage inspectionPlanReviewPage;
	private EnquiryReviewPage enquiryReviewPage;
	private AdviceNoticeDetailsPage adviceNoticeDetailsPage;
	private InspectionFeedbackConfirmationPage inspectionFeedbackConfirmationPage;
	private InspectionFeedbackDetailsPage inspectionFeedbackDetailsPage;
	private InspectionPlanSearchPage inspectionPlanSearchPage;
	private InspectionContactDetailsPage inspectionContactDetailsPage;
	private RemoveEnforcementPage removeEnforcementPage;
	private EnforcementCompletionPage enforcementCompletionPage;
	private EnforcementActionPage enforcementActionPage;
	private EnforcementDetailsPage enforcementDetailsPage;
	private EnforcementLegalEntityPage enforcementLegalEntityPage;
	private BusinessConfirmationPage businessConfirmationPage;
	private EnforcementContactDetailsPage enforcementContactDetailsPage;
	private OrganisationDashboardPage organisationDashboardPage;
	private EnforcementNotificationPage enforcementNotificationPage;
	private EnforcementSearchPage enforcementSearchPage;
	private ONSCodePage onsCodePage;
	private UploadAdviceNoticePage uploadAdviceNoticePage;
	private InspectionFeedbackSearchPage inspectionFeedbackSearchPage;
	private ProposedEnforcementPage proposedEnforcementPage;
	private EnforcementReviewPage enforcementReviewPage;
	private RegulatoryFunctionPage regulatoryFunctionPage;
	private AuthorityConfirmationPage authorityConfirmationPage;
	private AuthorityAddressDetailsPage authorityAddressDetailsPage;
	private AuthorityTypePage authorityTypePage;
	private AdviceNoticeSearchPage adviceNoticeSearchPage;
	private AuthorityNamePage authorityNamePage;
	private LegalEntityReviewPage legalEntityReviewPage;
	private ReplyInspectionFeedbackPage replyInspectionFeedbackPage;
	private PartnershipAdvancedSearchPage partnershipAdvancedSearchPage;
	private UserProfileConfirmationPage userProfileConfirmationPage;
	private MailLogPage mailLogPage;
	private RequestDeviationPage requestDeviationPage;
	private InspectionPlanExpirationPage inspectionPlanExpirationPage;
	private AuthorityDashboardPage authoritiesDashboardPage;
	private PartnershipApprovalPage partnershipApprovalPage;
	private UserProfileCompletionPage userProfileCompletionPage;
	private UserCommsPreferencesPage userCommsPreferencesPage;
	private PasswordPage passwordPage;
	private UserSubscriptionPage userSubscriptionPage;
	private MemberListPage memberListPage;
	private LoginPage parLoginPage;
	private UploadInspectionPlanPage uploadInspectionPlanPage;
	private SICCodePage sicCodePage;
	private DeviationCompletionPage deviationCompletionPage;
	private DashboardPage parDashboardPage;
	private AuthorityPage parAuthorityPage;
	private PartnershipSearchPage partnershipSearchPage;
	private PartnershipTypePage parPartnershipTypePage;
	private PartnershipTermsPage parPartnershipTermsPage;
	private PartnershipDescriptionPage parPartnershipDescriptionPage;
	private BusinessPage parBusinessPage;
	private RemoveReasonInspectionPlanPage removeReasonInspectionPlanPage;
	private RevokePartnershipConfirmationPage revokePartnershipConfirmationPage;
	private PartnershipRevokedPage partnershipRevokedPage;
	private UserTermsPage userTermsPage;
	private LegalEntityTypePage legalEntityTypePage;
	private EmployeesPage employeesPage;
	private BusinessDetailsPage parBusinessDetailsPage;
	private DeclarationPage parDeclarationPage;
	private BusinessContactDetailsPage parBusinessContactDetailsPage;
	private PartnershipConfirmationPage parPartnershipConfirmationPage;
	private BusinessInvitePage parBusinessInvitePage;
	private PartnershipCompletionPage parPartnershipCompletionPage;
	private BusinessAddressDetailsPage parBusinessAddressDetailsPage;
	private TradingPage tradingPage;
	private EnquiryCompletionPage enquiryCompletionPage;
	private EnquiryContactDetailsPage enquiryContactDetailsPage;
	private InspectionPlanDetailsPage inspectionPlanDetailsPage;
	private RestorePartnershipConfirmationPage restorePartnershipConfirmationPage;
	private PartnershipRestoredPage partnershipRestoredPage;
	private RemoveEnforcementConfirmationPage removeEnforcementConfirmationPage;
	private InspectionFeedbackCompletionPage inspectionFeedbackCompletionPage;
	private ViewEnquiryPage viewEnquiryPage;
	private EditRegisteredAddressPage editRegisteredAddressPage;
	private AdviceArchivePage adviceArchivePage;
	private AdviceRemovalPage adviceRemovalPage;
	private DeletePage deletePage;
	private CompletionPage completionPage;
	private AddOrganisationNamePage addOrganisationNamePage;
	private EnterTheDatePage enterTheDatePage;
	private InspectionPlanCoveragePage inspectionPlanCoveragePage;
	private MemberOrganisationSummaryPage memberOrganisationSummaryPage;
	private MemberOrganisationAddedConfirmationPage memberOrganisationAddedConfirmationPage;
	
	// PAR News Letter
	private UserProfilePage userProfilePage;
	private UpdateUserCommunicationPreferencesPage updateUserCommunicationPreferencesPage;
	private UpdateUserContactDetailsPage updateUserContactDetailsPage;
	private UpdateUserSubscriptionsPage updateUserSubscriptionsPage;
	private NewsLetterSubscriptionPage newsLetterSubscriptionPage;
	private NewsLetterManageSubscriptionListPage newsLetterManageSubscriptionListPage;
	private NewsLetterSubscriptionReviewPage newsLetterSubscriptionReviewPage;

	// Person Creation and Update
	private ManagePeoplePage managePeoplePage;
	private PersonContactDetailsPage personsContactDetailsPage;
	private PersonAccountPage personAccountPage;
	private PersonMembershipPage personMembershipPage;
	private PersonUserRoleTypePage personUserTypePage;
	private PersonCreateAccountPage personCreateAccountPage;
	private PersonsProfilePage personsProfilePage;
	private RemoveContactPage removeContactPage;
	private DeviationReviewPage deviationReviewPage;
	private DeviationApprovalPage deviationApprovalPage;
	private EnquiriesSearchPage enquiriesSearchPage;
	private ReplyDeviationRequestPage replyDeviationRequestPage;
	private ReplyEnquiryPage replyEnquiryPage;

	public PARStepDefs() throws ClassNotFoundException, IOException {
		driver = ScenarioContext.lastDriver;
		
		// PAR Home Page
		localRegulationPrimaryAuthorityPage = PageFactory.initElements(driver, LocalRegulationPrimaryAuthorityPage.class);
		primaryAuthorityDocumentsPage = PageFactory.initElements(driver, PrimaryAuthorityDocumentsPage.class);
		termsAndConditionsPage = PageFactory.initElements(driver, TermsAndConditionsPage.class);
		cookiesPage = PageFactory.initElements(driver, CookiesPage.class);
		opssPrivacyNoticePage = PageFactory.initElements(driver, OPSSPrivacyNoticePage.class);
		accessibilityStatementPage = PageFactory.initElements(driver, AccessibilityStatementPage.class);
		openGovernmentLicencePage = PageFactory.initElements(driver, OpenGovernmentLicencePage.class);
		crownCopyrightPage = PageFactory.initElements(driver, CrownCopyrightPage.class);
		
		// User Management
		mergeContactRecordsPage = PageFactory.initElements(driver, MergeContactRecordsPage.class);
		mergeContactRecordsConfirmationPage = PageFactory.initElements(driver, MergeContactRecordsConfirmationPage.class);
		
		// Legal Entity
		updateLegalEntityPage = PageFactory.initElements(driver, UpdateLegalEntityPage.class);
		
		// Partnership
		membershipCeasedPage  = PageFactory.initElements(driver, MembershipCeasedPage.class);
		
		// Next Section
		adviceNoticeDetailsPage = PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
		uploadAdviceNoticePage = PageFactory.initElements(driver, UploadAdviceNoticePage.class);
		adviceNoticeSearchPage = PageFactory.initElements(driver, AdviceNoticeSearchPage.class);
		legalEntityReviewPage = PageFactory.initElements(driver, LegalEntityReviewPage.class);
		removeReasonInspectionPlanPage = PageFactory.initElements(driver, RemoveReasonInspectionPlanPage.class);
		revokeReasonInspectionPlanPage = PageFactory.initElements(driver, RevokeReasonInspectionPlanPage.class);
		inspectionPlanReviewPage = PageFactory.initElements(driver, InspectionPlanReviewPage.class);
		replyEnquiryPage = PageFactory.initElements(driver, ReplyEnquiryPage.class);
		enquiriesSearchPage = PageFactory.initElements(driver, EnquiriesSearchPage.class);
		enquiryReviewPage = PageFactory.initElements(driver, EnquiryReviewPage.class);
		requestEnquiryPage = PageFactory.initElements(driver, RequestEnquiryPage.class);
		enquiryContactDetailsPage = PageFactory.initElements(driver, EnquiryContactDetailsPage.class);
		replyDeviationRequestPage = PageFactory.initElements(driver, ReplyDeviationRequestPage.class);
		deviationApprovalPage = PageFactory.initElements(driver, DeviationApprovalPage.class);
		deviationSearchPage = PageFactory.initElements(driver, DeviationSearchPage.class);
		deviationCompletionPage = PageFactory.initElements(driver, DeviationCompletionPage.class);
		deviationReviewPage = PageFactory.initElements(driver, DeviationReviewPage.class);
		requestDeviationPage = PageFactory.initElements(driver, RequestDeviationPage.class);
		replyInspectionFeedbackPage = PageFactory.initElements(driver, ReplyInspectionFeedbackPage.class);
		inspectionFeedbackSearchPage = PageFactory.initElements(driver, InspectionFeedbackSearchPage.class);
		inspectionFeedbackCompletionPage = PageFactory.initElements(driver, InspectionFeedbackCompletionPage.class);
		inspectionFeedbackConfirmationPage = PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
		inspectionFeedbackDetailsPage = PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
		inspectionContactDetailsPage = PageFactory.initElements(driver, InspectionContactDetailsPage.class);
		inspectionPlanExpirationPage = PageFactory.initElements(driver, InspectionPlanExpirationPage.class);
		inspectionPlanDetailsPage = PageFactory.initElements(driver, InspectionPlanDetailsPage.class);
		uploadInspectionPlanPage = PageFactory.initElements(driver, UploadInspectionPlanPage.class);
		inspectionPlanSearchPage = PageFactory.initElements(driver, InspectionPlanSearchPage.class);
		removeEnforcementConfirmationPage = PageFactory.initElements(driver, RemoveEnforcementConfirmationPage.class);
		removeEnforcementPage = PageFactory.initElements(driver, RemoveEnforcementPage.class);
		enforcementCompletionPage = PageFactory.initElements(driver, EnforcementCompletionPage.class);
		proposedEnforcementPage = PageFactory.initElements(driver, ProposedEnforcementPage.class);
		enforcementReviewPage = PageFactory.initElements(driver, EnforcementReviewPage.class);
		enforcementSearchPage = PageFactory.initElements(driver, EnforcementSearchPage.class);
		enforcementActionPage = PageFactory.initElements(driver, EnforcementActionPage.class);
		enforcementDetailsPage = PageFactory.initElements(driver, EnforcementDetailsPage.class);
		enforcementLegalEntityPage = PageFactory.initElements(driver, EnforcementLegalEntityPage.class);
		enforcementContactDetailsPage = PageFactory.initElements(driver, EnforcementContactDetailsPage.class);
		enforcementNotificationPage = PageFactory.initElements(driver, EnforcementNotificationPage.class);
		businessConfirmationPage = PageFactory.initElements(driver, BusinessConfirmationPage.class);
		organisationDashboardPage = PageFactory.initElements(driver, OrganisationDashboardPage.class);
		onsCodePage = PageFactory.initElements(driver, ONSCodePage.class);
		enquiryCompletionPage = PageFactory.initElements(driver, EnquiryCompletionPage.class);
		authorityConfirmationPage = PageFactory.initElements(driver, AuthorityConfirmationPage.class);
		authorityTypePage = PageFactory.initElements(driver, AuthorityTypePage.class);
		regulatoryFunctionPage = PageFactory.initElements(driver, RegulatoryFunctionPage.class);
		authorityAddressDetailsPage = PageFactory.initElements(driver, AuthorityAddressDetailsPage.class);
		authorityNamePage = PageFactory.initElements(driver, AuthorityNamePage.class);
		authoritiesDashboardPage = PageFactory.initElements(driver, AuthorityDashboardPage.class);
		partnershipRestoredPage = PageFactory.initElements(driver, PartnershipRestoredPage.class);
		revokePartnershipConfirmationPage = PageFactory.initElements(driver, RevokePartnershipConfirmationPage.class);
		partnershipRevokedPage = PageFactory.initElements(driver, PartnershipRevokedPage.class);
		partnershipApprovalPage = PageFactory.initElements(driver, PartnershipApprovalPage.class);
		regulatoryFunctionPage = PageFactory.initElements(driver, RegulatoryFunctionPage.class);
		restorePartnershipConfirmationPage = PageFactory.initElements(driver, RestorePartnershipConfirmationPage.class);
		partnershipAdvancedSearchPage = PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
		userCommsPreferencesPage = PageFactory.initElements(driver, UserCommsPreferencesPage.class);
		userProfileConfirmationPage = PageFactory.initElements(driver, UserProfileConfirmationPage.class);
		userSubscriptionPage = PageFactory.initElements(driver, UserSubscriptionPage.class);
		employeesPage = PageFactory.initElements(driver, EmployeesPage.class);
		userTermsPage = PageFactory.initElements(driver, UserTermsPage.class);
		userProfileCompletionPage = PageFactory.initElements(driver, UserProfileCompletionPage.class);
		passwordPage = PageFactory.initElements(driver, PasswordPage.class);
		mailLogPage = PageFactory.initElements(driver, MailLogPage.class);
		memberListPage = PageFactory.initElements(driver, MemberListPage.class);
		tradingPage = PageFactory.initElements(driver, TradingPage.class);
		sicCodePage = PageFactory.initElements(driver, SICCodePage.class);
		parHomePage = PageFactory.initElements(driver, HomePage.class);
		parBusinessDetailsPage = PageFactory.initElements(driver, BusinessDetailsPage.class);
		parDeclarationPage = PageFactory.initElements(driver, DeclarationPage.class);
		parLoginPage = PageFactory.initElements(driver, LoginPage.class);
		parDashboardPage = PageFactory.initElements(driver, DashboardPage.class);
		parAuthorityPage = PageFactory.initElements(driver, AuthorityPage.class);
		parPartnershipTypePage = PageFactory.initElements(driver, PartnershipTypePage.class);
		parPartnershipDescriptionPage = PageFactory.initElements(driver, PartnershipDescriptionPage.class);
		parBusinessPage = PageFactory.initElements(driver, BusinessPage.class);
		parBusinessContactDetailsPage = PageFactory.initElements(driver, BusinessContactDetailsPage.class);
		parPartnershipConfirmationPage = PageFactory.initElements(driver, PartnershipConfirmationPage.class);
		parBusinessInvitePage = PageFactory.initElements(driver, BusinessInvitePage.class);
		parPartnershipCompletionPage = PageFactory.initElements(driver, PartnershipCompletionPage.class);
		parBusinessAddressDetailsPage = PageFactory.initElements(driver, BusinessAddressDetailsPage.class);
		parPartnershipTermsPage = PageFactory.initElements(driver, PartnershipTermsPage.class);
		partnershipSearchPage = PageFactory.initElements(driver, PartnershipSearchPage.class);
		viewEnquiryPage = PageFactory.initElements(driver, ViewEnquiryPage.class);
		editRegisteredAddressPage = PageFactory.initElements(driver, EditRegisteredAddressPage.class);
		legalEntityTypePage = PageFactory.initElements(driver, LegalEntityTypePage.class);
		adviceArchivePage = PageFactory.initElements(driver, AdviceArchivePage.class);
		adviceRemovalPage = PageFactory.initElements(driver, AdviceRemovalPage.class);
		deletePage = PageFactory.initElements(driver, DeletePage.class);
		completionPage = PageFactory.initElements(driver, CompletionPage.class);
		addOrganisationNamePage = PageFactory.initElements(driver, AddOrganisationNamePage.class);
		enterTheDatePage = PageFactory.initElements(driver, EnterTheDatePage.class);
		inspectionPlanCoveragePage = PageFactory.initElements(driver, InspectionPlanCoveragePage.class);
		memberOrganisationSummaryPage = PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
		memberOrganisationAddedConfirmationPage = PageFactory.initElements(driver, MemberOrganisationAddedConfirmationPage.class);
		
		// PAR News Letter
		userProfilePage = PageFactory.initElements(driver, UserProfilePage.class);
		updateUserCommunicationPreferencesPage = PageFactory.initElements(driver,
				UpdateUserCommunicationPreferencesPage.class);
		updateUserContactDetailsPage = PageFactory.initElements(driver, UpdateUserContactDetailsPage.class);
		updateUserSubscriptionsPage = PageFactory.initElements(driver, UpdateUserSubscriptionsPage.class);
		newsLetterSubscriptionPage = PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
		newsLetterManageSubscriptionListPage = PageFactory.initElements(driver,
				NewsLetterManageSubscriptionListPage.class);
		newsLetterSubscriptionReviewPage = PageFactory.initElements(driver, NewsLetterSubscriptionReviewPage.class);

		// Person Creation and Update
		managePeoplePage = PageFactory.initElements(driver, ManagePeoplePage.class);
		personsContactDetailsPage = PageFactory.initElements(driver, PersonContactDetailsPage.class);
		personMembershipPage = PageFactory.initElements(driver, PersonMembershipPage.class);
		personAccountPage = PageFactory.initElements(driver, PersonAccountPage.class);
		personUserTypePage = PageFactory.initElements(driver, PersonUserRoleTypePage.class);
		personCreateAccountPage = PageFactory.initElements(driver, PersonCreateAccountPage.class);
		personsProfilePage = PageFactory.initElements(driver, PersonsProfilePage.class);
		removeContactPage = PageFactory.initElements(driver, RemoveContactPage.class);
	}

	@Given("^the user is on the PAR home page$")
	public void the_user_is_on_the_PAR_home_page() throws Throwable {
		LOG.info("Navigating to PAR Home page but first accepting cookies if present");
		parHomePage.navigateToUrl();
		parHomePage.checkAndAcceptCookies();
	}

	@Given("^the user is on the PAR login page$")
	public void the_user_is_on_the_PAR_login_page() throws Throwable {
		LOG.info("Navigating to PAR login page - logging out user first if already logged in");
		parLoginPage.navigateToUrl();
		parLoginPage.checkAndAcceptCookies();
	}

	@Given("^the user visits the login page$")
	public void the_user_wants_to_login() throws Throwable {
		parHomePage.selectLogin();
	}

	@Given("^the user logs in with the \"([^\"]*)\" user credentials$")
	public void the_user_logs_in_with_the_user_credentials(String user) throws Throwable {
		DataStore.saveValue(UsableValues.LOGIN_USER, user);
		String pass = PropertiesUtil.getConfigPropertyValue(user);
		LOG.info("Logging in user with credentials; username: " + user + " and password " + pass);
		parLoginPage.enterLoginDetails(user, pass);
		parLoginPage.selectLogin();
	}

	@Then("^the user is on the dashboard page$")
	public void the_user_is_on_the_dashboard_page() throws Throwable {
		LOG.info("Check user is on the PAR Dashboard Page");
		Assert.assertTrue("Text not found", parDashboardPage.checkPage().contains("Dashboard"));
	}

	@When("^the user creates a new \"([^\"]*)\" partnership application with the following details:$")
	public void the_user_creates_a_new_partnership_application_with_the_following_details(String type, DataTable details) throws Throwable {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.AUTHORITY_NAME, data.get("Authority"));
			DataStore.saveValue(UsableValues.PARTNERSHIP_TYPE, type);
			DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, data.get("Partnership Info"));
			DataStore.saveValue(UsableValues.BUSINESS_NAME, RandomStringGenerator.getBusinessName(4));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE1, data.get("addressline1"));
			DataStore.saveValue(UsableValues.BUSINESS_TOWN, data.get("town"));
			DataStore.saveValue(UsableValues.BUSINESS_POSTCODE, data.get("postcode"));
			DataStore.saveValue(UsableValues.BUSINESS_FIRSTNAME, data.get("firstname"));
			DataStore.saveValue(UsableValues.BUSINESS_LASTNAME, data.get("lastname"));
			DataStore.saveValue(UsableValues.BUSINESS_PHONE, data.get("phone"));
			DataStore.saveValue(UsableValues.BUSINESS_EMAIL, RandomStringGenerator.getEmail(4));
		}
		
		ScenarioContext.secondJourneyPart = false;
		
		LOG.info("Select apply new partnership");
		parDashboardPage.selectApplyForNewPartnership();
		
		LOG.info("Choose authority");
		parAuthorityPage.selectAuthority(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		
		LOG.info("Select partnership type");
		parPartnershipTypePage.selectPartnershipType(type);
		
		LOG.info("Accepting terms");
		parPartnershipTermsPage.acceptTerms();
		
		LOG.info("Entering partnership description");
		parPartnershipDescriptionPage.enterPartnershipDescription(DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO));
		
		LOG.info("Entering business/organisation name");
		parBusinessPage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		
		LOG.info("Enter address details");
		parBusinessAddressDetailsPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_TOWN),
				DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		
		LOG.info("Enter contact details");
		parBusinessContactDetailsPage.enterContactDetails(DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME), DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME),
				DataStore.getSavedValue(UsableValues.BUSINESS_PHONE), DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		
		LOG.info("Send invitation to user");
		parBusinessInvitePage.sendInvite();
	}

	@Then("^the first part of the partnership application is successfully completed$")
	public void the_first_part_of_the_partnership_application_is_successfully_completed() throws Throwable {
		LOG.info("Confirm/check partnership details");
		parPartnershipConfirmationPage.confirmDetailsAsAuthority();
		
		Assert.assertTrue("Partnership info missing", parPartnershipConfirmationPage.checkPartnershipInfo());
		Assert.assertTrue("Partnership appliction information not correct", parPartnershipConfirmationPage.checkPartnershipApplication());
		
		LOG.info("Saving changes");
		parPartnershipConfirmationPage.saveChanges();
		parPartnershipCompletionPage.completeApplication();
	}

	@When("^the user searches for the last created partnership$")
	public void the_user_searches_for_the_last_created_partnership() throws Throwable {
		parDashboardPage.checkAndAcceptCookies();

		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
			LOG.info("Selecting view partnerships");
			parDashboardPage.selectSearchPartnerships();
			partnershipAdvancedSearchPage.searchPartnerships();
			break;

		case ("par_enforcement_officer@example.com"):
			LOG.info("Selecting search for partnership");
			parDashboardPage.selectSearchforPartnership();
			partnershipSearchPage.searchPartnerships();
			break;

		default:
			LOG.info("Search partnerships");
			parDashboardPage.selectSeePartnerships();
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
			DataStore.saveValue(UsableValues.SIC_CODE, data.get("SIC Code"));
			
			switch (DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase()) {

			case ("direct"):
				DataStore.saveValue(UsableValues.NO_EMPLOYEES, data.get("No of Employees"));
				break;

			case ("co-ordinated"):
				DataStore.saveValue(UsableValues.MEMBERLIST_SIZE, data.get("Member List Size"));
				break;
			}
			
			DataStore.saveValue(UsableValues.TRADING_NAME, DataStore.getSavedValue(UsableValues.BUSINESS_NAME).replace("Business", "trading name"));
			DataStore.saveValue(UsableValues.ENTITY_NAME, data.get("Legal Entity Name"));
			DataStore.saveValue(UsableValues.ENTITY_TYPE, data.get("Legal entity Type"));
			DataStore.saveValue(UsableValues.ENTITY_NUMBER, data.get("Company number"));
		}
		
		LOG.info("Accepting terms");
		parDeclarationPage.acceptTerms();
		
		LOG.info("Add business description");
		parBusinessDetailsPage.enterBusinessDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		
		LOG.info("Confirming address details");
		parBusinessAddressDetailsPage.proceed();
		
		LOG.info("Confirming contact details");
		parBusinessContactDetailsPage.proceed();
		
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
		
		legalEntityTypePage.selectEntityType(DataStore.getSavedValue(UsableValues.ENTITY_NAME),DataStore.getSavedValue(UsableValues.ENTITY_TYPE),DataStore.getSavedValue(UsableValues.ENTITY_NUMBER));
		legalEntityReviewPage.proceed();
		
		LOG.info("Set second part of journey part to true");
		ScenarioContext.secondJourneyPart = true;
	}

	@Then("^the second part of the partnership application is successfully completed$")
	public void the_second_part_of_the_partnership_application_is_successfully_completed() throws Throwable {
		LOG.info("Check and confirm changes");

		switch (DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase()) {

		case ("direct"):
			LOG.info("Check employee size");
			parPartnershipConfirmationPage.checkNoEmployees();
			break;

		case ("co-ordinated"):
			LOG.info("Check membership size");
			parPartnershipConfirmationPage.checkMemberSize();
			break;
		}

		Assert.assertTrue("Appliction not complete", parPartnershipConfirmationPage.checkPartnershipApplicationSecondPart());
		
		if (ScenarioContext.registered == true) {
			parPartnershipConfirmationPage.checkRegNo();
		}
		if (ScenarioContext.registered == false) {
			parPartnershipConfirmationPage.checkEntityName();
		}

		parPartnershipConfirmationPage.confirmDetails();
		parPartnershipConfirmationPage.saveChanges();
		parPartnershipCompletionPage.completeApplication();
	}

	@When("^the user visits the maillog page and extracts the invite link$")
	public void the_user_visits_the_maillog_page_and_extracts_the_invite_link() throws Throwable {
		mailLogPage.navigateToUrl();
		mailLogPage.searchForUserAccountInvite(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		mailLogPage.selectEamilAndGetINviteLink(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
	}

	@When("^the user follows the invitation link$")
	public void the_user_follows_the_invitation_link() throws Throwable {
		parLoginPage.navigateToInviteLink();
	}

	@When("^the user completes the user creation journey$")
	public void the_user_completes_the_user_creation_journey() throws Throwable {
		LOG.info("Completing user creation journey");
		passwordPage.enterPassword("TestPassword", "TestPassword");
		passwordPage.selectRegister();
		userTermsPage.acceptTerms();
		parBusinessContactDetailsPage.proceed();
		userCommsPreferencesPage.proceed();
		userSubscriptionPage.selectContinue();
	}

	@Then("^the user journey creation is successful$")
	public void the_user_journey_creation_is_successful() throws Throwable {
		LOG.info("Checking user creation is sucessful");
		userProfileConfirmationPage.saveChanges();
		userProfileCompletionPage.completeApplication();
	}

	@When("^the user approves the partnership$")
	public void the_user_approves_the_partnership() throws Throwable {
		LOG.info("Approving last created partnership");
		partnershipAdvancedSearchPage.selectApproveBusinessNameLink();
		parDeclarationPage.setAdvancedSearch(true);
		parDeclarationPage.acceptTerms();
		regulatoryFunctionPage.proceed();
		partnershipApprovalPage.completeApplication();
	}

	@When("^the user searches again for the last created partnership$")
	public void the_user_searches_again_for_the_last_created_partnership() throws Throwable {
		LOG.info("Searching for last created partnership");
		partnershipAdvancedSearchPage.searchPartnerships();
	}

	@Then("^the partnership is displayed with Status \"([^\"]*)\" and Actions \"([^\"]*)\"$")
	public void the_partnership_is_displayed_with_Status_and_Actions(String status, String action) throws Throwable {
		LOG.info("Check status of partnership is: " + status + " and action is: " + action);
		partnershipAdvancedSearchPage.checkPartnershipDetails(status, action);
	}

	@When("^the user revokes the partnership$")
	public void the_user_revokes_the_partnership() throws Throwable {
		LOG.info("Revoking last created partnership");
		partnershipAdvancedSearchPage.selectRevokeBusinessNameLink();
		revokePartnershipConfirmationPage.enterRevokeReason("Revoking...");
		partnershipRevokedPage.completeApplication();
	}

	@When("^the user restores the partnership$")
	public void the_user_restores_the_partnership() throws Throwable {
		LOG.info("Restoring last revoked partnership");
		partnershipAdvancedSearchPage.selectRestoreBusinessNameLink();
		restorePartnershipConfirmationPage.proceed();
		partnershipRestoredPage.completeApplication();
	}

	@When("^the user updates the partnership information with the following info: \"([^\"]*)\"$")
	public void the_user_updates_the_partnership_information_with_the_following_info(String desc) throws Throwable {
		parPartnershipConfirmationPage.editAboutPartnership();
		DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, desc);
		parPartnershipDescriptionPage.enterPartnershipDescription(desc);
	}

	@Then("^the partnership is updated correctly$")
	public void the_partnership_is_updated_correctly() throws Throwable {
		Assert.assertTrue("Partnership info doesn't check out", parPartnershipConfirmationPage.checkPartnershipInfo());
	}

	@When("^the user creates a new authority with the following details:$")
	public void the_user_creates_a_new_authority_with_the_following_details(DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.AUTHORITY_NAME, RandomStringGenerator.getAuthorityName(3));
			DataStore.saveValue(UsableValues.AUTHORITY_TYPE, data.get("Authority Type"));
			DataStore.saveValue(UsableValues.AUTHORITY_ADDRESSLINE1, data.get("addressline1"));
			DataStore.saveValue(UsableValues.AUTHORITY_ADDRESSLINE2, data.get("addressline2"));
			DataStore.saveValue(UsableValues.AUTHORITY_TOWN, data.get("town"));
			DataStore.saveValue(UsableValues.AUTHORITY_COUNTY, data.get("county"));
			DataStore.saveValue(UsableValues.AUTHORITY_POSTCODE, data.get("postcode"));
			DataStore.saveValue(UsableValues.ONS_CODE, data.get("ONS Code"));
			DataStore.saveValue(UsableValues.AUTHORITY_REGFUNCTION, data.get("Regulatory Function"));
		}
		
		LOG.info("Select manage authorities.");
		parDashboardPage.selectManageAuthorities();
		
		LOG.info("Select add authority.");
		authoritiesDashboardPage.selectAddAuthority();
		
		LOG.info("Provide authority name.");
		authorityNamePage.enterAuthorityName(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		
		LOG.info("Provide authority type.");
		authorityTypePage.selectAuthorityType(DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE));
		
		LOG.info("Enter authority contact details.");
		authorityAddressDetailsPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.AUTHORITY_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.AUTHORITY_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.AUTHORITY_TOWN), DataStore.getSavedValue(UsableValues.AUTHORITY_COUNTY), DataStore.getSavedValue(UsableValues.AUTHORITY_POSTCODE));
		
		LOG.info("Provide ONS code.");
		onsCodePage.enterONSCode(DataStore.getSavedValue(UsableValues.ONS_CODE));
		
		LOG.info("Select regulatory function.");
		regulatoryFunctionPage.selectRegFunction(DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION));
		
		LOG.info("Clicking the Save button on the Review page.");
		//authorityConfirmationPage.saveChanges();
	}

	@Then("^the authority is created sucessfully$")
	public void the_authority_is_created_sucessfully() throws Throwable {
		LOG.info("On the Authorities Dashboard.");
		
		// Search for the new Authority and Assert it is in the Table.
		
		Assert.assertTrue("Details don't check out", authorityConfirmationPage.checkAuthorityDetails());
		authorityConfirmationPage.saveChanges();
	}

	@When("^the user searches for the last created authority$")
	public void the_user_searches_for_the_last_created_authority() throws Throwable {
		
		LOG.info("Search for last created authority");
		authoritiesDashboardPage.searchAuthority();
		authoritiesDashboardPage.selectAuthority();
	}

	@When("^the user updates all the fields for newly created authority$")
	public void the_user_updates_all_the_fields_for_newly_created_authority() throws Throwable {
		LOG.info("Updating all editble fields against selected authority");
		authorityConfirmationPage.editAuthorityName();
		DataStore.saveValue(UsableValues.AUTHORITY_NAME, DataStore.getSavedValue(UsableValues.AUTHORITY_NAME) + " Updated");
		authorityNamePage.editAuthorityName(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		
		authorityConfirmationPage.editAuthorityType();
		DataStore.saveValue(UsableValues.AUTHORITY_TYPE, "District");
		authorityTypePage.editAuthorityType(DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE));
		
		authorityConfirmationPage.editONSCode();
		DataStore.saveValue(UsableValues.ONS_CODE, DataStore.getSavedValue(UsableValues.ONS_CODE) + " Updated");
		onsCodePage.editONSCode(DataStore.getSavedValue(UsableValues.ONS_CODE));
		
		authorityConfirmationPage.editRegFunction();
		DataStore.saveValue(UsableValues.AUTHORITY_REGFUNCTION, "Alphabet learning");
		regulatoryFunctionPage.editRegFunction(DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION));
	}

	@Then("^the update for the authority is successful$")
	public void the_update_for_the_authority_is_successful() throws Throwable {
		LOG.info("Check all updated changes check out");
		Assert.assertTrue("Details don't check out", authorityConfirmationPage.checkAuthorityDetails());
		authorityConfirmationPage.saveChanges();
	}

	@Given("^the user updates all the fields for last created organisation$")
	public void the_user_updates_all_the_fields_for_last_created_organisation() throws Throwable {
		LOG.info("Update all fields");
		businessConfirmationPage.editOrganisationName();
		
		DataStore.saveValue(UsableValues.BUSINESS_NAME, DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + " Updated");
		parBusinessPage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		businessConfirmationPage.editOrganisationDesc();
		
		DataStore.saveValue(UsableValues.BUSINESS_DESC, DataStore.getSavedValue(UsableValues.BUSINESS_DESC) + " Updated");
		parBusinessDetailsPage.enterBusinessDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		businessConfirmationPage.editTradingName();
		
		DataStore.saveValue(UsableValues.TRADING_NAME, DataStore.getSavedValue(UsableValues.TRADING_NAME) + " Updated");
		tradingPage.enterTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
		
		businessConfirmationPage.editSICCode();
		sicCodePage.selectSICCode("allow people to eat");
	}

	@When("^the user searches for the last created organisation$")
	public void the_user_searches_for_the_last_created_organisation() throws Throwable {
		LOG.info("Search and select last created organisation");
		parDashboardPage.selectManageOrganisations();
		organisationDashboardPage.searchOrganisation();
		organisationDashboardPage.selectOrganisation();
	}

	@Then("^all the fields are updated correctly$")
	public void all_the_fields_are_updated_correctly() throws Throwable {
		LOG.info("Check all updated changes check out");
		Assert.assertTrue("Details don't check out", businessConfirmationPage.checkAuthorityDetails());
		businessConfirmationPage.saveChanges();
	}

	@When("^the user creates an enforcement notice against the partnership with the following details:$")
	public void the_user_creates_an_enforcement_notice_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENFORCEMENT_TYPE, data.get("Enforcement Action"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_REGFUNC, data.get("Regulatory Function"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_FILENAME, data.get("Attachment"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_DESCRIPTION, data.get("Description"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_TITLE, data.get("Title"));
		}
		
		LOG.info("Create enformcement notification against partnership");
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		parPartnershipConfirmationPage.createEnforcement();
		enforcementNotificationPage.proceed();
		enforcementContactDetailsPage.proceed();
		
		enforcementLegalEntityPage.enterEntity(DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		enforcementLegalEntityPage.proceed();
		
		enforcementDetailsPage.selectEnforcementType(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TYPE));
		enforcementDetailsPage.enterEnforcementDescription("Enformcement description");
		enforcementDetailsPage.proceed();
		
		enforcementActionPage.selectRegFunc(DataStore.getSavedValue(UsableValues.ENFORCEMENT_REGFUNC));
		enforcementActionPage.chooseFile(DataStore.getSavedValue(UsableValues.ENFORCEMENT_FILENAME));
		enforcementActionPage.enterEnforcementDescription(DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION).toLowerCase());
		enforcementActionPage.enterTitle(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE));
		enforcementActionPage.proceed();
	}

	@Then("^all the fields for the enforcement notice are updated correctly$")
	public void all_the_fields_for_the_enforcement_are_updated_correctly() throws Throwable {
		LOG.info("Check all updated changes check out");
		Assert.assertTrue("Details don't check out", enforcementReviewPage.checkEnforcementCreation());
		enforcementReviewPage.saveChanges();
	}

	@When("^the user selects the last created enforcement notice$")
	public void the_user_selects_the_last_created_enforcement() throws Throwable {
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_enforcement_officer@example.com"):
			LOG.info("Select last created enforcement");
			parDashboardPage.selectSeeEnforcementNotices();
			enforcementSearchPage.searchEnforcements();
			enforcementSearchPage.selectEnforcement();
			break;

		case ("par_authority@example.com"):
			LOG.info("Select last created enforcement");
			parDashboardPage.selectSeeEnforcementNotices();
			enforcementSearchPage.searchEnforcements();
			enforcementSearchPage.selectEnforcement();
			break;

		case ("par_helpdesk@example.com"):
			LOG.info("Searching for an Enforcement Notice.");
			parDashboardPage.selectManageEnforcementNotices();
			enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
			enforcementSearchPage.selectEnforcement();
			break;

		default:
			// do nothing
		}
	}

	@When("^the user approves the enforcement notice$")
	public void the_user_approves_the_enforcement_notice() throws Throwable {
		LOG.info("Approve the enforcement");
		proposedEnforcementPage.selectAllow();
		proposedEnforcementPage.proceed();
		enforcementReviewPage.saveChanges();
		enforcementCompletionPage.complete();
	}

	@Then("^the enforcement notice is set to approved status$")
	public void the_enforcement_notice_is_set_to_approved_status() throws Throwable {
		LOG.info("Check the enformcement is approved");
		enforcementSearchPage.searchEnforcements();
		Assert.assertTrue("Enforcement Status doesn't check out", enforcementSearchPage.getStatus().equalsIgnoreCase("Approved"));
	}
	
	@When("^the user blocks the enforcement notice with the following reason: \"([^\"]*)\"$")
	public void the_user_blocks_the_enforcement_notice_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Block the Enforcement Notice.");
		proposedEnforcementPage.selectBlock();
		proposedEnforcementPage.enterReasonForBlockingEnforcement(reason);
		proposedEnforcementPage.proceed();
		
		enforcementReviewPage.saveChanges();
		enforcementCompletionPage.complete();
	}
	
	@Then("^the enforcement notice is set to blocked status$")
	public void the_enforcement_notice_is_set_to_blocked_status() throws Throwable {
		LOG.info("Check the Enformcement Notice is Blocked.");
		enforcementSearchPage.searchEnforcements();
		Assert.assertTrue("Enforcement Status doesn't check out", enforcementSearchPage.getStatus().equalsIgnoreCase("Blocked"));
	}
	
	@When("^the user searches for the last created enforcement notice$")
	public void the_user_searches_for_the_last_created_enforcement_notice() throws Throwable {
		LOG.info("Select last created enforcement");
		parDashboardPage.selectManageEnforcementNotices();
		enforcementSearchPage.searchEnforcements();
		enforcementSearchPage.removeEnforcement();
	}

	@Then("^the user removes the enforcement notice successfully$")
	public void the_user_removes_the_enforcement_notice_successfully() throws Throwable {
		LOG.info("Check enforcement notice is removed");
		removeEnforcementPage.selectRevokeReason("This is a duplicate enforcement");
		removeEnforcementPage.enterRevokeDescription("Revoking");
		removeEnforcementConfirmationPage.acceptTerms();
		enforcementSearchPage.searchEnforcements();
		Assert.assertTrue("Some results are returned indeed", enforcementSearchPage.confirmNoReturnedResults());
	}

	@When("^the user uploads an inspection plan against the partnership with the following details:$")
	public void the_user_uploads_an_inspection_plan_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Upload inspection plan and save details");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_DESCRIPTION, data.get("Description"));
		}
		
		partnershipAdvancedSearchPage.selectPartnershipLink();
		parPartnershipConfirmationPage.selectSeeAllInspectionPlans();
		
		inspectionPlanSearchPage.selectUploadLink();
		
		uploadInspectionPlanPage.chooseFile("link.txt");
		uploadInspectionPlanPage.uploadFile();
		
		inspectionPlanDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE));
		inspectionPlanDetailsPage.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
		inspectionPlanDetailsPage.save();
		
		inspectionPlanExpirationPage.enterDate("ddMMYYYY");
		inspectionPlanExpirationPage.save();
		
		LOG.info("Check inspection plan status is set to \"Current\"");
		Assert.assertTrue("Failed: Status not set to \"Current\"", inspectionPlanSearchPage.getPlanStatus().equalsIgnoreCase("Current"));
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
		parPartnershipConfirmationPage.selectSeeAllAdviceNotices();
		
		adviceNoticeSearchPage.selectUploadLink();
		uploadAdviceNoticePage.chooseFile("link.txt");
		uploadAdviceNoticePage.uploadFile();
		
		adviceNoticeDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		adviceNoticeDetailsPage.selectAdviceType(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TYPE));
		adviceNoticeDetailsPage.selectRegFunc(DataStore.getSavedValue(UsableValues.ADVICENOTICE_REGFUNCTION));
		adviceNoticeDetailsPage.enterDescription(DataStore.getSavedValue(UsableValues.ADVICENOTICE_DESCRIPTION));
		adviceNoticeDetailsPage.save();
	}

	@When("^the user uploads an advice plan against the partnership with the following details:$")
	public void the_user_uploads_an_insection_plan_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Upload inspection plan and save details");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_DESCRIPTION, data.get("Description"));
		}
		
		partnershipAdvancedSearchPage.selectPartnershipLink();
		parPartnershipConfirmationPage.selectSeeAllInspectionPlans();
		
		inspectionPlanSearchPage.selectUploadLink();
		uploadInspectionPlanPage.chooseFile("link.txt");
		uploadInspectionPlanPage.uploadFile();
		
		inspectionPlanDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE));
		inspectionPlanDetailsPage.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
		inspectionPlanDetailsPage.save();
		
		inspectionPlanExpirationPage.enterDate("ddMMYYYY");
		inspectionPlanExpirationPage.save();
		
		LOG.info("Check inspection plan status is set to \"Current\"");
		Assert.assertTrue("Failed: Status not set to \"Current\"", inspectionPlanSearchPage.getPlanStatus().equalsIgnoreCase("Current"));
	}

	@When("^the user updates the last created inspection plan against the partnership with the following details:$")
	public void the_user_updates_the_last_created_inspection_plan_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Edit inspection plan and save details");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_DESCRIPTION, data.get("Description"));
		}
		
		partnershipAdvancedSearchPage.selectPartnershipLink();
		parPartnershipConfirmationPage.selectSeeAllInspectionPlans();
		
		inspectionPlanSearchPage.selectEditLink();
		inspectionPlanDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE));
		inspectionPlanDetailsPage.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
		inspectionPlanDetailsPage.save();
		
		inspectionPlanExpirationPage.save();
		inspectionPlanSearchPage.selectInspectionPlan();
	}

	@Then("^the inspection plan is updated correctly$")
	public void the_inspection_plan_is_updated_correctly() throws Throwable {
		LOG.info("Check the inspection plan details are correct");
		Assert.assertTrue("Failed: inspection plan details not correct", inspectionPlanReviewPage.checkInspectionPlan());
	}

	@When("^the user submits an inspection feedback against the inspection plan with the following details:$")
	public void the_user_submits_an_inspection_feedback_against_the_inspection_plan_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit inspection feedback against partnership");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION, data.get("Description"));
		}
		
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		parPartnershipConfirmationPage.selectSendInspectionFeedbk();
		
		inspectionContactDetailsPage.proceed();
		
		inspectionFeedbackDetailsPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION));
		inspectionFeedbackDetailsPage.chooseFile("link.txt");
		inspectionFeedbackDetailsPage.proceed();
		inspectionFeedbackConfirmationPage.saveChanges();
		inspectionFeedbackCompletionPage.complete();
	}

	@When("^the user searches for the last created inspection feedback$")
	public void the_user_searches_for_the_last_created_inspection_feedback() throws Throwable {
		LOG.info("Search for last created inspection feedback");
		parDashboardPage.selectSeeInspectionFeedbackNotices();
		inspectionFeedbackSearchPage.selectInspectionFeedbackNotice();
	}

	@Then("^the user successfully approves the inspection feedback$")
	public void the_user_successfully_approves_the_inspection_feedback() throws Throwable {
		LOG.info("Verify the inspection feedback description");
		Assert.assertTrue("Failed: Inspection feedback description doesn't check out ", inspectionFeedbackConfirmationPage.checkInspectionFeedback());
	}

	@Given("^the user clicks the PAR Home page link$")
	public void the_user_clicks_the_PAR_Home_page_link() throws Throwable {
		LOG.info("Click PAR header to navigate to the PAR Home Page");
		parAuthorityPage.selectPageHeader();
	}

	@When("^the user is on the search for a partnership page$")
	public void the_user_is_on_the_search_for_a_partnership_page() throws Throwable {
		LOG.info("Click Search Public List of Partnerships to navigate to PAR Search for Partnership Page");
		parHomePage.selectPartnershipSearchLink();
	}

	@Then("^the user can search for a PA Organisation Trading name Company number$")
	public void the_user_can_search_for_a_PA_Organisation_Trading_name_Company_number() throws Throwable {
		LOG.info("Enter business name and click the search button");
		partnershipSearchPage.searchForPartnership(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		partnershipSearchPage.clickSearchButton();
	}

	@Then("^the user is shown the information for that partnership$")
	public void the_user_is_shown_the_information_for_that_partnership() throws Throwable {
		LOG.info("Verify the Partnership contains the business name");
		assertTrue(partnershipSearchPage.partnershipContains(DataStore.getSavedValue(UsableValues.BUSINESS_NAME)));
	}

	@Given("^the user submits a response to the inspection feedback with the following details:$")
	public void the_user_submits_a_response_to_the_inspection_feedback_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit response to inspection feedback request");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1, data.get("Description"));
		}

		inspectionFeedbackConfirmationPage.submitResponse();
		
		replyInspectionFeedbackPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1));
		replyInspectionFeedbackPage.chooseFile("link.txt");
		replyInspectionFeedbackPage.proceed();
		
		LOG.info("Verify the inspection feedback response");
		Assert.assertTrue("Failed: Inspection feedback response doesn't check out ", inspectionFeedbackConfirmationPage.checkInspectionResponse());
	}

	@When("^the user sends a reply to the inspection feedback message with the following details:$")
	public void the_user_sends_a_reply_to_the_inspection_feedback_message_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit reply to inspection feedback response");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE2, data.get("Description"));
		}
		
		inspectionFeedbackConfirmationPage.submitResponse();
		
		replyInspectionFeedbackPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE2));
		replyInspectionFeedbackPage.chooseFile("link.txt");
		replyInspectionFeedbackPage.proceed();
	}

	@Then("^the inspection feedback reply is received successfully$")
	public void the_inspection_feedback_reply_is_received_successfully() throws Throwable {
		LOG.info("Verify the inspection feedback reply");
		Assert.assertTrue("Failed: Inspection feedback reply doesn't check out ", 	inspectionFeedbackConfirmationPage.checkInspectionReply());
	}

	@When("^the user submits a deviation request against an inspection plan with the following details:$")
	public void the_user_submits_a_deviation_request_against_an_inspection_plan_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit Deviation Request");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.DEVIATION_DESCRIPTION, data.get("Description"));
		}
		
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		parPartnershipConfirmationPage.selectDeviateInspectionPlan();
		enforcementContactDetailsPage.save();
		
		requestDeviationPage.enterDescription(DataStore.getSavedValue(UsableValues.DEVIATION_DESCRIPTION));
		requestDeviationPage.chooseFile("link.txt");
		requestDeviationPage.proceed();
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
		
		parDashboardPage.selectSeeDeviationRequests();
		deviationSearchPage.selectDeviationRequest();
	}
	
	@When("^the user blocks the deviation request with the following reason: \"([^\"]*)\"$")
	public void the_user_blocks_the_deviation_request_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Block the deviation request.");
		
		deviationApprovalPage.selectBlock();
		deviationApprovalPage.enterReasonForBlocking(reason);
		deviationApprovalPage.proceed();
	}

	@Then("^the deviation request is set to blocked status$")
	public void the_deviation_request_is_set_to_blocked_status() throws Throwable {
		LOG.info("Check the Deviation Request is Blocked on the Review Page.");
		
		Assert.assertTrue("Failed: Deviation request status is not Set to Blocked", deviationReviewPage.checkDeviationStatusBlocked());
		deviationReviewPage.saveChanges();
		deviationCompletionPage.complete();
	}

	@Then("^the user successfully approves the deviation request$")
	public void the_user_successfully_approves_the_deviation_request() throws Throwable {
		LOG.info("Approve the deviation request");
		deviationApprovalPage.selectAllow();
		deviationApprovalPage.proceed();
		
		Assert.assertTrue("Failed: Deviation request status not correct", deviationReviewPage.checkDeviationStatusApproved());
		deviationReviewPage.saveChanges();
		deviationCompletionPage.complete();
	}

	@Given("^the user submits a response to the deviation request with the following details:$")
	public void the_user_submits_a_response_to_the_deviation_request_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit response to the deviation request");
		deviationSearchPage.selectDeviationRequest();
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1, data.get("Description"));
		}
		
		deviationReviewPage.submitResponse();
		
		replyDeviationRequestPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1));
		replyDeviationRequestPage.chooseFile("link.txt");
		replyDeviationRequestPage.proceed();
		
		LOG.info("Verify the deviation response");
		Assert.assertTrue("Failed: Deviation response doesn't check out ", deviationReviewPage.checkDeviationResponse());
	}

	@When("^the user sends a reply to the deviation request message with the following details:$")
	public void the_user_sends_a_reply_to_the_deviation_request_message_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit reply to the deviation request");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE2, data.get("Description"));
		}
		
		deviationReviewPage.submitResponse();
		
		replyDeviationRequestPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE2));
		replyDeviationRequestPage.chooseFile("link.txt");
		replyDeviationRequestPage.proceed();
		
		LOG.info("Verify the deviation response");
		Assert.assertTrue("Failed: Deviation reply doesn't check out ", deviationReviewPage.checkDeviationResponse());
	}

	@Then("^the deviation reply received successfully$")
	public void the_deviation_reply_received_successfully() throws Throwable {
		LOG.info("Verify the deviation response");
		Assert.assertTrue("Failed: Deviation reply doesn't check out ", deviationReviewPage.checkDeviationResponse());
	}

	@When("^the user submits a general enquiry with the following details:$")
	public void the_user_submits_a_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Send general query");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENQUIRY_DESCRIPTION, data.get("Description"));
		}
		
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		parPartnershipConfirmationPage.sendGeneralEnquiry();
		enquiryContactDetailsPage.proceed();
		
		requestEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION));
		requestEnquiryPage.chooseFile("link.txt");
		requestEnquiryPage.proceed();
	}
	
	@When("^the user sends a general enquiry for an enforcement notice with the following details:$")
	public void the_user_sends_a_general_enquiry_for_an_enforcement_notice_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Send general query");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENQUIRY_DESCRIPTION, data.get("Description"));
		}
		
		partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		parPartnershipConfirmationPage.createEnforcement();
		
		enforcementNotificationPage.selectDiscussEnforcement();
		enquiryContactDetailsPage.proceed();
		
		requestEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION));
		requestEnquiryPage.chooseFile("link.txt");
		requestEnquiryPage.proceed();
	}

	@Then("^the Enquiry is created Successfully$")
	public void the_Enquiry_is_created_Successfully() throws Throwable {
		LOG.info("Verify the enquiry is created.");
		
		Assert.assertTrue("Failed: Enquiry details are not correct.", enquiryReviewPage.checkEnquiryCreation());
		
		enquiryReviewPage.saveChanges();
		enquiryCompletionPage.complete();
	}

	@When("^the user searches for the last created general enquiry$")
	public void the_user_searches_for_the_last_created_general_enquiry() throws Throwable {
		LOG.info("Search for last created enquiry");
		parDashboardPage.selectGeneralEnquiries();
		enquiriesSearchPage.selectEnquiry();
	}

	@Then("^the user successfully views the enquiry$")
	public void the_user_successfully_views_the_enquiry() throws Throwable {
		enquiryReviewPage.checkEnquiryCreation();
	}

	@Given("^the user submits a response to the general enquiry with the following details:$")
	public void the_user_submits_a_response_to_the_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit reply to the enquiry");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENQUIRY_REPLY, data.get("Description"));
		}
		
		enquiryReviewPage.submitResponse();
		
		replyEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY));
		replyEnquiryPage.chooseFile("link.txt");
		replyEnquiryPage.proceed();
		
		LOG.info("Verify the reply message");
		Assert.assertTrue("Failed: Enquiry reply doesn't check out ", enquiryReviewPage.checkEnquiryReply());
	}

	@When("^the user sends a reply to the general enquiry with the following details:$")
	public void the_user_sends_a_reply_to_the_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENQUIRY_REPLY1, data.get("Description"));
		}
		
		enquiryReviewPage.submitResponse();
		
		replyEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY1));
		replyEnquiryPage.chooseFile("link.txt");
		replyEnquiryPage.proceed();
		
		LOG.info("Verify the reply message");
		Assert.assertTrue("Failed: Enquiry reply doesn't check out ", enquiryReviewPage.checkEnquiryReply1());
	}

	@When("^the user selects a contact to update$")
	public void the_user_selects_a_contact_to_update() throws Throwable {
		DataStore.saveValue(UsableValues.BUSINESS_EMAIL, DataStore.getSavedValue(UsableValues.LOGIN_USER));
		parDashboardPage.selectManageProfileDetails();

		// This can be used in a manage colleagues update person test.
		DataStore.saveValue(UsableValues.ACCOUNT_ID, userProfilePage.getAccountID()); 

		userProfilePage.selectContactToUpdate();
		userProfilePage.selectContinueButton();
		LOG.info("Selected user contact to update.");
	}

	@Then("^the user can successfully subscribe to PAR News$")
	public void the_user_can_successfully_subscribe_to_PAR_News() throws Throwable {
		updateUserContactDetailsPage.selectContinueButton();
		updateUserCommunicationPreferencesPage.selectContinueButton();

		updateUserSubscriptionsPage.selectPARNewsSubscription();
		updateUserSubscriptionsPage.selectContinueButton();
		LOG.info("Successfully subscribed from PAR news letter.");

		userProfileConfirmationPage.saveChanges();
		userProfileCompletionPage.completeApplication();

	}

	@Then("^the user can successfully unsubscribe from PAR News$")
	public void the_user_can_successfully_unsubscribe_from_PAR_News() throws Throwable {
		updateUserContactDetailsPage.selectContinueButton();
		updateUserCommunicationPreferencesPage.selectContinueButton();

		updateUserSubscriptionsPage.selectPARNewsUnsubscription();
		updateUserSubscriptionsPage.selectContinueButton();
		LOG.info("Successfully unsubscribed from PAR news letter.");

		userProfileConfirmationPage.saveChanges();
		userProfileCompletionPage.completeApplication();

	}

	@When("^the user searches for the par_authority email$")
	public void the_user_searches_for_the_par_authority_email() throws Throwable {
		newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		newsLetterSubscriptionPage.ClickSearchButton();
		LOG.info("Searching for the Authority Email:" + DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL) + ".");
	}

	@When("^the user is on the Subscriptions page$")
	public void the_user_is_on_the_Subscriptions_page() throws Throwable {
		parDashboardPage.selectManageSubscriptions();
		LOG.info("Navigated to Manage Subscriptions Page.");
	}

	@Then("^the user can verify the email is successfully in the Subscriptions List$")
	public void the_user_can_verify_the_email_is_successfully_in_the_Subscriptions_List() throws Throwable {
		LOG.info("Assert the Email is successfully added to the Subscription List.");
		assertTrue("Failed: Email address was not added to the PAR News Subscription List.",
				newsLetterSubscriptionPage.verifyTableElementExists());
	}

	@Then("^the user can verify the email is successfully removed from the Subscriptions List$")
	public void the_user_can_verify_the_email_is_successfully_removed_from_the_Subscriptions_List() throws Throwable {
		LOG.info("Assert the Email is removed successfully from the Subscription List.");
		assertTrue("Failed: Email address was not removed from the PAR News Subscription List.",
				newsLetterSubscriptionPage.verifyTableElementIsNull());
	}

	@When("^the user is on the Manage a subscription list page$")
	public void the_user_is_on_the_Manage_a_subscription_list_page() throws Throwable {
		parDashboardPage.selectManageSubscriptions();
		newsLetterSubscriptionPage.selectManageSubsciptions();
		LOG.info("Navigated to Manage Subscriptions Page.");
		LOG.info("Email with the largest number: " + DataStore.getSavedValue(UsableValues.LAST_PAR_NEWS_EMAIL));
	}

	@When("^the user enters a new email to add to the list \"([^\"]*)\"$")
	public void the_user_enters_a_new_email_to_add_to_the_list(String email) throws Throwable {
		newsLetterManageSubscriptionListPage.selectInsertNewEmailRadioButton();
		newsLetterManageSubscriptionListPage.AddNewEmail(email);
		DataStore.saveValue(UsableValues.PAR_NEWS_EMAIL, email);
		newsLetterManageSubscriptionListPage.clickContinueButton();
		LOG.info("Adding a new email to the subscription list.");
		newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify the new email was added successfully$")
	public void the_user_can_verify_the_new_email_was_added_successfully() throws Throwable {
		newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.PAR_NEWS_EMAIL));
		newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue(newsLetterSubscriptionPage.verifyTableElementExists());
		LOG.info("Successfully added a new email to the Subscription list.");
	}

	@When("^the user enters an email to be removed from the list \"([^\"]*)\"$")
	public void the_user_enters_an_email_to_be_removed_from_the_list(String email) throws Throwable {
		newsLetterManageSubscriptionListPage.selectRemoveEmailRadioButton();
		newsLetterManageSubscriptionListPage.RemoveEmail(email);
		DataStore.saveValue(UsableValues.PAR_NEWS_EMAIL, email);
		
		newsLetterManageSubscriptionListPage.clickContinueButton();
		
		LOG.info("Removing an email from the subscription list.");
		newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify the email was removed successfully$")
	public void the_user_can_verify_the_email_was_removed_successfully() throws Throwable {
		newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.PAR_NEWS_EMAIL));
		newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue(newsLetterSubscriptionPage.verifyTableElementIsNull());
		LOG.info("Successfully removed an email from the Subscription list.");
	}

	@When("^the user enters a list of new emails to replace the subscription list$")
	public void the_user_enters_a_list_of_new_emails_to_replace_the_subscription_list() throws Throwable {

		newsLetterManageSubscriptionListPage.selectReplaceSubscriptionListRadioButton();
		newsLetterManageSubscriptionListPage.clickContinueButton();
		LOG.info("Adding new emails to replace the original Subscription List.");

		newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify an email from the original list was removed successfully$")
	public void the_user_can_verify_an_email_from_the_original_list_was_removed_successfully() throws Throwable {

		newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.LAST_PAR_NEWS_EMAIL));
		newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue(newsLetterSubscriptionPage.verifyTableElementIsNull());
		LOG.info("Successfully replaced the original subscription list with a new list.");
	}

	@When("^the user creates a new person:$")
	public void the_user_creates_a_new_person(DataTable details) throws Throwable {
		parDashboardPage.selectManagePeople();
		managePeoplePage.selectAddPerson();

		LOG.info("Adding a new person.");
		personsContactDetailsPage.enterContactDetails(details);
		personsContactDetailsPage.clickContinueButton();

		LOG.info("Successfully entered new contact details.");

		personAccountPage.selectInviteUserToCreateAccount();
		personAccountPage.clickContinueButton();

		LOG.info("Successfully chose to invite the person to create an account.");
		personMembershipPage.selectOrganisation(details);
		personMembershipPage.selectAuthority(details);
		personMembershipPage.clickContinueButton();
		
		LOG.info("Chosen Organisation: " + DataStore.getSavedValue(UsableValues.CHOSEN_ORGANISATION));
		LOG.info("Chosen Authority: " + DataStore.getSavedValue(UsableValues.CHOSEN_AUTHORITY));

		personUserTypePage.selectEnforcementOfficer();
		personUserTypePage.clickContinueButton();
		LOG.info("User Account Type: " + DataStore.getSavedValue(UsableValues.ACCOUNT_TYPE));

		personCreateAccountPage.clickInviteButton();

		LOG.info("Successfully sent account invite.");

		userProfileConfirmationPage.saveChanges();
		userProfileCompletionPage.clickDoneGoToProfile();
	}

	@Then("^the user can verify the person was created successfully and can see resend an account invite$")
	public void the_user_can_verify_the_person_was_created_successfully_and_can_see_resend_an_account_invite() throws Throwable {
		assertTrue("Failed: Header does not contain the person's fullname and title.", personsProfilePage.checkHeaderForName());
		assertTrue("Failed: Cannot find the Re-send account creation invite link.", personsProfilePage.checkForUserAccountInvitationLink());
		assertTrue("Failed: Contact name field does not contain the person's fullname and title.", personsProfilePage.checkContactName());
		assertTrue("Failed: Contact email field does not contain the correct email address.", personsProfilePage.checkContactEmail());
		assertTrue("Failed: Contact numbers field does not contain the work and/or mobile phone numbers", personsProfilePage.checkContactPhoneNumbers());
		assertTrue("Failed: Both Contact Locations are not displayed.", personsProfilePage.seeMoreContactInformation());
	}

	@When("^the user searches for an existing person successfully$")
	public void the_user_searches_for_an_existing_person_successfully() throws Throwable {
		parDashboardPage.selectManagePeople();

		String personsName = DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " "
				+ DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);

		managePeoplePage.enterNameOrEmail(personsName);
		managePeoplePage.clickSubmit();

		managePeoplePage.clickManageContact();

		LOG.info("Found an existing user with the name: " + personsName);
	}

	@When("^the user updates an existing person:$")
	public void the_user_updates_an_existing_person_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Updating an existing person.");
		personsProfilePage.clickUpdateUserButton();
		
		personsContactDetailsPage.enterContactDetails(details);
		personsContactDetailsPage.clickContinueButton();

		LOG.info("Successfully entered new contact details.");

		personAccountPage.selectInviteUserToCreateAccount();
		personAccountPage.clickContinueButton();

		LOG.info("Successfully chose to invite the person to create an account.");
		personMembershipPage.selectOrganisation(details);
		personMembershipPage.selectAuthority(details);
		personMembershipPage.clickContinueButton();

		LOG.info("Chosen Organisation: " + DataStore.getSavedValue(UsableValues.CHOSEN_ORGANISATION));
		LOG.info("Chosen Authority: " + DataStore.getSavedValue(UsableValues.CHOSEN_AUTHORITY));

		personUserTypePage.selectAuthorityMember();
		personUserTypePage.clickContinueButton();

		LOG.info("User Account Type: " + DataStore.getSavedValue(UsableValues.ACCOUNT_TYPE));

		personCreateAccountPage.clickInviteButton();

		LOG.info("Successfully sent account invite.");

		userProfileConfirmationPage.saveChanges();
		userProfileCompletionPage.clickDoneGoToProfile();
	}

	@Then("^the user can verify the person was updated successfully and can see resend an account invite$")
	public void the_user_can_verify_the_person_was_updated_successfully_and_can_see_resend_an_account_invite()
			throws Throwable {
		assertTrue("Failed: Header does not contain the person's fullname and title.", personsProfilePage.checkHeaderForName());
		assertTrue("Failed: Cannot find the Re-send account creation invite link.", personsProfilePage.checkForUserAccountInvitationLink());
		assertTrue("Failed: Contact name field does not contain the person's fullname and title.", personsProfilePage.checkContactName());
		assertTrue("Failed: Contact email field does not contain the correct email address.", personsProfilePage.checkContactEmail());
		assertTrue("Failed: Contact numbers field does not contain the work and/or mobile phone numbers", personsProfilePage.checkContactPhoneNumbers());
		assertTrue("Failed: Both Contact Locations are not displayed.", personsProfilePage.seeMoreContactInformation());
	}

	@Then("^the user can verify the enforcement officers details are displayed$")
	public void the_user_can_verify_the_enforcement_officers_details_are_displayed() throws Throwable {
		assertTrue("Failed: Officer details not displayed", enforcementReviewPage.checkOfficerDetails());
	}

	@When("^the user searches for a partnership with the Test Business \"([^\"]*)\" name$")
	public void the_user_searches_for_a_partnership_with_the_Test_Business_name(String search) throws Throwable {
		parDashboardPage.selectSearchforPartnership();

		partnershipSearchPage.selectPartnershipLink(search);
		parPartnershipConfirmationPage.sendGeneralEnquiry();
	}

	@Then("^the user can submit a general enquiry with description:$")
	public void the_user_can_submit_a_general_enquiry_with_description(DataTable description) throws Throwable {
		LOG.info("Creating Enquiry Notice.");

		enquiryContactDetailsPage.proceed();

		for (Map<String, String> data : description.asMaps(String.class, String.class)) {
			requestEnquiryPage.enterDescription(data.get("Description"));
		}

		requestEnquiryPage.proceed();
		enquiryReviewPage.saveChanges();
		enquiryCompletionPage.complete();
		parPartnershipConfirmationPage.clickDone();

		LOG.info("Successfully created Enquiry Notice.");
	}

	@Then("^the user can verify the Enforcement details:$")
	public void the_user_can_verify_the_Enforcement_details(DataTable details) throws Throwable {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			assertEquals(data.get("Officer"), viewEnquiryPage.getEnforcementOfficerDetails());
			assertEquals(data.get("Enforcing"), viewEnquiryPage.getEnforcingAuthorityName());
			assertEquals(data.get("Primary"), viewEnquiryPage.getPrimaryAuthorityName());
			assertEquals(data.get("Summary"), viewEnquiryPage.getSummaryOfEnquiryText());
		}
		
		LOG.info("Asserting the Equiry Notice Details.");
	}

	@Then("^the user successfully revokes the last created inspection plan$")
	public void the_user_successfully_removes_the_last_created_inspection_plan() throws Throwable {
		LOG.info("Revoking inspection plan and confirming it is revoked");
		partnershipAdvancedSearchPage.selectPartnershipLink();
		parPartnershipConfirmationPage.selectSeeAllInspectionPlans();
		inspectionPlanSearchPage.selectRevokeLink();
		revokeReasonInspectionPlanPage.enterRevokeDescription();
		assertEquals(inspectionPlanSearchPage.getPlanStatus(), "Revoked");
	}

	@When("^the user has revoked the last created inspection plan$")
	public void the_user_has_revoked_the_last_created_inspection_plan() throws Throwable {
		LOG.info("Removing inspection plan");
		inspectionPlanSearchPage.selectRemoveLink();
		removeReasonInspectionPlanPage.enterRemoveDescription();
	}

	@Then("^the inspection plan is successfully removed$")
	public void the_inspection_plan_is_successfully_removed() throws Throwable {
		LOG.info("Confirm inspection plan is removed");
		assertEquals(inspectionPlanSearchPage.getPlanStatus(), "No results returned");
	}
	
	@When("^the user searches for the last created partnership Authority$")
	public void the_user_searches_for_the_last_created_partnership_Authority() throws Throwable {
		LOG.info("Searching for and selecting the latest Partnerships Primary Authority.");
		
		parDashboardPage.selectSearchPartnerships();
		
		partnershipAdvancedSearchPage.searchPartnerships();
		partnershipAdvancedSearchPage.selectPrimaryAuthorityLink();
	}
	
	@When("^the user updates the About the Partnership and Regulatory Functions:$")
	public void the_user_updates_the_About_the_Partnership_and_Regulatory_Functions(DataTable details) throws Throwable {
		LOG.info("Updating about the Partnership and Regulatory Functions.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, data.get("About the Partnership"));
		}
		
		parPartnershipConfirmationPage.editAboutPartnership();
		parPartnershipDescriptionPage.enterDescription(DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO));
		parPartnershipDescriptionPage.clickSave();
		
		parPartnershipConfirmationPage.editRegulatoryFunctions();
		regulatoryFunctionPage.updateRegFunction();
	}

	@Then("^the About the Partnership and Regulatory Functions are updated Successfully$")
	public void the_About_the_Partnership_and_Regulatory_Functions_are_updated_Successfully() throws Throwable {
		LOG.info("Verifying About the Partnership and Regulatory Functions have been updated Successfully.");
		
		// Verifying the updated values are visible on the Partnerships Details Page.
		assertTrue(parPartnershipConfirmationPage.checkPartnershipInfo());
		assertTrue(parPartnershipConfirmationPage.checkRegulatoryFunctions());
		
		parPartnershipConfirmationPage.clickSave();
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
		
		parPartnershipConfirmationPage.editOrganisationAddress();
		editRegisteredAddressPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1),
				DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN),
				DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY),
				DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY),
				DataStore.getSavedValue(UsableValues.BUSINESS_NATION),
				DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		editRegisteredAddressPage.clickSaveButton();
		
		LOG.info("Selected Country: " + DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY));
		LOG.info("Selected Nation: " + DataStore.getSavedValue(UsableValues.BUSINESS_NATION));
		
		parPartnershipConfirmationPage.editAboutTheOrganisation();
		parPartnershipDescriptionPage.enterDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		parPartnershipDescriptionPage.clickSave();
		
		parPartnershipConfirmationPage.editSICCode();
		sicCodePage.editSICCode(DataStore.getSavedValue(UsableValues.SIC_CODE));
		
		parPartnershipConfirmationPage.editTradingName();
		tradingPage.editTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
	}

	@Then("^all of the Partnership details have been updated successfully$")
	public void all_of_the_Partnership_details_have_been_updated_successfully() throws Throwable {
		LOG.info("Verifying all the remaining Partnership details have been updated Successfully.");

		assertTrue(parPartnershipConfirmationPage.checkOrganisationAddress());
		assertTrue(parPartnershipConfirmationPage.checkAboutTheOrganisation());
		assertTrue(parPartnershipConfirmationPage.checkSICCode());
		assertTrue(parPartnershipConfirmationPage.checkTradingName());
		
		parPartnershipConfirmationPage.clickSave();
	}
	
	@When("^the user adds a Primary Authority contact to be Invited with the following details:$")
	public void the_user_adds_a_Primary_Authority_contact_to_be_Invited_with_the_following_details(DataTable details) throws Throwable {
		
		parPartnershipConfirmationPage.addAnotherAuthorityContactButton();

		LOG.info("Adding new contact details.");
		personsContactDetailsPage.addContactDetailsWithRandomName(details);
		personsContactDetailsPage.selectContinueButton();

		LOG.info("Choosing user account type.");
		personUserTypePage.selectAuthorityMember();
		personUserTypePage.clickContinueButton();
		
		LOG.info("Sending new user an account invite.");
		personCreateAccountPage.clickInviteButton();
		
		userProfileConfirmationPage.clickSaveButton();
	}
	
	@Then("^the new Primary Authority contact is added Successfully$")
	public void the_new_Primary_Authority_contact_is_added_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact is added successfully.");
		assertTrue("Contact Details are not Displayed Correctly.", parPartnershipConfirmationPage.checkContactDetails());
	}

	@When("^the user updates the new Primary Authority contact with the following details:$")
	public void the_user_updates_the_new_Primary_Authority_contact_with_the_following_details(DataTable details) throws Throwable {
		
		parPartnershipConfirmationPage.editContactsDetailsButton();
		
		LOG.info("Editing contact details.");
		personsContactDetailsPage.editContactDetailsWithRandomName(details);
		personsContactDetailsPage.selectContinueButton();	
		
		LOG.info("Updating user account type.");
		personUserTypePage.selectAuthorityManager();
		personUserTypePage.clickContinueButton();	

		userProfileConfirmationPage.clickSaveButton();
	}
	
	@Then("^the new Primary Authority contact is updated Successfully$")
	public void the_new_Primary_Authority_contact_is_updated_Successfully() throws Throwable {
		LOG.info("Verifying the Authority contact was updated successfully.");
		assertTrue("Contact Details are not Displayed Correctly.", parPartnershipConfirmationPage.checkContactDetails());
	}

	@When("^the user removes the new Primary Authority contact$")
	public void the_user_removes_the_new_Primary_Authority_contact() throws Throwable {
		parPartnershipConfirmationPage.removeContactsDetailsButton();
		
		LOG.info("Removing the contact.");
		removeContactPage.clickRemoveButton();
	}

	@Then("^the new Primary Authority contact is removed Successfully$")
	public void the_new_Primary_Authority_contact_is_removed_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact was removed successfully.");
		assertTrue("Contact was not Removed.", parPartnershipConfirmationPage.checkContactExists());
	}
	
	@When("^the user adds a new Organisation contact to be Invited with the following details:$")
	public void the_user_adds_a_new_Organisation_contact_to_be_Invited_with_the_following_details(DataTable details) throws Throwable {
		parPartnershipConfirmationPage.addAnotherOrganisationContactButton();

		LOG.info("Adding new contact details.");
		personsContactDetailsPage.addContactDetailsWithRandomName(details);
		personsContactDetailsPage.selectContinueButton();
		
		LOG.info("Sending new user an account invite.");
		personCreateAccountPage.clickInviteButton();
		
		userProfileConfirmationPage.clickSaveButton();
	}

	@Then("^the new Organisation contact is added Successfully$")
	public void the_new_Organisation_contact_is_added_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact is added successfully.");
		assertTrue("Contact Details are not Displayed Correctly.", parPartnershipConfirmationPage.checkContactDetails());
	}

	@When("^the user updates the new Organisation contact with the following details:$")
	public void the_user_updates_the_new_Organisation_contact_with_the_following_details(DataTable details) throws Throwable {
		parPartnershipConfirmationPage.editContactsDetailsButton();
		
		LOG.info("Editing contact details.");
		personsContactDetailsPage.editContactDetailsWithRandomName(details);
		personsContactDetailsPage.selectContinueButton();	
		
		userProfileConfirmationPage.clickSaveButton();
	}

	@Then("^the new Organisation contact is updated Successfully$")
	public void the_new_Organisation_contact_is_updated_Successfully() throws Throwable {
		LOG.info("Verifying the Authority contact was updated successfully.");
		assertTrue("Contact Details are not Displayed Correctly.", parPartnershipConfirmationPage.checkContactDetails());
	}

	@When("^the user removes the new Organisation contact$")
	public void the_user_removes_the_new_Organisation_contact() throws Throwable {
		parPartnershipConfirmationPage.removeContactsDetailsButton();
		
		LOG.info("Removing the contact.");
		removeContactPage.clickRemoveButton();
	}

	@Then("^the new Organisation contact is removed Successfully$")
	public void the_new_Organisation_contact_is_removed_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact was removed successfully.");
		assertTrue("Contact was not Removed.", parPartnershipConfirmationPage.checkContactExists());
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
		LOG.info("Checking Advice notice status is set to \"Active\"");
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
		
		adviceRemovalPage.enterReasonForRemoval(reason);
	}

	@Then("^the advice notice is removed successfully$")
	public void the_advice_notice_is_removed_successfully() throws Throwable {
		LOG.info("Check Advice notice was Removed.");
		adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		
		Assert.assertTrue("Failed: Advice Notice was not Removed.", adviceNoticeSearchPage.checkNoResultsReturned());
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
		Assert.assertTrue(partnershipAdvancedSearchPage.checkPartnershipExists());
	}
	
	@When("^the user creates a new contact with the following details:$")
	public void the_user_creates_a_new_contact_with_the_following_details(DataTable details) throws Throwable {
		parDashboardPage.selectManageColleagues();
		managePeoplePage.selectAddPerson();

		LOG.info("Adding a new person.");
		personsContactDetailsPage.addContactDetails(details);
		personsContactDetailsPage.clickContinueButton();

		LOG.info("Successfully entered new contact details.");

		personAccountPage.selectUseExistingAccount();
		personAccountPage.clickContinueButton();

		LOG.info("Successfully chose to use the existing account.");

		personMembershipPage.selectCityEnforcementSquad();
		personMembershipPage.selectUpperWestSideBoroughCouncil();
		personMembershipPage.selectLowerEstSideBoroughCouncil();
		personMembershipPage.clickContinueButton();
		
		LOG.info("Successfully chose the contacts Authority memberships.");

		personUserTypePage.selectAuthorityMember();
		personUserTypePage.clickProfileReviewContinueButton();
		LOG.info("User Account Type: " + DataStore.getSavedValue(UsableValues.ACCOUNT_TYPE));

		userProfileConfirmationPage.saveChanges();
		userProfileCompletionPage.clickDoneGoToProfile();
	}
	
	@Then("^the user can verify the contact record was added to the user profile$")
	public void the_user_can_verify_the_contact_record_was_added_to_the_user_profile() throws Throwable {
		LOG.info("Verifying the Duplicate Contact Record was Added Successfully.");
		
		Assert.assertTrue(personsProfilePage.checkContactRecordAdded());
	}

	@When("^the user merges the contact record$")
	public void the_user_merges_the_contact_record() throws Throwable {
		LOG.info("Selecting Contact Records to Merge.");
		
		personsProfilePage.clickMergeContactRecords();
		mergeContactRecordsPage.mergeContacts();
		mergeContactRecordsPage.clickContinue();
		
		LOG.info("Confirming the Contact Records to be Merged.");
		mergeContactRecordsConfirmationPage.clickMerge();
	}

	@Then("^the user can verify the contact record was merged successfully$")
	public void the_user_can_verify_the_contact_record_was_merged_successfully() throws Throwable {
		LOG.info("Verifying the Contact Records have been Merged Successfully.");
		
		Assert.assertTrue(personsProfilePage.checkContactRecord());
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
		
		parPartnershipConfirmationPage.selectShowMembersListLink();
		memberListPage.selectAddAMemberLink();
		
		LOG.info("Entering the Member Organisation's Name.");
		addOrganisationNamePage.enterMemberOrganisationName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		LOG.info("Entering the Member Organisation's Address.");
		authorityAddressDetailsPage.enterMemberOrganisationAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		
		LOG.info("Entering the Member Organisation's Contact Details.");
		personsContactDetailsPage.enterContactDetails(details);
		personsContactDetailsPage.clickContinueButtonForMemberContact();
		
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
		Assert.assertTrue(memberListPage.checkMemberCreated());
	}
	
	@When("^the user updates a single member organisation of the patnership with the following details:$")
	public void the_user_updates_a_single_member_organisation_of_the_patnership_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Update a Single Member Organisation to a Co-ordinated Partnership.");
		
		partnershipAdvancedSearchPage.selectOrganisationLink();
		parPartnershipConfirmationPage.selectShowMembersListLink();
		
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
		
		editRegisteredAddressPage.editAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		
		editRegisteredAddressPage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Membership Start Date.");
		memberOrganisationSummaryPage.selectEditMembershipStartDate();
		enterTheDatePage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Contact Details.");
		memberOrganisationSummaryPage.selectEditPerson();
		personsContactDetailsPage.enterContactDetails(details);
		personsContactDetailsPage.goToMemberOrganisationSummaryPage();
		
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
		Assert.assertTrue(memberListPage.checkMemberCreated());
	}
	
	@When("^the user Ceases a single member organisation of the patnership with the current date$")
	public void the_user_Ceases_a_single_member_organisation_of_the_patnership_with_the_current_date() throws Throwable {
		LOG.info("Cease a Single Member Organisation to a Co-ordinated Partnership.");
		
		partnershipAdvancedSearchPage.selectOrganisationLink();
		parPartnershipConfirmationPage.selectShowMembersListLink();
		
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
		Assert.assertTrue("FAILED: Links are still present and/ or the Cease date is incorrect. ", memberListPage.checkMembershipCeased());
	}

	@When("^the user selects the Read more about Primary Authority link$")
	public void the_user_selects_the_Read_more_about_Primary_Authority_link() throws Throwable {
		LOG.info("Selecting the Read More About Primary Authority Link.");
		
		parHomePage.selectReadMoreAboutPrimaryAuthorityLink();
	}

	@Then("^the user is taken to the GOV\\.UK Guidance page for Local regulation Primary Authority Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Guidance_page_for_Local_regulation_Primary_Authority_Successfully() throws Throwable {
		LOG.info("Verifying the Local regulation: Primary Authority Page is Displayed.");
		
		Assert.assertTrue(localRegulationPrimaryAuthorityPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Access tools and templates for local authorities link$")
	public void the_user_selects_the_Access_tools_and_templates_for_local_authorities_link() throws Throwable {
		LOG.info("Selecting the Access Tools and Templates Link.");
		
		parHomePage.selectAccessToolsAndTemplatesLink();
	}

	@Then("^the user is taken to the GOV\\.UK Collection page for Primary Authority Documents Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Collection_page_for_Primary_Authority_Documents_Successfully() throws Throwable {
		LOG.info("Verifying the Primary Authority documents page is Displayed.");
		
		Assert.assertTrue(primaryAuthorityDocumentsPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Terms and Conditions link$")
	public void the_user_selects_the_Terms_and_Conditions_link() throws Throwable {
		LOG.info("Selecting the Terms and Conditions Link.");
		
		parHomePage.selectTermsAndConditionsLink();
	}

	@Then("^the user is taken to the GOV\\.UK Guidance page for Primary Authority terms and conditions Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Guidance_page_for_Primary_Authority_terms_and_conditions_Successfully() throws Throwable {
		LOG.info("Verifying the Primary Authority terms and conditions page is Displayed.");
		
		Assert.assertTrue(termsAndConditionsPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Cookies link$")
	public void the_user_selects_the_Cookies_link() throws Throwable {
		LOG.info("Selecting the Cookies Link from the Footer.");
		
		parHomePage.selectCookiesFooterLink();
	}

	@Then("^the user is taken to the Cookies page and can accept the Analytics Cookies Successfully$")
	public void the_user_is_taken_to_the_Cookies_page_and_can_accept_the_Analytics_Cookies_Successfully() throws Throwable {
		LOG.info("Verifying the Cookies page is Displayed and the User Accepts the Analytics Cookies.");
		
		Assert.assertTrue(cookiesPage.checkPageHeaderDisplayed());
		
		cookiesPage.acceptCookies();
		cookiesPage.selectSaveButton();
	}

	@When("^the user selects the Privacy link$")
	public void the_user_selects_the_Privacy_link() throws Throwable {
		LOG.info("Selecting the Privacy Link.");
		
		parHomePage.selectPrivacyLink();
	}

	@Then("^the user is taken to the GOV\\.UK Corporate report OPSS Privacy notice page Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Corporate_report_OPSS_Privacy_notice_page_Successfully() throws Throwable {
		LOG.info("Verifying the OPSS: privacy notice page is Displayed.");
		
		Assert.assertTrue(opssPrivacyNoticePage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Accessibility link$")
	public void the_user_selects_the_Accessibility_link() throws Throwable {
		LOG.info("Selecting the Accessibility Link.");
		
		parHomePage.selectAccessibilityLink();
	}

	@Then("^the user is taken to the GOV\\.UK Guidance page for the Primary Authority Register accessibility statement Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Guidance_page_for_the_Primary_Authority_Register_accessibility_statement_Successfully() throws Throwable {
		LOG.info("Verifying the Primary Authority Register: accessibility statement page is Displayed.");
		
		Assert.assertTrue(accessibilityStatementPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Open Government Licence link$")
	public void the_user_selects_the_Open_Government_Licence_link() throws Throwable {
		LOG.info("Selecting the Open Government Licence Link.");
		
		parHomePage.selectOpenGovernmentLicenceLink();
	}

	@Then("^the user is taken to the Open Government Licence for public sector information page Successfully$")
	public void the_user_is_taken_to_the_Open_Government_Licence_for_public_sector_information_page_Successfully() throws Throwable {
		LOG.info("Verifying the Open Government Licence page is Displayed.");
		
		Assert.assertTrue(openGovernmentLicencePage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Crown copyright link$")
	public void the_user_selects_the_Crown_copyright_link() throws Throwable {
		LOG.info("Selecting the Crown copyright Link.");
		
		parHomePage.selectCrownCopyrightLink();
	}

	@Then("^the user is taken to the Crown copyright page Successfully$")
	public void the_user_is_taken_to_the_Crown_copyright_page_Successfully() throws Throwable {
		LOG.info("Verifying the Crown copyright page is Displayed.");
		
		Assert.assertTrue(crownCopyrightPage.checkPageHeaderDisplayed());
	}
}
