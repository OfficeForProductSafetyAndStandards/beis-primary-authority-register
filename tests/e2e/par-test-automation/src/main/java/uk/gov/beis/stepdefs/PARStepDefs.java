package uk.gov.beis.stepdefs;

import java.io.IOException;
import java.util.Map;

import org.junit.Assert;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;

import cucumber.api.DataTable;
import cucumber.api.PendingException;
import cucumber.api.java.en.Given;
import cucumber.api.java.en.Then;
import cucumber.api.java.en.When;
import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.helper.LOG;
import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.EmployeesPage;
import uk.gov.beis.pageobjects.PARAuthorityPage;
import uk.gov.beis.pageobjects.PARBusinessAddressDetailsPage;
import uk.gov.beis.pageobjects.PARBusinessContactDetailsPage;
import uk.gov.beis.pageobjects.PARBusinessDetailsPage;
import uk.gov.beis.pageobjects.PARBusinessInvitePage;
import uk.gov.beis.pageobjects.PARBusinessPage;
import uk.gov.beis.pageobjects.PARDashboardPage;
import uk.gov.beis.pageobjects.PARDeclarationPage;
import uk.gov.beis.pageobjects.PARHomePage;
import uk.gov.beis.pageobjects.PARLoginPage;
import uk.gov.beis.pageobjects.PARPartnershipCompletionPage;
import uk.gov.beis.pageobjects.PARPartnershipConfirmationPage;
import uk.gov.beis.pageobjects.PARPartnershipDescriptionPage;
import uk.gov.beis.pageobjects.PARPartnershipTermsPage;
import uk.gov.beis.pageobjects.PARPartnershipTypePage;
import uk.gov.beis.pageobjects.PartnershipSearchPage;
import uk.gov.beis.pageobjects.SICCodePage;
import uk.gov.beis.pageobjects.TradingPage;
import uk.gov.beis.utility.DataStore;
import uk.gov.beis.utility.RandomStringGenerator;

public class PARStepDefs {

	public static WebDriver driver;
	private PARHomePage parHomePage;
	private PARLoginPage parLoginPage;
	private SICCodePage sicCodePage;
	private PARDashboardPage parDashboardPage;
	private PARAuthorityPage parAuthorityPage;
	private PartnershipSearchPage partnershipSearchPage;
	private PARPartnershipTypePage parPartnershipTypePage;
	private PARPartnershipTermsPage parPartnershipTermsPage;
	private PARPartnershipDescriptionPage parPartnershipDescriptionPage;
	private PARBusinessPage parBusinessPage;
	private EmployeesPage employeesPage;
	private PARBusinessDetailsPage parBusinessDetailsPage;
	private PARDeclarationPage parDeclarationPage;
	private PARBusinessContactDetailsPage parBusinessContactDetailsPage;
	private PARPartnershipConfirmationPage parPartnershipConfirmationPage;
	private PARBusinessInvitePage parBusinessInvitePage;
	private PARPartnershipCompletionPage parPartnershipCompletionPage;
	private PARBusinessAddressDetailsPage parBusinessAddressDetailsPage;
	private TradingPage tradingPage;

	public PARStepDefs() throws ClassNotFoundException, IOException {
		driver = ScenarioContext.lastDriver;
		employeesPage = PageFactory.initElements(driver, EmployeesPage.class);
		tradingPage = PageFactory.initElements(driver, TradingPage.class);
		sicCodePage = PageFactory.initElements(driver, SICCodePage.class);
		parHomePage = PageFactory.initElements(driver, PARHomePage.class);
		parBusinessDetailsPage = PageFactory.initElements(driver, PARBusinessDetailsPage.class);
		parDeclarationPage = PageFactory.initElements(driver, PARDeclarationPage.class);
		parLoginPage = PageFactory.initElements(driver, PARLoginPage.class);
		parDashboardPage = PageFactory.initElements(driver, PARDashboardPage.class);
		parAuthorityPage = PageFactory.initElements(driver, PARAuthorityPage.class);
		parPartnershipTypePage = PageFactory.initElements(driver, PARPartnershipTypePage.class);
		parPartnershipDescriptionPage = PageFactory.initElements(driver, PARPartnershipDescriptionPage.class);
		parBusinessPage = PageFactory.initElements(driver, PARBusinessPage.class);
		parBusinessContactDetailsPage = PageFactory.initElements(driver, PARBusinessContactDetailsPage.class);
		parPartnershipConfirmationPage = PageFactory.initElements(driver, PARPartnershipConfirmationPage.class);
		parBusinessInvitePage = PageFactory.initElements(driver, PARBusinessInvitePage.class);
		parPartnershipCompletionPage = PageFactory.initElements(driver, PARPartnershipCompletionPage.class);
		parBusinessAddressDetailsPage = PageFactory.initElements(driver, PARBusinessAddressDetailsPage.class);
		parPartnershipTermsPage = PageFactory.initElements(driver, PARPartnershipTermsPage.class);
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
		String pass = PropertiesUtil.getConfigPropertyValue(user);
		LOG.info("Logging in user with credentials; username: " + user + " and password +" + pass);
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
			parAuthorityPage.selectAuthority(data.get("Authority"));
			LOG.info("Select partnership type");
			parPartnershipTypePage.selectPartnershipType(type);
			LOG.info("Accepting terms");
			parPartnershipTermsPage.acceptTerms();
			DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, data.get("Partnership Info"));
			LOG.info("Entering partnership description");
			parPartnershipDescriptionPage.enterPartnershipDescription(data.get("Partnership Info"));
			LOG.info("Entering business/organisation name");
			DataStore.saveValue(UsableValues.BUSINESS_NAME, RandomStringGenerator.getBusinessName(3));
			parBusinessPage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
			LOG.info("Enter address details");
			parBusinessAddressDetailsPage.enterAddressDetails(data.get("addressline1"), data.get("town"),
					data.get("postcode"));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE1, data.get("addressline1"));
			DataStore.saveValue(UsableValues.BUSINESS_TOWN, data.get("town"));
			DataStore.saveValue(UsableValues.BUSINESS_POSTCODE, data.get("postcode"));

			DataStore.saveValue(UsableValues.BUSINESS_EMAIL, RandomStringGenerator.getEmail(3));
			LOG.info("Enter contact details");
			parBusinessContactDetailsPage.enterContactDetails(data.get("firstname"), data.get("lastname"),
					data.get("phone"), DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
			DataStore.saveValue(UsableValues.BUSINESS_FIRSTNAME, data.get("firstname"));
			DataStore.saveValue(UsableValues.BUSINESS_LASTNAME, data.get("lastname"));
			DataStore.saveValue(UsableValues.BUSINESS_PHONE, data.get("phone"));
			LOG.info("Send invitation to user");
			parBusinessInvitePage.sendInvite();
			LOG.info("Confirm partnership details");
			parPartnershipConfirmationPage.confirmDetails();
			Assert.assertTrue("Appliction not complete", parPartnershipConfirmationPage.checkPartnershipApplication());
			LOG.info("Saving changes");
			parPartnershipConfirmationPage.saveChanges();
			parPartnershipCompletionPage.completeApplication();
		}
	}

	@Then("^the partnership application is successfully created$")
	public void the_partnership_application_is_successfully_created() throws Throwable {
//		Assert.assertTrue("Not created successfully",parPartnershipConfirmationPage.checkPartnershipApplication());
	}

	@When("^the user searches for the last created partnership$")
	public void the_user_searches_for_the_last_created_partnership() throws Throwable {
		LOG.info("Selecting view partnerships");
		parDashboardPage.selectSeePartnerships();
		LOG.info("Search partnerships");
		partnershipSearchPage.searchPartnerships();
		LOG.info("Select organisation link details");
		partnershipSearchPage.selectBusinessNameLink();
	}

	@When("^the user completes the direct partnership application with the following details:$")
	public void the_user_completes_the_direct_partnership_application_with_the_following_details(DataTable details)
			throws Throwable {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			LOG.info("Accepting terms");
			parDeclarationPage.acceptTerms();
			LOG.info("Add business description");
			parBusinessDetailsPage.enterBusinessDescription(data.get("Business Description"));
			LOG.info("Confirming address details");
			parBusinessAddressDetailsPage.proceed();
			LOG.info("Confirming contact details");
			parBusinessContactDetailsPage.proceed();
			LOG.info("Selecting SIC Code");
			sicCodePage.selectSICCode(data.get("SIC Code"));
			LOG.info("Selecting No of Employees");
			employeesPage.selectNoEmployees(data.get("No of Employees"));
			LOG.info("Entering business trading name");
			tradingPage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME).replace("Business", "trading name"));
		}
	}
}
