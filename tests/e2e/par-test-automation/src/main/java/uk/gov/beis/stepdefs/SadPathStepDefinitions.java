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
		DataStore.saveValue(UsableValues.PARTNERSHIP_TYPE, partnershipType);
		
		websiteManager.parPartnershipTypePage.selectPartnershipType(DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE));
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
	
	@When("^the user does not confirm they have permission from the organisation$")
	public void the_user_does_not_confirm_they_have_permission_from_the_organisation() throws Throwable {
		LOG.info("Not declaring permission to complete the partnership by proxy.");
		websiteManager.declarationPage.deselectConfirmCheckbox();
		websiteManager.declarationPage.clickContinueButton();
	}
	
	@When("^the user confirms they have permission from the organisation$")
	public void the_user_confirms_they_have_permission_from_the_organisation() throws Throwable {
		LOG.info("Declaring permission to complete partnership by proxy.");
		websiteManager.declarationPage.selectConfirmCheckbox();
		websiteManager.declarationPage.goToBusinessDetailsPage();
	}

	@When("^the user leaves the details about the organisation field empty$")
	public void the_user_leaves_the_details_about_the_organisation_field_empty() throws Throwable {
		LOG.info("Leaving the details about the Organisation field empty.");
		websiteManager.aboutTheOrganisationPage.enterDescription("");
		websiteManager.aboutTheOrganisationPage.clickContinueButton();
	}

	@When("^the user enters details about the organisation$")
	public void the_user_enters_details_about_the_organisation() throws Throwable {
		LOG.info("Entering details about the Organisation.");
		
		DataStore.saveValue(UsableValues.BUSINESS_DESC, "Error Message Testing.");
		
		websiteManager.aboutTheOrganisationPage.enterDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		websiteManager.aboutTheOrganisationPage.goToAddressPage();
	}

	@When("^the user leaves all address details fields empty$")
	public void the_user_leaves_all_address_details_fields_empty() throws Throwable {
		LOG.info("Leaving all address fields empty.");
		websiteManager.addAddressPage.clearAddressFields();
		websiteManager.addAddressPage.clickContinueButton();
	}

	@When("^the user confirms the address details with the following:$")
	public void the_user_confirms_the_address_details_with_the_following(DataTable details) throws Throwable {
		LOG.info("Reentering the address details.");
		
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

	@When("^the user leaves all contact details fields empty$")
	public void the_user_leaves_all_contact_details_fields_empty() throws Throwable {
		LOG.info("Leave the contact details fields empty.");
		websiteManager.contactDetailsPage.clearAllFields();
		websiteManager.contactDetailsPage.clickContinueButton();
	}

	@When("^the user confirms the primary contact details with the following:$")
	public void the_user_confirms_the_primary_contact_details_with_the_following(DataTable details) throws Throwable {
		LOG.info("Reeneter the contact details.");
		websiteManager.contactDetailsPage.addContactDetails(details);
		
		LOG.info("Select the prefered contact methods.");
		websiteManager.contactDetailsPage.selectPreferredEmail();
		websiteManager.contactDetailsPage.selectPreferredWorkphone();
		websiteManager.contactDetailsPage.selectPreferredMobilephone();
		DataStore.saveValue(UsableValues.CONTACT_NOTES, "Test Note.");
		websiteManager.contactDetailsPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		websiteManager.contactDetailsPage.goToSICCodePage();
	}

	@When("^the user confirms the sic code$")
	public void the_user_confirms_the_sic_code() throws Throwable {
		LOG.info("Selecting SIC Code");
		DataStore.saveValue(UsableValues.SIC_CODE, "allow people to eat");
		websiteManager.sicCodePage.selectSICCode(DataStore.getSavedValue(UsableValues.SIC_CODE));
	}

	@When("^the user does not confirm the number of employees$")
	public void the_user_does_not_confirm_the_number_of_employees() throws Throwable {
		LOG.info("Not selecting a number of employees option.");
	    websiteManager.employeesPage.clickContinueButton();
	}

	@When("^the user confirms the number of employees$")
	public void the_user_confirms_the_number_of_employees() throws Throwable {
		
		switch (DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase()) {

		case ("direct"):
			LOG.info("Selecting No of Employees");
			DataStore.saveValue(UsableValues.NO_EMPLOYEES, "50 to 249");
			websiteManager.employeesPage.selectNoEmployees(DataStore.getSavedValue(UsableValues.NO_EMPLOYEES));
			break;

		case ("co-ordinated"):
			LOG.info("Selecting Membership List size");
			DataStore.saveValue(UsableValues.MEMBERLIST_SIZE, "Medium");
			websiteManager.memberListPage.selectMemberSize(DataStore.getSavedValue(UsableValues.MEMBERLIST_SIZE));
			break;
		}
	}

	@When("^the user leaves the trading name field empty$")
	public void the_user_leaves_the_trading_name_field_empty() throws Throwable {
		LOG.info("Leaving the Trading Name field empty.");
		websiteManager.tradingPage.enterTradingName("");
		websiteManager.tradingPage.clickContinueButton();
	}

	@When("^the user enters a trading name \"([^\"]*)\"$")
	public void the_user_enters_a_trading_name(String tradingName) throws Throwable {
		LOG.info("Entering a Trading Name.");
		DataStore.saveValue(UsableValues.TRADING_NAME, tradingName);
		websiteManager.tradingPage.enterTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
		websiteManager.tradingPage.goToLegalEntityTypePage();
	}

	@When("^the user does not select a registered, charity or unregistered legal entity$")
	public void the_user_does_not_select_a_registered_charity_or_unregistered_legal_entity() throws Throwable {
		LOG.info("Not selecting a Legal Entity type.");
		websiteManager.legalEntityTypePage.clickContinueButton();
	}

	@When("^the user selects an \"([^\"]*)\" legal entity$")
	public void the_user_selects_an_legal_entity(String entityType) throws Throwable {
		LOG.info("Selecting a Legal Entity type.");
		websiteManager.legalEntityTypePage.selectUnregisteredEntity(entityType, "");
	}

	@When("^the user does not select a legal entity type or enter a legal entity name$")
	public void the_user_does_not_select_a_legal_entity_type_or_enter_a_legal_entity_name() throws Throwable {
		LOG.info("Entering a Legal Entity type but not selecting an entity structure or entering an entity name.");
		websiteManager.legalEntityTypePage.clickContinueButton();
	}

	@When("^the user chooses a legal entity with the following details:$")
	public void the_user_chooses_a_legal_entity_with_the_following_details(DataTable details) throws Throwable {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENTITY_NAME, data.get("Legal Entity Name"));
			DataStore.saveValue(UsableValues.ENTITY_TYPE, data.get("Legal Entity Type"));
		}
		
		LOG.info("Entering a Legal Entity.");
		websiteManager.legalEntityTypePage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		websiteManager.legalEntityTypePage.goToLegalEntityReviewPage();
	}

	@When("^the user confirms the legal entity$")
	public void the_user_confirms_the_legal_entity() throws Throwable {
		LOG.info("Confirm the Legal Entity.");
		websiteManager.legalEntityReviewPage.goToCheckPartnershipInformationPage();
	}

	@When("^the user does not confirm they have read the terms and conditions$")
	public void the_user_does_not_confirm_they_have_read_the_terms_and_conditions() throws Throwable {
		LOG.info("Not accepting the partnership terms and conditions.");
		websiteManager.checkPartnershipInformationPage.deselectOrganisationConfirmationCheckbox();
	}

	@Then("^the user confirms the second part of the partnership application$")
	public void the_user_confirms_the_second_part_of_the_partnership_application() throws Throwable {
		LOG.info("Verify the Partnership Information.");
		
		Assert.assertTrue("About the Organisation is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyAboutTheOrganisation());
		Assert.assertTrue("Organisation Name is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyOrganisationName());
		Assert.assertTrue("Organisation Address is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyOrganisationAddress());
		Assert.assertTrue("Organisation Contact is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyContactAtTheOrganisation());
		
		Assert.assertTrue("Primary SIC Code is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyPrimarySICCode());
		
		switch (DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase()) {
			case ("direct"):
				LOG.info("Checking Employee Size.");
				Assert.assertTrue("Number of Employees is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyNumberOfEmployees());
				break;
			case ("co-ordinated"):
				LOG.info("Checking Members Size.");
				Assert.assertTrue("Members Size is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyMemberSize());
				break;
		}
		
		Assert.assertTrue("Legal Entity is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyLegalEntity());
		Assert.assertTrue("Trading Name is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyTradingName());
		
		LOG.info("Complete Partnership Application.");
		websiteManager.checkPartnershipInformationPage.confirmApplication();
		websiteManager.parPartnershipCompletionPage.clickDoneButton();
	    
	    LOG.info("Set second part of journey part to true.");
		ScenarioContext.secondJourneyPart = true;
	}
	
	@When("^the user selects the approve partnership action link$")
	public void the_user_selects_the_approve_partnership_action_link() throws Throwable {
		LOG.info("Select last created partnership Approval link.");
		websiteManager.partnershipAdvancedSearchPage.selectApproveBusinessNameLink();
	}

	@When("^the user does not confirm they are authorised to approve$")
	public void the_user_does_not_confirm_they_are_authorised_to_approve() throws Throwable {
		LOG.info("Not confirming the user is authorised to approve the partnership.");
		websiteManager.declarationPage.clickContinueButton();
	}

	@When("^the user confirms they are authorised to approve$")
	public void the_user_confirms_they_are_authorised_to_approve() throws Throwable {
		LOG.info("Confirming the user is authorised to approve the partnership.");
		websiteManager.declarationPage.selectAuthorisedCheckbox();
		websiteManager.declarationPage.goToRegulatoryFunctionsPage();
	}

	@When("^the user selects the bespoke Radio but not the type of bespoke regulatory functions$")
	public void the_user_selects_the_bespoke_Radio_but_not_the_type_of_bespoke_regulatory_functions() throws Throwable {
		LOG.info("Selecting the Bespoke Radio button but not selecting the type of Regulatory Functions.");
		websiteManager.regulatoryFunctionPage.deselectBespokeFunctions();
		websiteManager.regulatoryFunctionPage.selectContinueButton();
	}
	
	@When("^the user selects the type of bespoke regulatory functions$")
	public void the_user_selects_the_type_of_bespoke_regulatory_functions() throws Throwable {
		LOG.info("Selecting the Bespoke Radio button andthe type of Regulatory Functions.");
		websiteManager.regulatoryFunctionPage.selectBespokeFunctions();
		websiteManager.regulatoryFunctionPage.goToPartnershipApprovedPage();
		
		LOG.info("Complete the Partnership Approval.");
		websiteManager.partnershipApprovalPage.completeApplication();
	}

	@Then("^the partnership is approved successfully$")
	public void the_partnership_is_approved_successfully() throws Throwable {
		LOG.info("Check status of partnership is set to Active.");
		assertTrue(websiteManager.partnershipAdvancedSearchPage.checkPartnershipStatus("Active"));
	}
	
	@When("^the user selects the revoke partnership action link$")
	public void the_user_selects_the_revoke_partnership_action_link() throws Throwable {
		LOG.info("Selecting the Revoke Partnership link.");
		websiteManager.partnershipAdvancedSearchPage.selectRevokeBusinessNameLink();
	}

	@When("^the user leaves the revoke reason field empty$")
	public void the_user_leaves_the_revoke_reason_field_empty() throws Throwable {
		LOG.info("Leaving the Revoke reason field empty.");
		websiteManager.revokePage.enterReasonForRevocation("");
		websiteManager.revokePage.clickRevokeButton();
	}

	@When("^the user enters a revoke reason$")
	public void the_user_enters_a_revoke_reason() throws Throwable {
		LOG.info("Revoking last created partnership.");
		websiteManager.revokePage.enterReasonForRevocation("Test Revoke.");
		websiteManager.revokePage.goToPartnershipRevokedPage();
		
		websiteManager.partnershipRevokedPage.goToAdvancedPartnershipSearchPage();
	}

	@Then("^the partnership is revoked successfully$")
	public void the_partnership_is_revoked_successfully() throws Throwable {
		LOG.info("Check status of partnership is set to Revoked.");
		assertTrue(websiteManager.partnershipAdvancedSearchPage.checkPartnershipStatus("Revoked"));
	}

	@When("^the user selects the restore partnership action link$")
	public void the_user_selects_the_restore_partnership_action_link() throws Throwable {
		LOG.info("Selecting the Reinstate Partnership link.");
		websiteManager.partnershipAdvancedSearchPage.selectRestoreBusinessNameLink();
	}
	
	@When("^the user restores the revoked partnership$")
	public void the_user_restores_the_revoked_partnership() throws Throwable {
		LOG.info("Reinstate the revoked Partnership.");
		websiteManager.reinstatePage.goToPartnershipRestoredPage();
		websiteManager.partnershipRestoredPage.goToAdvancedPartnershipSearchPage();
	}

	@Then("^the partnership is restored successfully$")
	public void the_partnership_is_restored_successfully() throws Throwable {
		LOG.info("Check status of partnership is set to Active.");
		assertTrue(websiteManager.partnershipAdvancedSearchPage.checkPartnershipStatus("Active"));
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
