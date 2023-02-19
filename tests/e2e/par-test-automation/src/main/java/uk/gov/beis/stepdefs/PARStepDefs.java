package uk.gov.beis.stepdefs;

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
import uk.gov.beis.pageobjects.AuthorityAddressDetailsPage;
import uk.gov.beis.pageobjects.AuthorityConfirmationPage;
import uk.gov.beis.pageobjects.AuthorityDashboardPage;
import uk.gov.beis.pageobjects.AuthorityNamePage;
import uk.gov.beis.pageobjects.AuthorityPage;
import uk.gov.beis.pageobjects.AuthorityTypePage;
import uk.gov.beis.pageobjects.BusinessAddressDetailsPage;
import uk.gov.beis.pageobjects.BusinessConfirmationPage;
import uk.gov.beis.pageobjects.BusinessContactDetailsPage;
import uk.gov.beis.pageobjects.BusinessDetailsPage;
import uk.gov.beis.pageobjects.BusinessInvitePage;
import uk.gov.beis.pageobjects.BusinessPage;
import uk.gov.beis.pageobjects.DashboardPage;
import uk.gov.beis.pageobjects.DeclarationPage;
import uk.gov.beis.pageobjects.EmployeesPage;
import uk.gov.beis.pageobjects.EnforcementActionPage;
import uk.gov.beis.pageobjects.EnforcementCompletionPage;
import uk.gov.beis.pageobjects.EnforcementContactDetailsPage;
import uk.gov.beis.pageobjects.EnforcementDetailsPage;
import uk.gov.beis.pageobjects.EnforcementLegalEntityPage;
import uk.gov.beis.pageobjects.EnforcementNotificationPage;
import uk.gov.beis.pageobjects.EnforcementReviewPage;
import uk.gov.beis.pageobjects.EnforcementSearchPage;
import uk.gov.beis.pageobjects.HomePage;
import uk.gov.beis.pageobjects.InspectionPlanDetailsPage;
import uk.gov.beis.pageobjects.InspectionPlanExpirationPage;
import uk.gov.beis.pageobjects.InspectionPlanSearchPage;
import uk.gov.beis.pageobjects.LegalEntityPage;
import uk.gov.beis.pageobjects.LoginPage;
import uk.gov.beis.pageobjects.MailLogPage;
import uk.gov.beis.pageobjects.MemberListPage;
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
import uk.gov.beis.pageobjects.ProposedEnforcementPage;
import uk.gov.beis.pageobjects.RegulatoryFunctionPage;
import uk.gov.beis.pageobjects.RemoveEnforcementConfirmationPage;
import uk.gov.beis.pageobjects.RemoveEnforcementPage;
import uk.gov.beis.pageobjects.RestorePartnershipConfirmationPage;
import uk.gov.beis.pageobjects.RevokePartnershipConfirmationPage;
import uk.gov.beis.pageobjects.SICCodePage;
import uk.gov.beis.pageobjects.TradingPage;
import uk.gov.beis.pageobjects.UploadInspectionPlanPage;
import uk.gov.beis.pageobjects.UserCommsPreferencesPage;
import uk.gov.beis.pageobjects.UserNotificationPreferencesPage;
import uk.gov.beis.pageobjects.UserProfileCompletionPage;
import uk.gov.beis.pageobjects.UserProfileConfirmationPage;
import uk.gov.beis.pageobjects.UserSubscriptionPage;
import uk.gov.beis.pageobjects.UserTermsPage;
import uk.gov.beis.utility.DataStore;
import uk.gov.beis.utility.RandomStringGenerator;

public class PARStepDefs {

	public static WebDriver driver;
	private HomePage parHomePage;
	private InspectionPlanSearchPage inspectionPlanSearchPage;
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
	private ProposedEnforcementPage proposedEnforcementPage;
	private EnforcementReviewPage enforcementReviewPage;
	private RegulatoryFunctionPage regulatoryFunctionPage;
	private AuthorityConfirmationPage authorityConfirmationPage;
	private AuthorityAddressDetailsPage authorityAddressDetailsPage;
	private AuthorityTypePage authorityTypePage;
	private AuthorityNamePage authorityNamePage;
	private PartnershipAdvancedSearchPage partnershipAdvancedSearchPage;
	private UserProfileConfirmationPage userProfileConfirmationPage;
	private UserNotificationPreferencesPage userNotificationPreferencesPage;
	private MailLogPage mailLogPage;
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
	private InspectionPlanDetailsPage inspectionPlanDetailsPage;
	private RestorePartnershipConfirmationPage restorePartnershipConfirmationPage;
	private PartnershipRestoredPage partnershipRestoredPage;
	private RemoveEnforcementConfirmationPage removeEnforcementConfirmationPage;

	public PARStepDefs() throws ClassNotFoundException, IOException {
		driver = ScenarioContext.lastDriver;
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
		userNotificationPreferencesPage.selectContinue();
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
		LOG.info("Select last created enforcement");
		parDashboardPage.selectSeeEnforcementNotices();
		enforcementSearchPage.searchPartnerships();
		enforcementSearchPage.selectEnforcement();
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
			inspectionPlanDetailsPage.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
			inspectionPlanDetailsPage.save();
			inspectionPlanExpirationPage.enterDate("ddMMYYYY");
			inspectionPlanExpirationPage.save();
			System.exit(1);
		}
	}
}
