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
import uk.gov.beis.pageobjects.AddPersonContactDetailsPage;
import uk.gov.beis.pageobjects.AuthorityAddressDetailsPage;
import uk.gov.beis.pageobjects.ChoosePersonMembershipPage;
import uk.gov.beis.pageobjects.AuthorityConfirmationPage;
import uk.gov.beis.pageobjects.AuthorityDashboardPage;
import uk.gov.beis.pageobjects.GivePersonAccountPage;
import uk.gov.beis.pageobjects.AuthorityNamePage;
import uk.gov.beis.pageobjects.AuthorityPage;
import uk.gov.beis.pageobjects.AuthorityTypePage;
import uk.gov.beis.pageobjects.PersonUserRoleTypeSelectionPage;
import uk.gov.beis.pageobjects.BusinessAddressDetailsPage;
import uk.gov.beis.pageobjects.BusinessConfirmationPage;
import uk.gov.beis.pageobjects.BusinessContactDetailsPage;
import uk.gov.beis.pageobjects.BusinessDetailsPage;
import uk.gov.beis.pageobjects.BusinessInvitePage;
import uk.gov.beis.pageobjects.BusinessPage;
import uk.gov.beis.pageobjects.DashboardPage;
import uk.gov.beis.pageobjects.DeclarationPage;
import uk.gov.beis.pageobjects.DeviationApprovalPage;
import uk.gov.beis.pageobjects.DeviationCompletionPage;
import uk.gov.beis.pageobjects.DeviationReviewPage;
import uk.gov.beis.pageobjects.DeviationSearchPage;
import uk.gov.beis.pageobjects.EmployeesPage;
import uk.gov.beis.pageobjects.EnforcementActionPage;
import uk.gov.beis.pageobjects.EnforcementCompletionPage;
import uk.gov.beis.pageobjects.EnforcementContactDetailsPage;
import uk.gov.beis.pageobjects.EnforcementDetailsPage;
import uk.gov.beis.pageobjects.EnforcementLegalEntityPage;
import uk.gov.beis.pageobjects.EnforcementNotificationActionReceivedPage;
import uk.gov.beis.pageobjects.EnforcementNotificationPage;
import uk.gov.beis.pageobjects.EnforcementReviewPage;
import uk.gov.beis.pageobjects.EnforcementSearchPage;
import uk.gov.beis.pageobjects.EnquiriesSearchPage;
import uk.gov.beis.pageobjects.EnquiryCompletionPage;
import uk.gov.beis.pageobjects.EnquiryContactDetailsPage;
import uk.gov.beis.pageobjects.EnquiryReviewPage;
import uk.gov.beis.pageobjects.GeneralEnquiriesPage;
import uk.gov.beis.pageobjects.HomePage;
import uk.gov.beis.pageobjects.InspectionContactDetailsPage;
import uk.gov.beis.pageobjects.InspectionFeedbackCompletionPage;
import uk.gov.beis.pageobjects.InspectionFeedbackConfirmationPage;
import uk.gov.beis.pageobjects.InspectionFeedbackDetailsPage;
import uk.gov.beis.pageobjects.InspectionFeedbackSearchPage;
import uk.gov.beis.pageobjects.InspectionPlanDetailsPage;
import uk.gov.beis.pageobjects.InspectionPlanExpirationPage;
import uk.gov.beis.pageobjects.InspectionPlanSearchPage;
import uk.gov.beis.pageobjects.LegalEntityPage;
import uk.gov.beis.pageobjects.LoginPage;
import uk.gov.beis.pageobjects.MailLogPage;
import uk.gov.beis.pageobjects.ManagePeoplePage;
import uk.gov.beis.pageobjects.MemberListPage;
import uk.gov.beis.pageobjects.PersonCompletionConfirmationPage;
import uk.gov.beis.pageobjects.PersonsProfilePage;
import uk.gov.beis.pageobjects.NewsLetterManageSubscriptionListPage;
import uk.gov.beis.pageobjects.NewsLetterSubscriptionPage;
import uk.gov.beis.pageobjects.NewsLetterSubscriptionReviewChangesPage;
import uk.gov.beis.pageobjects.ONSCodePage;
import uk.gov.beis.pageobjects.OrganisationDashboardPage;
import uk.gov.beis.pageobjects.PartnershipAdvancedSearchPage;
import uk.gov.beis.pageobjects.PartnershipApprovalPage;
import uk.gov.beis.pageobjects.PartnershipCompletionPage;
import uk.gov.beis.pageobjects.PartnershipConfirmationPage;
import uk.gov.beis.pageobjects.PartnershipDescriptionPage;
import uk.gov.beis.pageobjects.PartnershipRestoredPage;
import uk.gov.beis.pageobjects.PartnershipRevokedPage;
import uk.gov.beis.pageobjects.PartnershipSearchPage;
import uk.gov.beis.pageobjects.PartnershipTermsPage;
import uk.gov.beis.pageobjects.PartnershipTypePage;
import uk.gov.beis.pageobjects.PasswordPage;
import uk.gov.beis.pageobjects.ProfileReviewPage;
import uk.gov.beis.pageobjects.ProposedEnforcementPage;
import uk.gov.beis.pageobjects.RegulatoryFunctionPage;
import uk.gov.beis.pageobjects.RemoveEnforcementConfirmationPage;
import uk.gov.beis.pageobjects.RemoveEnforcementPage;
import uk.gov.beis.pageobjects.ReplyDeviationRequestPage;
import uk.gov.beis.pageobjects.ReplyEnquiryPage;
import uk.gov.beis.pageobjects.ReplyInspectionFeedbackPage;
import uk.gov.beis.pageobjects.RequestDeviationPage;
import uk.gov.beis.pageobjects.RequestEnquiryPage;
import uk.gov.beis.pageobjects.RestorePartnershipConfirmationPage;
import uk.gov.beis.pageobjects.RevokePartnershipConfirmationPage;
import uk.gov.beis.pageobjects.SICCodePage;
import uk.gov.beis.pageobjects.TradingPage;
import uk.gov.beis.pageobjects.UpdateUserCommunicationPreferencesPage;
import uk.gov.beis.pageobjects.UpdateUserConfirmationPage;
import uk.gov.beis.pageobjects.UpdateUserContactDetailsPage;
import uk.gov.beis.pageobjects.UpdateUserSubscriptionsPage;
import uk.gov.beis.pageobjects.UploadInspectionPlanPage;
import uk.gov.beis.pageobjects.UserCommsPreferencesPage;
import uk.gov.beis.pageobjects.InvitePersonToCreateAccountPage;
import uk.gov.beis.pageobjects.UserNotificationPreferencesPage;
import uk.gov.beis.pageobjects.UserProfileCompletionPage;
import uk.gov.beis.pageobjects.UserProfileConfirmationPage;
import uk.gov.beis.pageobjects.UserProfilePage;
import uk.gov.beis.pageobjects.UserSubscriptionPage;
import uk.gov.beis.pageobjects.UserTermsPage;
import uk.gov.beis.pageobjects.ViewEnquiryPage;
import uk.gov.beis.utility.DataStore;
import uk.gov.beis.utility.RandomStringGenerator;

public class PARStepDefs {

	public static WebDriver driver;
	private HomePage parHomePage;
	private RequestEnquiryPage requestEnquiryPage;
	private DeviationSearchPage deviationSearchPage;
	private EnquiryReviewPage enquiryReviewPage;
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
	private InspectionFeedbackSearchPage inspectionFeedbackSearchPage;
	private ProposedEnforcementPage proposedEnforcementPage;
	private EnforcementReviewPage enforcementReviewPage;
	private RegulatoryFunctionPage regulatoryFunctionPage;
	private AuthorityConfirmationPage authorityConfirmationPage;
	private AuthorityAddressDetailsPage authorityAddressDetailsPage;
	private AuthorityTypePage authorityTypePage;
	private AuthorityNamePage authorityNamePage;
	private ReplyInspectionFeedbackPage replyInspectionFeedbackPage;
	private PartnershipAdvancedSearchPage partnershipAdvancedSearchPage;
	private UserProfileConfirmationPage userProfileConfirmationPage;
	private UserNotificationPreferencesPage userNotificationPreferencesPage;
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
	private RevokePartnershipConfirmationPage revokePartnershipConfirmationPage;
	private PartnershipRevokedPage partnershipRevokedPage;
	private UserTermsPage userTermsPage;
	private LegalEntityPage legalEntityPage;
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
	private EnforcementNotificationActionReceivedPage enforcementNotificationActionReceivedPage;
	private GeneralEnquiriesPage generalEnquiriesPage;
	private ViewEnquiryPage viewEnquiryPage;

	// PAR News Letter
	private UserProfilePage userProfilePage;
	private UpdateUserCommunicationPreferencesPage updateUserCommunicationPreferencesPage;
	private UpdateUserConfirmationPage updateUserConfirmationPage;
	private UpdateUserContactDetailsPage updateUserContactDetailsPage;
	private UpdateUserSubscriptionsPage updateUserSubscriptionsPage;
	private NewsLetterSubscriptionPage newsLetterSubscriptionPage;
	private NewsLetterManageSubscriptionListPage newsLetterManageSubscriptionListPage;
	private NewsLetterSubscriptionReviewChangesPage newsLetterSubscriptionReviewPage;

	// Person Creation and Update
	private ManagePeoplePage managePeoplePage;
	private AddPersonContactDetailsPage addPersonsContactDetailsPage;
	private GivePersonAccountPage givePersonAccountPage;
	private ChoosePersonMembershipPage choosePersonMembershipPage;
	private PersonUserRoleTypeSelectionPage personUserTypeSelectionPage;
	private InvitePersonToCreateAccountPage invitePersonToCreateAccountPage;
	private ProfileReviewPage profileReviewPage;
	private PersonCompletionConfirmationPage personCompletionConfirmationPage;
	private PersonsProfilePage personsProfilePage;

	private DeviationReviewPage deviationReviewPage;
	private DeviationApprovalPage deviationApprovalPage;
	private EnquiriesSearchPage enquiriesSearchPage;
	private ReplyDeviationRequestPage replyDeviationRequestPage;
	private ReplyEnquiryPage replyEnquiryPage;

	public PARStepDefs() throws ClassNotFoundException, IOException {
		driver = ScenarioContext.lastDriver;
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
		userNotificationPreferencesPage = PageFactory.initElements(driver, UserNotificationPreferencesPage.class);
		userTermsPage = PageFactory.initElements(driver, UserTermsPage.class);
		userProfileCompletionPage = PageFactory.initElements(driver, UserProfileCompletionPage.class);
		passwordPage = PageFactory.initElements(driver, PasswordPage.class);
		mailLogPage = PageFactory.initElements(driver, MailLogPage.class);
		memberListPage = PageFactory.initElements(driver, MemberListPage.class);
		legalEntityPage = PageFactory.initElements(driver, LegalEntityPage.class);
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
		enforcementNotificationActionReceivedPage = PageFactory.initElements(driver,
				EnforcementNotificationActionReceivedPage.class);
		generalEnquiriesPage = PageFactory.initElements(driver, GeneralEnquiriesPage.class);
		viewEnquiryPage = PageFactory.initElements(driver, ViewEnquiryPage.class);

		// PAR News Letter
		userProfilePage = PageFactory.initElements(driver, UserProfilePage.class);
		updateUserCommunicationPreferencesPage = PageFactory.initElements(driver,
				UpdateUserCommunicationPreferencesPage.class);
		updateUserConfirmationPage = PageFactory.initElements(driver, UpdateUserConfirmationPage.class);
		updateUserContactDetailsPage = PageFactory.initElements(driver, UpdateUserContactDetailsPage.class);
		updateUserSubscriptionsPage = PageFactory.initElements(driver, UpdateUserSubscriptionsPage.class);
		newsLetterSubscriptionPage = PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
		newsLetterManageSubscriptionListPage = PageFactory.initElements(driver,
				NewsLetterManageSubscriptionListPage.class);
		newsLetterSubscriptionReviewPage = PageFactory.initElements(driver,
				NewsLetterSubscriptionReviewChangesPage.class);

		// Person Creation and Update
		managePeoplePage = PageFactory.initElements(driver, ManagePeoplePage.class);
		addPersonsContactDetailsPage = PageFactory.initElements(driver, AddPersonContactDetailsPage.class);
		choosePersonMembershipPage = PageFactory.initElements(driver, ChoosePersonMembershipPage.class);
		givePersonAccountPage = PageFactory.initElements(driver, GivePersonAccountPage.class);
		personUserTypeSelectionPage = PageFactory.initElements(driver, PersonUserRoleTypeSelectionPage.class);
		invitePersonToCreateAccountPage = PageFactory.initElements(driver, InvitePersonToCreateAccountPage.class);
		profileReviewPage = PageFactory.initElements(driver, ProfileReviewPage.class);
		personCompletionConfirmationPage = PageFactory.initElements(driver, PersonCompletionConfirmationPage.class);
		personsProfilePage = PageFactory.initElements(driver, PersonsProfilePage.class);
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
	public void the_user_creates_a_new_partnership_application_with_the_following_details(String type,
			DataTable details) throws Throwable {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			LOG.info("Select apply new partnership");

			parDashboardPage.selectApplyForNewPartnership();
			LOG.info("Choose authority");
			DataStore.saveValue(UsableValues.AUTHORITY_NAME, data.get("Authority"));
			parAuthorityPage.selectAuthority(data.get("Authority"));
			LOG.info("Select partnership type");
			DataStore.saveValue(UsableValues.PARTNERSHIP_TYPE, type);
			parPartnershipTypePage.selectPartnershipType(type);
			LOG.info("Accepting terms");
			parPartnershipTermsPage.acceptTerms();
			DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, data.get("Partnership Info"));
			LOG.info("Entering partnership description");
			parPartnershipDescriptionPage.enterPartnershipDescription(data.get("Partnership Info"),
					ScenarioContext.secondJourneyPart);
			LOG.info("Entering business/organisation name");
			DataStore.saveValue(UsableValues.BUSINESS_NAME, RandomStringGenerator.getBusinessName(4));
			parBusinessPage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
			LOG.info("Enter address details");
			parBusinessAddressDetailsPage.enterAddressDetails(data.get("addressline1"), data.get("town"),
					data.get("postcode"));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE1, data.get("addressline1"));
			DataStore.saveValue(UsableValues.BUSINESS_TOWN, data.get("town"));
			DataStore.saveValue(UsableValues.BUSINESS_POSTCODE, data.get("postcode"));

			DataStore.saveValue(UsableValues.BUSINESS_EMAIL, RandomStringGenerator.getEmail(4));
			LOG.info("Enter contact details");
			parBusinessContactDetailsPage.enterContactDetails(data.get("firstname"), data.get("lastname"),
					data.get("phone"), DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
			DataStore.saveValue(UsableValues.BUSINESS_FIRSTNAME, data.get("firstname"));
			DataStore.saveValue(UsableValues.BUSINESS_LASTNAME, data.get("lastname"));
			DataStore.saveValue(UsableValues.BUSINESS_PHONE, data.get("phone"));
			LOG.info("Send invitation to user");
			parBusinessInvitePage.sendInvite();
		}
	}

	@Then("^the first part of the partnership application is successfully completed$")
	public void the_first_part_of_the_partnership_application_is_successfully_completed() throws Throwable {
		LOG.info("Confirm/check partnership details");
		parPartnershipConfirmationPage.confirmDetails();
		Assert.assertTrue("Partnership info missing", parPartnershipConfirmationPage.checkPartnershipInfo());
		Assert.assertTrue("Partnership appliction information not correct",
				parPartnershipConfirmationPage.checkPartnershipApplication());
		LOG.info("Saving changes");
		parPartnershipConfirmationPage.saveChanges();
		parPartnershipCompletionPage.completeApplication();
	}

	@When("^the user searches for the last created partnership$")
	public void the_user_searches_for_the_last_created_partnership() throws Throwable {
		parDashboardPage.checkAndAcceptCookies();

		String user = DataStore.getSavedValue(UsableValues.LOGIN_USER);
		switch (user) {
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
	public void the_user_completes_the_partnership_application_with_the_following_details(DataTable details)
			throws Throwable {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			LOG.info("Accepting terms");
			parDeclarationPage.acceptTerms();
			LOG.info("Add business description");
			DataStore.saveValue(UsableValues.BUSINESS_DESC, data.get("Business Description"));
			parBusinessDetailsPage.enterBusinessDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
			LOG.info("Confirming address details");
			parBusinessAddressDetailsPage.proceed();
			LOG.info("Confirming contact details");
			parBusinessContactDetailsPage.proceed();
			LOG.info("Selecting SIC Code");
			DataStore.saveValue(UsableValues.SIC_CODE, data.get("SIC Code"));
			sicCodePage.selectSICCode(data.get("SIC Code"));

			String partnershiptype = DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase();
			switch (partnershiptype) {

			case ("direct"):
				LOG.info("Selecting No of Employees");
				DataStore.saveValue(UsableValues.NO_EMPLOYEES, data.get("No of Employees"));
				employeesPage.selectNoEmployees(data.get("No of Employees"));
				break;

			case ("co-ordinated"):
				LOG.info("Selecting Membership List size");
				DataStore.saveValue(UsableValues.MEMBERLIST_SIZE, data.get("Member List Size"));
				memberListPage.selectMemberSize(data.get("Member List Size"));
				break;
			}

			LOG.info("Entering business trading name");
			DataStore.saveValue(UsableValues.TRADING_NAME,
					DataStore.getSavedValue(UsableValues.BUSINESS_NAME).replace("Business", "trading name"));
			tradingPage.enterTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
			DataStore.saveValue(UsableValues.ENTITY_TYPE, data.get("Legal entity Type"));
			legalEntityPage.createLegalEntity(data.get("Legal entity Type"));
			LOG.info("Set second part of journey part to true");
			ScenarioContext.secondJourneyPart = true;
		}
	}

	@Then("^the second part of the partnership application is successfully completed$")
	public void the_second_part_of_the_partnership_application_is_successfully_completed() throws Throwable {
		LOG.info("Check and confirm changes");

		String partnershiptype = DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase();
		switch (partnershiptype) {

		case ("direct"):
			LOG.info("Check employee size");
			parPartnershipConfirmationPage.checkNoEmployees();
			break;

		case ("co-ordinated"):
			LOG.info("Check membership size");
			parPartnershipConfirmationPage.checkMemberSize();
			break;
		}

		Assert.assertTrue("Appliction not complete",
				parPartnershipConfirmationPage.checkPartnershipApplicationSecondPart());
		parPartnershipConfirmationPage.confirmDetails();
		parPartnershipConfirmationPage.saveChanges();
		parPartnershipCompletionPage.completeApplication();
	}

	@When("^the user visits the maillog page and extracts the invite link$")
	public void the_user_visits_the_maillog_page_and_extracts_the_invite_link() throws Throwable {
		mailLogPage.navigateToUrl();
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
//		userNotificationPreferencesPage.selectContinue();
	}

	@Then("^the user journey creation is successful$")
	public void the_user_journey_creation_is_successful() throws Throwable {
		LOG.info("Checking user creation is sucessful");
//		userProfileConfirmationPage.checkUserCreation();
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
		parPartnershipDescriptionPage.enterPartnershipDescription(desc, ScenarioContext.secondJourneyPart);
	}

	@Then("^the partnership is updated correctly$")
	public void the_partnership_is_updated_correctly() throws Throwable {
		Assert.assertTrue("Partnership info doesn't check out", parPartnershipConfirmationPage.checkPartnershipInfo());
	}

	@When("^the user creates a new authority with the following details:$")
	public void the_user_creates_a_new_authority_with_the_following_details(DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			LOG.info("Select manage authorities");
			parDashboardPage.selectManageAuthorities();
			LOG.info("Select add authority");
			authoritiesDashboardPage.selectAddAuthority();
			DataStore.saveValue(UsableValues.AUTHORITY_NAME, RandomStringGenerator.getAuthorityName(3));
			LOG.info("Provide authority name");
			authorityNamePage.enterAuthorityName(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
			LOG.info("Provide authority type");
			DataStore.saveValue(UsableValues.AUTHORITY_TYPE, data.get("Authority Type"));
			authorityTypePage.selectAuthorityType(DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE));
			LOG.info("Enter authority contact details");
			authorityAddressDetailsPage.enterAddressDetails(data.get("addressline1"), data.get("town"),
					data.get("postcode"));
			DataStore.saveValue(UsableValues.AUTHORITY_ADDRESSLINE1, data.get("addressline1"));
			DataStore.saveValue(UsableValues.AUTHORITY_TOWN, data.get("town"));
			DataStore.saveValue(UsableValues.AUTHORITY_POSTCODE, data.get("postcode"));
			LOG.info("Provide ONS code");
			DataStore.saveValue(UsableValues.ONS_CODE, data.get("ONS Code"));
			onsCodePage.enterONSCode(DataStore.getSavedValue(UsableValues.ONS_CODE));
			LOG.info("Select regulatory function");
			DataStore.saveValue(UsableValues.AUTHORITY_REGFUNCTION, data.get("Regulatory Function"));
			regulatoryFunctionPage.selectRegFunction(DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION));
		}
	}

	@Then("^the authority is created sucessfully$")
	public void the_authority_is_created_sucessfully() throws Throwable {
		LOG.info("Confirm all details entered check out and save changes");
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
		DataStore.saveValue(UsableValues.AUTHORITY_NAME,
				DataStore.getSavedValue(UsableValues.AUTHORITY_NAME) + " Updated");
		authorityNamePage.enterAuthorityName(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		authorityConfirmationPage.editAuthorityType();
		DataStore.saveValue(UsableValues.AUTHORITY_TYPE, "District");
		authorityTypePage.selectAuthorityType(DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE));
		authorityConfirmationPage.editONSCode();
		DataStore.saveValue(UsableValues.ONS_CODE, DataStore.getSavedValue(UsableValues.ONS_CODE) + " Updated");
		onsCodePage.enterONSCode(DataStore.getSavedValue(UsableValues.ONS_CODE));
		authorityConfirmationPage.editRegFunction();
		DataStore.saveValue(UsableValues.AUTHORITY_REGFUNCTION, "Alphabet learning");
		regulatoryFunctionPage.selectRegFunction(DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION));

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
		DataStore.saveValue(UsableValues.BUSINESS_NAME,
				DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + " Updated");
		parBusinessPage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		businessConfirmationPage.editOrganisationDesc();
		DataStore.saveValue(UsableValues.BUSINESS_DESC,
				DataStore.getSavedValue(UsableValues.BUSINESS_DESC) + " Updated");
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
	public void the_user_creates_an_enforcement_notice_against_the_partnership_with_the_following_details(
			DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			LOG.info("Create enformcement notification against partnership");
			partnershipSearchPage.selectBusinessNameLinkFromPartnership();
			parPartnershipConfirmationPage.createEnforcement();
			enforcementNotificationPage.proceed();
			enforcementContactDetailsPage.proceed();
			enforcementLegalEntityPage.selectLegalEntity(DataStore.getSavedValue(UsableValues.ENTITY_NAME));
			enforcementLegalEntityPage.proceed();
			DataStore.saveValue(UsableValues.ENFORCEMENT_TYPE, data.get("Enforcement Action"));
			enforcementDetailsPage.selectEnforcementType(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TYPE));
			enforcementDetailsPage.enterEnforcementDescription("Enformcement description");
			enforcementDetailsPage.proceed();
			DataStore.saveValue(UsableValues.ENFORCEMENT_REGFUNC, data.get("Regulatory Function"));
			enforcementActionPage.selectRegFunc(DataStore.getSavedValue(UsableValues.ENFORCEMENT_REGFUNC));
			DataStore.saveValue(UsableValues.ENFORCEMENT_FILENAME, data.get("Attachment"));
			enforcementActionPage.chooseFile(DataStore.getSavedValue(UsableValues.ENFORCEMENT_FILENAME));
			DataStore.saveValue(UsableValues.ENFORCEMENT_DESCRIPTION, data.get("Description"));
			enforcementActionPage.enterEnforcementDescription(
					DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION).toLowerCase());
			DataStore.saveValue(UsableValues.ENFORCEMENT_TITLE, data.get("Title"));
			enforcementActionPage.enterTitle(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE));
			enforcementActionPage.proceed();

		}
	}

	@Then("^all the fields for the enforcement notice are updated correctly$")
	public void all_the_fields_for_the_enforcement_are_updated_correctly() throws Throwable {
		LOG.info("Check all updated changes check out");
		Assert.assertTrue("Details don't check out", enforcementReviewPage.checkEnforcementCreation());
		enforcementReviewPage.saveChanges();
	}

	@When("^the user selects the last created enforcement notice$")
	public void the_user_selects_the_last_created_enforcement() throws Throwable {
		String user = DataStore.getSavedValue(UsableValues.LOGIN_USER);
		switch (user) {
		case ("par_enforcement_officer@example.com"):
			LOG.info("Select last created enforcement");
			parDashboardPage.selectSeeEnforcementNotices();
			enforcementSearchPage.searchPartnerships();
			enforcementSearchPage.selectEnforcement();
			break;

		case ("par_authority@example.com"):
			LOG.info("Select last created enforcement");
			parDashboardPage.selectSeeEnforcementNotices();
			enforcementSearchPage.searchPartnerships();
			enforcementSearchPage.selectEnforcement();
			break;

		case ("par_helpdesk@example.com"):
			LOG.info("Searching for an Enforcement Notice.");
			parDashboardPage.selectManageEnforcementNotices();
			enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
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
		enforcementSearchPage.searchPartnerships();
		Assert.assertTrue("Enforcement Status doesn't check out",
				enforcementSearchPage.getStatus().equalsIgnoreCase("Approved"));
	}

	@When("^the user searches for the last created enforcement notice$")
	public void the_user_searches_for_the_last_created_enforcement_notice() throws Throwable {
		LOG.info("Select last created enforcement");
		parDashboardPage.selectManageEnforcementNotices();
		enforcementSearchPage.searchPartnerships();
		enforcementSearchPage.removeEnforcement();
	}

	@Then("^the user removes the enforcement notice successfully$")
	public void the_user_removes_the_enforcement_notice_successfully() throws Throwable {
		LOG.info("Check enforcement notice is removed");
		removeEnforcementPage.selectRevokeReason("This is a duplicate enforcement");
		removeEnforcementPage.enterRevokeDescription("Revoking");
		removeEnforcementConfirmationPage.acceptTerms();
		enforcementSearchPage.searchPartnerships();
		Assert.assertTrue("Some results are returned indeed", enforcementSearchPage.confirmNoReturnedResults());
	}

	@When("^the user uploads an inspection plan against the partnership with the following details:$")
	public void the_user_uploads_an_inspection_plan_against_the_partnership_with_the_following_details(DataTable dets)
			throws Throwable {
		LOG.info("Upload inspection plan and save details");
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			partnershipAdvancedSearchPage.selectPartnershipLink();
			parPartnershipConfirmationPage.selectSeeAllInspectionPlans();
			inspectionPlanSearchPage.selectUploadLink();
			uploadInspectionPlanPage.chooseFile("link.txt");
			uploadInspectionPlanPage.uploadFile();
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_TITLE, data.get("Title"));
			inspectionPlanDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE));
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_DESCRIPTION, data.get("Description"));
			inspectionPlanDetailsPage
					.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
			inspectionPlanDetailsPage.save();
			inspectionPlanExpirationPage.enterDate("ddMMYYYY");
			inspectionPlanExpirationPage.save();
			LOG.info("Check inspection plan status is set to \"Current\"");
			Assert.assertTrue("Failed: Status not set to \"Current\"",
					inspectionPlanSearchPage.getPlanStatus().equalsIgnoreCase("Current"));
		}
	}

	@When("^the user submits an inspection feedback against the inspection plan with the following details:$")
	public void the_user_submits_an_inspection_feedback_against_the_inspection_plan_with_the_following_details(
			DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			LOG.info("Submit inspection feedback against partnership");
			partnershipSearchPage.selectBusinessNameLinkFromPartnership();
			parPartnershipConfirmationPage.selectSendInspectionFeedbk();
			inspectionContactDetailsPage.proceed();
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION, data.get("Description"));
			inspectionFeedbackDetailsPage
					.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION));
			inspectionFeedbackDetailsPage.chooseFile("link.txt");
			inspectionFeedbackDetailsPage.proceed();
			inspectionFeedbackConfirmationPage.saveChanges();
			inspectionFeedbackCompletionPage.complete();
		}
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
		Assert.assertTrue("Failed: Inspection feedback description doesn't check out ",
				inspectionFeedbackConfirmationPage.checkInspectionFeedback());
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
	public void the_user_submits_a_response_to_the_inspection_feedback_with_the_following_details(DataTable dets)
			throws Throwable {
		LOG.info("Submit response to inspection feedback request");
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1, data.get("Description"));
			inspectionFeedbackConfirmationPage.submitResponse();
			replyInspectionFeedbackPage
					.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1));
			replyInspectionFeedbackPage.chooseFile("link.txt");
			replyInspectionFeedbackPage.proceed();
			LOG.info("Verify the inspection feedback response");
			Assert.assertTrue("Failed: Inspection feedback response doesn't check out ",
					inspectionFeedbackConfirmationPage.checkInspectionResponse());
		}
	}

	@When("^the user sends a reply to the inspection feedback message with the following details:$")
	public void the_user_sends_a_reply_to_the_inspection_feedback_message_with_the_following_details(DataTable dets)
			throws Throwable {
		LOG.info("Submit reply to inspection feedback response");
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE2, data.get("Description"));
			inspectionFeedbackConfirmationPage.submitResponse();
			replyInspectionFeedbackPage
					.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE2));
			replyInspectionFeedbackPage.chooseFile("link.txt");
			replyInspectionFeedbackPage.proceed();
		}
	}

	@Then("^the inspection feedback_reply is received successfully$")
	public void the_inspection_feedback_reply_is_received_successfully() throws Throwable {
		LOG.info("Verify the inspection feedback reply");
		Assert.assertTrue("Failed: Inspection feedback reply doesn't check out ",
				inspectionFeedbackConfirmationPage.checkInspectionReply());
	}

	@When("^the user submits a deviation request against an inspection plan with the following details:$")
	public void the_user_submits_a_deviation_request_against_an_inspection_plan_with_the_following_details(
			DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			LOG.info("Submit deviation request");
			partnershipSearchPage.selectBusinessNameLinkFromPartnership();
			parPartnershipConfirmationPage.selectDeviateInspectionPlan();
			enforcementContactDetailsPage.save();
			DataStore.saveValue(UsableValues.DEVIATION_DESCRIPTION, data.get("Description"));
			requestDeviationPage.enterDescription(DataStore.getSavedValue(UsableValues.DEVIATION_DESCRIPTION));
			requestDeviationPage.chooseFile("link.txt");
			requestDeviationPage.proceed();
			LOG.info("Verify the deviation request is created");
			Assert.assertTrue("Failed: Deviation request details don't check out ",
					deviationReviewPage.checkDeviationCreation());
			deviationReviewPage.saveChanges();
			deviationCompletionPage.complete();
		}
	}

	@When("^the user searches for the last created deviation request$")
	public void the_user_searches_for_the_last_created_deviation_request() throws Throwable {
		LOG.info("Search for last created deviation request");
		parDashboardPage.selectSeeDeviationRequests();
		deviationSearchPage.selectDeviationRequest();
	}

	@Then("^the user successfully approves the deviation request$")
	public void the_user_successfully_approves_the_deviation_request() throws Throwable {
		LOG.info("Approve the deviation request");
		deviationApprovalPage.selectAllow();
		deviationApprovalPage.proceed();
		Assert.assertTrue("Failed: Deviation request status not correct", deviationReviewPage.checkDeviationStatus());
		deviationReviewPage.saveChanges();
		deviationCompletionPage.complete();
	}

	@Given("^the user submits a response to the deviation request with the following details:$")
	public void the_user_submits_a_response_to_the_deviation_request_with_the_following_details(DataTable dets)
			throws Throwable {
		LOG.info("Submit response to the deviation request");
		deviationSearchPage.selectDeviationRequest();
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1, data.get("Description"));
			deviationReviewPage.submitResponse();
			replyDeviationRequestPage
					.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1));
			replyDeviationRequestPage.chooseFile("link.txt");
			replyDeviationRequestPage.proceed();
			LOG.info("Verify the deviation response");
			Assert.assertTrue("Failed: Deviation response doesn't check out ",
					deviationReviewPage.checkDeviationResponse());
		}
	}

	@When("^the user sends a reply to the deviation request message with the following details:$")
	public void the_user_sends_a_reply_to_the_deviation_request_message_with_the_following_details(DataTable dets)
			throws Throwable {
		LOG.info("Submit reply to the deviation request");
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE2, data.get("Description"));
			deviationReviewPage.submitResponse();
			replyDeviationRequestPage
					.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE2));
			replyDeviationRequestPage.chooseFile("link.txt");
			replyDeviationRequestPage.proceed();
			LOG.info("Verify the deviation response");
			Assert.assertTrue("Failed: Deviation reply doesn't check out ",
					deviationReviewPage.checkDeviationResponse());
		}
	}

	@Then("^the deviation reply received successfully$")
	public void the_deviation_reply_received_successfully() throws Throwable {
		LOG.info("Verify the deviation response");
		Assert.assertTrue("Failed: Deviation reply doesn't check out ", deviationReviewPage.checkDeviationResponse());
	}

	@When("^the user submits a general enquiry with the following details:$")
	public void the_user_submits_a_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			LOG.info("Send general query");
			partnershipSearchPage.selectBusinessNameLinkFromPartnership();
			parPartnershipConfirmationPage.sendGeneralEnquiry();
			enquiryContactDetailsPage.proceed();
			DataStore.saveValue(UsableValues.ENQUIRY_DESCRIPTION, data.get("Description"));
			requestEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION));
			requestEnquiryPage.chooseFile("link.txt");
			requestEnquiryPage.proceed();
			LOG.info("Verify the enquiry is created");
			Assert.assertTrue("Failed: Enquiry details don't check out ", enquiryReviewPage.checkEnquiryCreation());
			enquiryReviewPage.saveChanges();
			enquiryCompletionPage.complete();
		}
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
	public void the_user_submits_a_response_to_the_general_enquiry_with_the_following_details(DataTable dets)
			throws Throwable {
		LOG.info("Submit reply to the enquiry");
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENQUIRY_REPLY, data.get("Description"));
			enquiryReviewPage.submitResponse();
			replyEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY));
			replyEnquiryPage.chooseFile("link.txt");
			replyEnquiryPage.proceed();
			LOG.info("Verify the reply message");
			Assert.assertTrue("Failed: Enquiry reply doesn't check out ", enquiryReviewPage.checkEnquiryReply());

		}
	}

	@When("^the user sends a reply to the general enquiry with the following details:$")
	public void the_user_sends_a_reply_to_the_general_enquiry_with_the_following_details(DataTable dets)
			throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENQUIRY_REPLY1, data.get("Description"));
			enquiryReviewPage.submitResponse();
			replyEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY1));
			replyEnquiryPage.chooseFile("link.txt");
			replyEnquiryPage.proceed();
			LOG.info("Verify the reply message");
			Assert.assertTrue("Failed: Enquiry reply doesn't check out ", enquiryReviewPage.checkEnquiryReply1());

		}
	}

	@When("^the user adds a new person to the contacts successfully with the following details:$")
	public void the_user_adds_a_new_person_to_the_contacts_successfully_with_the_following_details(DataTable newPerson)
			throws Throwable {
		parDashboardPage.selectManageColleagues();
		managePeoplePage.selectAddPerson();

		LOG.info("Adding a new person.");
		for (Map<String, String> data : newPerson.asMaps(String.class, String.class)) {

			addPersonsContactDetailsPage.enterTitle(data.get("Title"));
			addPersonsContactDetailsPage.enterFirstname(data.get("Firstname"));
			addPersonsContactDetailsPage.enterLastname(data.get("Lastname"));
			addPersonsContactDetailsPage.enterWorkPhoneNumber(data.get("WorkNumber"));
			addPersonsContactDetailsPage.enterMobilePhoneNumber(data.get("MobileNumber"));
			addPersonsContactDetailsPage.enterEmailAddress(data.get("Email"));

			DataStore.saveValue(UsableValues.PERSON_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.PERSON_FIRSTNAME, data.get("Firstname"));
			DataStore.saveValue(UsableValues.PERSON_LASTNAME, data.get("Lastname"));
		}

		addPersonsContactDetailsPage.clickContinueButton();

		givePersonAccountPage.selectExistingAccount();
		givePersonAccountPage.clickContinueButton();

		// choosePersonMembershipPage.selectTestBusiness();
		choosePersonMembershipPage.selectABCDMart();
		choosePersonMembershipPage.selectDemolitionExperts();
		choosePersonMembershipPage.selectPartnershipConfirmedByAuthority();

		choosePersonMembershipPage.selectCityEnforcementSquad();
		choosePersonMembershipPage.selectUpperWestSideBoroughCouncil();
		// choosePersonMembershipPage.selectLowerEstSideBoroughCouncil();
		choosePersonMembershipPage.clickContinueButton();

		personUserTypeSelectionPage.selectEnforcementOfficer();
		personUserTypeSelectionPage.clickProfileReviewContinueButton();

		profileReviewPage.savePersonCreation();
		personCompletionConfirmationPage.clickDoneButton();
		personsProfilePage.clickDoneButton();

		managePeoplePage.clickDashboadButton();
		LOG.info("Successfully added a new person.");
	}

	@Then("^the user can update the new contact to subscribe to PAR News$")
	public void the_user_can_update_the_new_contact_to_subscribe_to_PAR_News() throws Throwable {

		String contactsName = DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " "
				+ DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME) + " "
				+ DataStore.getSavedValue(UsableValues.PERSON_LASTNAME);

		LOG.info("New contact's name: " + contactsName + ".");

		parDashboardPage.selectManageProfileDetails();
		LOG.info("Updating the new contact details");

		userProfilePage.selectContactToUpdate(contactsName);
		userProfilePage.selectContinueButton();

		LOG.info("Found the new contact to update.");
		updateUserContactDetailsPage.selectContinueButton();
		updateUserCommunicationPreferencesPage.selectContinueButton();

		updateUserSubscriptionsPage.selectPARNewsSubscription();
		LOG.info("Subscribed to the PAR News Letter.");
		updateUserSubscriptionsPage.selectContinueButton();

		profileReviewPage.saveContactUpdate();
		updateUserConfirmationPage.selectDoneButton();

		LOG.info("Successfully updated the new contact's details.");
	}

	@Then("^the user can update the new contact to unsubscribe from PAR News$")
	public void the_user_can_update_the_new_contact_to_unsubscribe_from_PAR_News() throws Throwable {

		String contactsName = DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " "
				+ DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME) + " "
				+ DataStore.getSavedValue(UsableValues.PERSON_LASTNAME);

		LOG.info("New contact's name: " + contactsName + ".");

		parDashboardPage.selectManageProfileDetails();
		LOG.info("Updating the new contact details");

		userProfilePage.selectContactToUpdate(contactsName);
		userProfilePage.selectContinueButton();

		LOG.info("Found the new contact to update.");
		updateUserContactDetailsPage.selectContinueButton();
		updateUserCommunicationPreferencesPage.selectContinueButton();

		updateUserSubscriptionsPage.selectPARNewsSubscription();
		LOG.info("Unsubscribed from PAR News Letter.");
		updateUserSubscriptionsPage.selectContinueButton();

		profileReviewPage.saveContactUpdate();
		updateUserConfirmationPage.selectDoneButton();

		LOG.info("Successfully updated the new contact's details.");
	}

	@When("^the user is on the Subscriptions page$")
	public void the_user_is_on_the_Subscriptions_page() throws Throwable {
		parDashboardPage.selectManageSubscriptions();
		LOG.info("Navigated to Manage Subscriptions Page.");
	}

	@When("^the user searches for the par_authority email \"([^\"]*)\"$")
	public void the_user_searches_for_the_par_authority_email(String email) throws Throwable {
		newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.PERSON_EMAIL));
		newsLetterSubscriptionPage.ClickSearchButton();
		LOG.info("Searching for the Authority Email:" + DataStore.getSavedValue(UsableValues.PERSON_EMAIL) + ".");
	}

	@Then("^the user can verify the email is successfully in the Subscriptions List$")
	public void the_user_can_verify_the_email_is_successfully_in_the_Subscriptions_List() throws Throwable {
		LOG.info("Assert the Email is successfully added to the Subscription List.");
		assertTrue(newsLetterSubscriptionPage.verifyTableElementExists());
	}

	@Then("^the user can verify the email is successfully removed from the Subscriptions List$")
	public void the_user_can_verify_the_email_is_successfully_removed_from_the_Subscriptions_List() throws Throwable {
		LOG.info("Assert the Email is removed successfully from the Subscription List.");
		assertTrue(newsLetterSubscriptionPage.verifyTableElementIsNull());
	}

	@When("^the user is on the Manage a subscription list page$")
	public void the_user_is_on_the_Manage_a_subscription_list_page() throws Throwable {
		parDashboardPage.selectManageSubscriptions();
		newsLetterSubscriptionPage.selectManageSubsciptions();
		LOG.info("Navigated to Manage Subscriptions Page.");
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

	@When("^the user enters a list of new emails to replace the subscription list:$")
	public void the_user_enters_a_list_of_new_emails_to_replace_the_subscription_list(DataTable newEmails)
			throws Throwable {
		newsLetterManageSubscriptionListPage.selectReplaceSubscriptionListRadioButton();

		for (Map<String, String> data : newEmails.asMaps(String.class, String.class)) {
			newsLetterManageSubscriptionListPage.ReplaceSubscriptionList(data.get("Email"));
		}

		newsLetterManageSubscriptionListPage.clickContinueButton();
		LOG.info("Adding new emails to replace the original Subscription List.");
		newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify an email from the original list was removed successfully \"([^\"]*)\"$")
	public void the_user_can_verify_an_email_from_the_original_list_was_removed_successfully(String email)
			throws Throwable {
		newsLetterSubscriptionPage.EnterEmail(email);
		newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue(newsLetterSubscriptionPage.verifyTableElementIsNull());
		LOG.info("Successfully replaced the original subscription list with a new list.");
	}

	@When("^the user creates a new person with the following details:$")
	public void the_user_creates_a_new_person_with_the_following_details(DataTable newPerson) throws Throwable {
		parDashboardPage.selectManagePeople();
		managePeoplePage.selectAddPerson();

		LOG.info("Adding a new person.");
		for (Map<String, String> data : newPerson.asMaps(String.class, String.class)) {

			addPersonsContactDetailsPage.enterTitle(data.get("Title"));
			addPersonsContactDetailsPage.enterFirstname(data.get("Firstname"));
			addPersonsContactDetailsPage.enterLastname(data.get("Lastname"));
			addPersonsContactDetailsPage.enterWorkPhoneNumber(data.get("WorkNumber"));
			addPersonsContactDetailsPage.enterMobilePhoneNumber(data.get("MobileNumber"));
			addPersonsContactDetailsPage.enterEmailAddress(data.get("Email"));
		}

		addPersonsContactDetailsPage.clickContinueButton();
		givePersonAccountPage.selectInviteUserToCreateAccount();
		givePersonAccountPage.clickContinueButton();

		choosePersonMembershipPage.selectOrganisation("32");
		choosePersonMembershipPage.selectAuthority("10");
		choosePersonMembershipPage.clickContinueButton();

		personUserTypeSelectionPage.selectEnforcementOfficer();
		personUserTypeSelectionPage.clickContinueButton();

		invitePersonToCreateAccountPage.clickInviteButton();

		profileReviewPage.savePersonCreation();
		personCompletionConfirmationPage.clickDoneButton();
		personsProfilePage.clickDoneButton();

		LOG.info("Successfully added a new person.");
	}

	@Then("^the user can verify the person \"([^\"]*)\" was created successfully$")
	public void the_user_can_verify_the_person_was_created_successfully(String name) throws Throwable {
		managePeoplePage.enterNameOrEmail(name);
		managePeoplePage.clickSubmit();

		assertEquals(name, managePeoplePage.GetPersonName()); // If this does not work, need to find the Table Element
																// in a different way.
	}

	@When("^the user searches for an existing person \"([^\"]*)\" successfully$")
	public void the_user_searches_for_an_existing_person_successfully(String name) throws Throwable {
		parDashboardPage.selectManagePeople();

		managePeoplePage.enterNameOrEmail(name);
		managePeoplePage.clickSubmit();

		managePeoplePage.clickManageContact();

		LOG.info("Found an existing user with the name: " + name);

		personsProfilePage.clickUpdateUserButton();
	}

	@When("^the user updates an existing person with the following details:$")
	public void the_user_updates_an_existing_person_with_the_following_details(DataTable updatePerson)
			throws Throwable {
		for (Map<String, String> data : updatePerson.asMaps(String.class, String.class)) {

			addPersonsContactDetailsPage.enterTitle(data.get("Title"));
			addPersonsContactDetailsPage.enterFirstname(data.get("Firstname"));
			addPersonsContactDetailsPage.enterLastname(data.get("Lastname"));
			addPersonsContactDetailsPage.enterWorkPhoneNumber(data.get("WorkNumber"));
			addPersonsContactDetailsPage.enterMobilePhoneNumber(data.get("MobileNumber"));
			addPersonsContactDetailsPage.enterEmailAddress(data.get("Email"));
		}

		addPersonsContactDetailsPage.clickContinueButton();

		LOG.info("Successfully Updated an person's contact details.");

		givePersonAccountPage.selectInviteUserToCreateAccount();
		givePersonAccountPage.clickContinueButton();

		LOG.info("Successfully Invited the person to create an account.");

		choosePersonMembershipPage.selectAuthority("10");
		choosePersonMembershipPage.selectOrganisation("32");
		choosePersonMembershipPage.clickContinueButton();

		LOG.info("Successfully Updated an person's Authority and Organisation Memberships.");

		personUserTypeSelectionPage.selectAuthorityMember();
		personUserTypeSelectionPage.clickContinueButton();

		LOG.info("Successfully Updated an person's User Role.");

		invitePersonToCreateAccountPage.clickInviteButton();

		LOG.info("Successfully Updated the person's Account creation invite.");

		profileReviewPage.savePersonCreation();
		personCompletionConfirmationPage.clickDoneButton();
		personsProfilePage.clickDoneButton();

		LOG.info("Successfully Updated an existing person.");
	}

	@Then("^the user can verify the person \"([^\"]*)\" was updated successfully$")
	public void the_user_can_verify_the_person_was_updated_successfully(String name) throws Throwable {
		managePeoplePage.enterNameOrEmail(name);
		managePeoplePage.clickSubmit();

		assertEquals(name, managePeoplePage.GetPersonName());
	}

	@When("^the user searches for an enforcement notice \"([^\"]*)\" Organisation$")
	public void the_user_searches_for_an_enforcement_notice_Organisation(String search) throws Throwable {
		parDashboardPage.selectManageEnforcementNotices();
		enforcementSearchPage.searchForEnforcementNotice(search);

		LOG.info("Searching for an Enforcement Notice.");
	}

	@Then("^the user can verify the enforcement officers details:$")
	public void the_user_can_verify_the_enforcement_officers_details(DataTable details) throws Throwable {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			assertEquals(data.get("Officer"), enforcementNotificationActionReceivedPage.getEnforcementOfficerDetails());
			assertEquals(data.get("Enforcing"), enforcementNotificationActionReceivedPage.getEnforcingAuthorityName());
			assertEquals(data.get("Organisation"),
					enforcementNotificationActionReceivedPage.getEnforcedOrganisationName());
			assertEquals(data.get("Primary"), enforcementNotificationActionReceivedPage.getPrimaryAuthorityName());
		}

		LOG.info("Asserting the Enforcement Notice Details.");
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

	@When("^the user searches for an Enquiry with the Test Business \"([^\"]*)\" name$")
	public void the_user_searches_for_an_Enquiry_with_the_Test_Business_name(String search) throws Throwable {
		parDashboardPage.selectManageGeneralEnquiries();
		generalEnquiriesPage.chooseGeneralEnquiry(search);

		LOG.info("Searching for Equiry Notice Details.");
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
}
