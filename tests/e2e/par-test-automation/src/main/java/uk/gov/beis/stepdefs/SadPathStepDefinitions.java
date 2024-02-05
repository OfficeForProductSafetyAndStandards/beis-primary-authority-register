package uk.gov.beis.stepdefs;

import static org.junit.Assert.assertTrue;

import java.io.IOException;
import java.util.Map;

import org.junit.Assert;

import cucumber.api.DataTable;
import cucumber.api.java.en.Given;
import cucumber.api.java.en.Then;
import cucumber.api.java.en.When;
import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.helper.LOG;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.WebsiteManager;
import uk.gov.beis.utility.DataStore;
import uk.gov.beis.utility.RandomStringGenerator;

public class SadPathStepDefinitions {
	
	private WebsiteManager websiteManager;
	
	public SadPathStepDefinitions() throws ClassNotFoundException, IOException {
		websiteManager = new WebsiteManager();
	}
	
	// Coordinated Partnership Features
	
	
	// Direct Partnership Features
	@When("^the user applies for a new partnership$")
	public void the_user_applies_for_a_new_partnership() throws Throwable {
		ScenarioContext.secondJourneyPart = false;
		
		LOG.info("Select apply new partnership");
		websiteManager.dashboardPage.selectApplyForNewPartnership();
	}

	@When("^does not select a primary authority$")
	public void does_not_select_a_primary_authority() throws Throwable {
		LOG.info("Not choosing an Authority.");
		websiteManager.parAuthorityPage.selectContinueButton();
	}

	@Then("^the user is shown the \"([^\"]*)\" error message$")
	public void the_user_is_shown_the_error_message(String expectedMessage) throws Throwable {
		LOG.info("Validating the error message.");
		
		assertTrue(websiteManager.basePageObject.checkErrorSummary(expectedMessage));
	}
	
	@When("^the user selects a primary authority$")
	public void the_user_selects_a_primary_authority() throws Throwable {
		LOG.info("Choose authority");
		websiteManager.parAuthorityPage.selectAuthority("Upper");
	}
	
	@When("^the user does not select a partnership type$")
	public void the_user_does_not_select_a_partnership_type() throws Throwable {
		LOG.info("Not select partnership type.");
		websiteManager.parPartnershipTypePage.clickContinueButton();
	}

	@When("^the user selects a \"([^\"]*)\" partnership type$")
	public void the_user_selects_a_partnership_type(String partnershipType) throws Throwable {
		LOG.info("Select partnership type.");
		websiteManager.parPartnershipTypePage.selectPartnershipType(partnershipType);
	}

	@When("^the user does not confirm the terms and conditions$")
	public void the_user_does_not_confirm_the_terms_and_conditions() throws Throwable {
		LOG.info("Not accepoting the Terms and Conditions.");
		websiteManager.parPartnershipTermsPage.deselectTerms();
	}
	
	@When("^the user confirms the partnership terms and conditions$")
	public void the_user_confirms_the_partnership_terms_and_conditions() throws Throwable {
		LOG.info("Confirm Terms and Conditions.");
		websiteManager.parPartnershipTermsPage.acceptTerms();
	}

	@When("^the user leaves the information about the partnership field empty$")
	public void the_user_leaves_the_information_about_the_partnership_field_empty() throws Throwable {
		LOG.info("Not entering information about the partnership.");
		
		websiteManager.parPartnershipDescriptionPage.enterDescription("");
		websiteManager.parPartnershipDescriptionPage.clickContinueButton();
	}
	
	@When("^the user enters informations about the partnership$")
	public void the_user_enters_informations_about_the_partnership() throws Throwable {
		LOG.info("Entering information about the partnership.");
		DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, "Partnership Sad Path Testing.");
		
		websiteManager.parPartnershipDescriptionPage.enterDescription(DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO));
		websiteManager.parPartnershipDescriptionPage.gotToBusinessNamePage();
	}

	@When("^the user leaves the organisation name field empty$")
	public void the_user_leaves_the_organisation_name_field_empty() throws Throwable {
		LOG.info("Not enterring an Organisation name.");
		websiteManager.businessNamePage.enterBusinessName("");
		websiteManager.businessNamePage.clickContinueButton();
	}
	
	@When("^the user enters an orgnasiation name$")
	public void the_user_enters_an_orgnasiation_name() throws Throwable {
		LOG.info("Entering organisation name.");
		DataStore.saveValue(UsableValues.BUSINESS_NAME, RandomStringGenerator.getBusinessName(4));
		
		websiteManager.businessNamePage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		websiteManager.businessNamePage.goToAddressPage();
	}

	@When("^the user leaves all the address fields empty$")
	public void the_user_leaves_all_the_address_fields_empty() throws Throwable {
		LOG.info("Not entering an address.");
		websiteManager.addAddressPage.clearAddressFields();
		websiteManager.addAddressPage.clickContinueButton();
	}

	@Then("^the user is shown the following error messages:$")
	public void the_user_is_shown_the_following_error_messages(DataTable expectedMessages) throws Throwable {
		LOG.info("Validating the error messages.");
		
		for (Map<String, String> message : expectedMessages.asMaps(String.class, String.class)) {
			assertTrue(websiteManager.basePageObject.checkErrorSummary(message.get("ErrorMessage")));
		}
	}

	@When("^the user enters an address with the following details:$")
	public void the_user_enters_an_address_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Enter the address details.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE1, data.get("AddressLine1"));
			DataStore.saveValue(UsableValues.BUSINESS_ADDRESSLINE2, data.get("AddressLine2"));
			
			DataStore.saveValue(UsableValues.BUSINESS_TOWN, data.get("Town"));
			DataStore.saveValue(UsableValues.BUSINESS_COUNTY, data.get("County"));
			DataStore.saveValue(UsableValues.BUSINESS_COUNTRY, data.get("Country"));
			DataStore.saveValue(UsableValues.BUSINESS_NATION, data.get("Nation"));
			DataStore.saveValue(UsableValues.BUSINESS_POSTCODE, data.get("Postcode"));
		}
		
		websiteManager.addAddressPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY),
				DataStore.getSavedValue(UsableValues.BUSINESS_NATION), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		websiteManager.addAddressPage.goToAddContactDetailsPage();
	}

	@When("^the user leaves the contact details fields empty$")
	public void the_user_leaves_the_contact_details_fields_empty() throws Throwable {
		LOG.info("Do not enter the contact details.");
		websiteManager.contactDetailsPage.clearAllFields();
		websiteManager.contactDetailsPage.clickContinueButton();
	}

	@When("^the user enters a contact with the following details:$")
	public void the_user_enters_a_contact_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Entering the contact details.");
		websiteManager.contactDetailsPage.addContactDetails(details);
		websiteManager.contactDetailsPage.goToInviteUserAccountPage();
	}

	@When("^the user invites the business$")
	public void the_user_invites_the_business() throws Throwable {
		LOG.info("Send invitation to the user.");
		websiteManager.accountInvitePage.sendInvite();
	}

	@When("^the user clicks the save button without accepting the terms and conditions$")
	public void the_user_clicks_the_save_button_without_accepting_the_terms_and_conditions() throws Throwable {
		LOG.info("Not accepting the partnership terms and conditions.");
		websiteManager.checkPartnershipInformationPage.deselectConfirmationCheckbox();
	}
	
	@When("^the user accepts the partnership terms and conditions$")
	public void the_user_accepts_the_partnership_terms_and_conditions() throws Throwable {
		LOG.info("Accept the Terms and Conditions and complete the Partnership Application.");
		websiteManager.checkPartnershipInformationPage.acceptTermsAndConditions();
	}

	@Then("^the user confirms the first part of the partnership application$")
	public void the_user_confirms_the_first_part_of_the_partnership_application() throws Throwable {
		LOG.info("Verifying Partnership Details on the Review Page.");
		
		Assert.assertTrue("About the Partnership is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyAboutThePartnership());
		Assert.assertTrue("Organisation Name is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyOrganisationName());
		Assert.assertTrue("Organisation Address is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyOrganisationAddress());
		Assert.assertTrue("Organisation Contact is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyContactAtTheOrganisation());
		Assert.assertTrue("Primary Authority name is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyPrimaryAuthorityName());
		
		LOG.info("Accept the Terms and Conditions and complete the Partnership Application.");
		websiteManager.checkPartnershipInformationPage.completeApplication();
		websiteManager.parPartnershipCompletionPage.clickDoneButton();
	}
	
	
	// Other Features
	@Given("^the user is on the home page$")
	public void the_user_is_on_the_home_page() throws Throwable {
		LOG.info("Navigating to PAR Home page.");
		websiteManager.homePage.navigateToUrl();
	}

	@Given("^the user is on the sign in page$")
	public void the_user_is_on_the_sign_in_page() throws Throwable {
		LOG.info("Navigating to PAR login page - logging out user first if already logged in");
		websiteManager.loginPage.navigateToUrl();
	}
	
	@When("^the user enters the following  and  credentials$")
	public void the_user_enters_the_following_and_credentials() throws Throwable {
		LOG.info("Leaving the email and password fields empty.");
		
		websiteManager.loginPage.enterEmailAddress("");
		websiteManager.loginPage.enterPassword("");
		websiteManager.loginPage.selectSignIn();
	}

	@Then("^the user is shown an error message The Enter your e-mail address is required\\. The Enter your password is required\\. successfully$")
	public void the_user_is_shown_an_error_message_The_Enter_your_e_mail_address_is_required_The_Enter_your_password_is_required_successfully() throws Throwable {
		LOG.info("Validating the error messages.");
		
		assertTrue(websiteManager.loginPage.checkErrorSummary("The Enter your e-mail address is required."));
		assertTrue(websiteManager.loginPage.checkErrorSummary("The Enter your password is required."));
	}

	@When("^the user enters the following  and TestPassword credentials$")
	public void the_user_enters_the_following_and_TestPassword_credentials() throws Throwable {
		LOG.info("Entering a valid password and leaving the email field empty.");
		
		websiteManager.loginPage.enterEmailAddress("");
		websiteManager.loginPage.enterPassword("TestPassword");
		websiteManager.loginPage.selectSignIn();
	}

	@Then("^the user is shown an error message The Enter your e-mail address is required\\.  successfully$")
	public void the_user_is_shown_an_error_message_The_Enter_your_e_mail_address_is_required_successfully() throws Throwable {
		LOG.info("Validating the error message.");
		
		assertTrue(websiteManager.loginPage.checkErrorSummary("The Enter your e-mail address is required."));
	}

	@When("^the user enters the following par_coordinator@example\\.com and  credentials$")
	public void the_user_enters_the_following_par_coordinator_example_com_and_credentials() throws Throwable {
		LOG.info("Entering a valid email and leaving the password field empty.");
		
		websiteManager.loginPage.enterEmailAddress("par_coordinator@example.com");
		websiteManager.loginPage.enterPassword("");
		websiteManager.loginPage.selectSignIn();
	}

	@Then("^the user is shown an error message The Enter your password is required\\. Unrecognized username or password\\. Forgot your password\\? successfully$")
	public void the_user_is_shown_an_error_message_The_Enter_your_password_is_required_Unrecognized_username_or_password_Forgot_your_password_successfully() throws Throwable {
		LOG.info("Validating the error messages.");
		
		assertTrue(websiteManager.loginPage.checkErrorSummary("The Enter your password is required."));
		assertTrue(websiteManager.loginPage.checkErrorSummary("Unrecognized username or password. Forgot your password?"));
	}
	
	@When("^the user enters the following par_coordinator@example\\.com and Invalid credentials$")
	public void the_user_enters_the_following_par_coordinator_example_com_and_Invalid_credentials() throws Throwable {
		LOG.info("Entering a valid email and an invalid password.");
		
		websiteManager.loginPage.enterEmailAddress("par_coordinator@example.com");
		websiteManager.loginPage.enterPassword("Invalid");
		websiteManager.loginPage.selectSignIn();
	}
	
	@When("^the user enters the following Invalid and TestPassword credentials$")
	public void the_user_enters_the_following_Invalid_and_TestPassword_credentials() throws Throwable {
		LOG.info("Entering an invalid email and a valid password.");
		
		websiteManager.loginPage.enterEmailAddress("Invalid");
		websiteManager.loginPage.enterPassword("TestPassword");
		websiteManager.loginPage.selectSignIn();
	}

	@When("^the user enters the following Invalid and Invalid credentials$")
	public void the_user_enters_the_following_Invalid_and_Invalid_credentials() throws Throwable {
		LOG.info("Entering an invalid email and password.");
		
		websiteManager.loginPage.enterEmailAddress("Invalid");
		websiteManager.loginPage.enterPassword("Invalid");
		websiteManager.loginPage.selectSignIn();
	}

	@Then("^the user is shown an error message Unrecognized username or password\\. Forgot your password\\?  successfully$")
	public void the_user_is_shown_an_error_message_Unrecognized_username_or_password_Forgot_your_password_successfully() throws Throwable {
		LOG.info("Validating the error message.");
		
		assertTrue(websiteManager.loginPage.checkErrorSummary("Unrecognized username or password. Forgot your password?"));
	}

	
}
