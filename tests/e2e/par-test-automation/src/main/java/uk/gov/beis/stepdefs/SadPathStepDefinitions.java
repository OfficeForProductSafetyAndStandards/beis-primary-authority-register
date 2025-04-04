package uk.gov.beis.stepdefs;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.io.IOException;
import java.util.Map;

import org.junit.Assert;

import io.cucumber.datatable.DataTable;
import io.cucumber.java.en.*;

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
		websiteManager.parAuthorityPage.selectContinueButton();
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
		websiteManager.parPartnershipTypePage.clickContinueButton();
	}

	@When("^the user does not confirm the terms and conditions$")
	public void the_user_does_not_confirm_the_terms_and_conditions() throws Throwable {
		LOG.info("Not accepoting the Terms and Conditions.");
		websiteManager.parPartnershipTermsPage.deselectTerms();
		websiteManager.parPartnershipTermsPage.clickContinueButton();
	}
	
	@When("^the user confirms the partnership terms and conditions$")
	public void the_user_confirms_the_partnership_terms_and_conditions() throws Throwable {
		LOG.info("Confirm Terms and Conditions.");
		websiteManager.parPartnershipTermsPage.acceptTerms();
		websiteManager.parPartnershipTermsPage.clickContinueButton();
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
		websiteManager.parPartnershipDescriptionPage.clickContinueButton();
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
		websiteManager.businessNamePage.clickContinueButton();
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
		websiteManager.addAddressPage.clickContinueButton();
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
		websiteManager.contactDetailsPage.clickContinueButton();
	}

	@When("^the user invites the business$")
	public void the_user_invites_the_business() throws Throwable {
		LOG.info("Send invitation to the user.");
		websiteManager.accountInvitePage.clickContinueButton();
	}

	@When("^the user clicks the save button without accepting the terms and conditions$")
	public void the_user_clicks_the_save_button_without_accepting_the_terms_and_conditions() throws Throwable {
		LOG.info("Not accepting the partnership terms and conditions.");
		websiteManager.checkPartnershipInformationPage.deselectConfirmationCheckbox();
		websiteManager.checkPartnershipInformationPage.clickSaveButton();
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
		websiteManager.checkPartnershipInformationPage.clickSaveButton();
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
		websiteManager.declarationPage.clickContinueButton();
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
		websiteManager.aboutTheOrganisationPage.clickContinueButton();
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
		websiteManager.addAddressPage.clickContinueButton();
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
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.CONTACT_NOTES, data.get("ContactNote"));
		}
		
		websiteManager.contactDetailsPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		websiteManager.contactDetailsPage.clickContinueButton();
	}

	@When("^the user confirms the sic code$")
	public void the_user_confirms_the_sic_code() throws Throwable {
		LOG.info("Selecting SIC Code");
		DataStore.saveValue(UsableValues.SIC_CODE, "allow people to eat");
		websiteManager.sicCodePage.selectSICCode(DataStore.getSavedValue(UsableValues.SIC_CODE));
		websiteManager.sicCodePage.clickContinueButton();
	}

	@When("^the user does not confirm the number of employees$")
	public void the_user_does_not_confirm_the_number_of_employees() throws Throwable {
		LOG.info("Not selecting a number of employees option.");
	    websiteManager.employeesPage.clickContinueButton();
	}

	@When("^the user confirms the number of employees$")
	public void the_user_confirms_the_number_of_employees() throws Throwable {
		LOG.info("Selecting No of Employees");
		DataStore.saveValue(UsableValues.NO_EMPLOYEES, "50 to 249");
		websiteManager.employeesPage.selectNumberOfEmployees(DataStore.getSavedValue(UsableValues.NO_EMPLOYEES));
		websiteManager.employeesPage.clickContinueButton();
	}
	
	@When("^the user confirms the size of the membership list as \"([^\"]*)\"$")
	public void the_user_confirms_the_size_of_the_membership_list_as(String size) throws Throwable {
		LOG.info("Selecting Membership List size");
		DataStore.saveValue(UsableValues.MEMBERLIST_SIZE, size);
		websiteManager.memberListPage.selectMemberSize(DataStore.getSavedValue(UsableValues.MEMBERLIST_SIZE));
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
		websiteManager.tradingPage.clickContinueButton();
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
		websiteManager.legalEntityTypePage.clickContinueButton();
	}

	@When("^the user confirms the legal entity$")
	public void the_user_confirms_the_legal_entity() throws Throwable {
		LOG.info("Confirm the Legal Entity.");
		websiteManager.legalEntityReviewPage.clickContinueButton();
	}

	@When("^the user does not confirm they have read the terms and conditions$")
	public void the_user_does_not_confirm_they_have_read_the_terms_and_conditions() throws Throwable {
		LOG.info("Not accepting the partnership terms and conditions.");
		websiteManager.checkPartnershipInformationPage.deselectOrganisationConfirmationCheckbox();
		websiteManager.checkPartnershipInformationPage.clickSaveButton();
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
		websiteManager.checkPartnershipInformationPage.clickSaveButton();
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
		websiteManager.declarationPage.clickContinueButton();
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
		websiteManager.regulatoryFunctionPage.selectContinueButton();
		
		LOG.info("Complete the Partnership Approval.");
		websiteManager.partnershipApprovalPage.clickDoneButton();
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
		websiteManager.revokePage.clickRevokeButton();
		
		websiteManager.partnershipRevokedPage.clickDoneButton();
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
		websiteManager.reinstatePage.clickContinueButton();
		websiteManager.partnershipRestoredPage.clickDoneButton();
	}

	@Then("^the partnership is restored successfully$")
	public void the_partnership_is_restored_successfully() throws Throwable {
		LOG.info("Check status of partnership is set to Active.");
		assertTrue(websiteManager.partnershipAdvancedSearchPage.checkPartnershipStatus("Active"));
	}
	
	@When("^the user does not update the information about the partnership field empty$")
	public void the_user_does_not_update_the_information_about_the_partnership_field_empty() throws Throwable {
		LOG.info("Leaving the Infdormation about the Partnership field empty.");
		websiteManager.partnershipInformationPage.editAboutPartnership();
		
		websiteManager.parPartnershipDescriptionPage.enterDescription("");
		websiteManager.parPartnershipDescriptionPage.clickSaveButton();
	}
	
	@When("^the user enters the following information about the partnership \"([^\"]*)\"$")
	public void the_user_enters_the_following_information_about_the_partnership(String information) throws Throwable {
		LOG.info("Entering information about the partnership.");
		DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, information);
		
		websiteManager.parPartnershipDescriptionPage.enterDescription(DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO));
		websiteManager.parPartnershipDescriptionPage.clickSaveButton();
	}

	@Then("^the information about the partnership is updated successfully$")
	public void the_information_about_the_partnership_is_updated_successfully() throws Throwable {
		LOG.info("Verifying About the Partnership have been updated Successfully.");
		
		assertTrue(websiteManager.partnershipInformationPage.verifyAboutThePartnership());
	}

	@When("^the user updates the partnership to bespoke but does not choose the regulatory functions$")
	public void the_user_updates_the_partnership_to_bespoke_but_does_not_choose_the_regulatory_functions() throws Throwable {
		LOG.info("Selecting the Bespoke Radio but not selecting the Regulatory Functions checkbox.");
		
		websiteManager.partnershipInformationPage.editRegulatoryFunctions();
		websiteManager.regulatoryFunctionPage.deselectBespokeFunctions();
		websiteManager.regulatoryFunctionPage.selectSaveButton();
	}

	@When("^the user updates the regulatory function$")
	public void the_user_updates_the_regulatory_function() throws Throwable {
		LOG.info("Selecting the Bespoke Regulatory Functions.");
		DataStore.saveValue(UsableValues.PARTNERSHIP_REGFUNC, "Alphabet learning");
		websiteManager.regulatoryFunctionPage.selectBespokeFunctions();
		websiteManager.regulatoryFunctionPage.selectSaveButton();
	}

	@Then("^the regulatory function is updated successfully$")
	public void the_regulatory_function_is_updated_successfully() throws Throwable {
		LOG.info("Verifying the Regulatory Functions have been updated Successfully.");
		
		assertTrue(websiteManager.partnershipInformationPage.checkRegulatoryFunctions());
		websiteManager.partnershipInformationPage.clickSaveButton();
	}

	@When("^the user leaves the organisation address fields empty$")
	public void the_user_leaves_the_organisation_address_fields_empty() throws Throwable {
		LOG.info("Leave all Address fields empty.");
		websiteManager.partnershipInformationPage.editOrganisationAddress();
		websiteManager.addAddressPage.clearAddressFields();
		websiteManager.addAddressPage.clickSaveButton();
	}

	@When("^the user updates the address with the following details:$")
	public void the_user_updates_the_address_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Updating the address.");
		
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
		websiteManager.addAddressPage.clickSaveButton();
	}
	
	@Then("^the organisation address is updated successfully$")
	public void the_organisation_address_is_updated_successfully() throws Throwable {
		LOG.info("Verifying the Organisation Address has been updated Successfully.");

		assertTrue(websiteManager.partnershipInformationPage.checkOrganisationAddress());
	}

	@When("^the user does not update the about the organisation field empty$")
	public void the_user_does_not_update_the_about_the_organisation_field_empty() throws Throwable {
		LOG.info("Leaving the Information about the Organisation field empty.");
		
		websiteManager.partnershipInformationPage.editAboutTheOrganisation();
		websiteManager.parPartnershipDescriptionPage.updateBusinessDescription("");
		websiteManager.parPartnershipDescriptionPage.clickSaveButton();
	}

	@When("^the user updates the organisation information with the following: \"([^\"]*)\"$")
	public void the_user_updates_the_organisation_information_with_the_following(String information) throws Throwable {
		LOG.info("Updating the Information about the Organisation.");
		DataStore.saveValue(UsableValues.BUSINESS_DESC, information);
		websiteManager.parPartnershipDescriptionPage.updateBusinessDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		websiteManager.parPartnershipDescriptionPage.clickSaveButton();
	}

	@Then("^the information about the organisation is updated successfully$")
	public void the_information_about_the_organisation_is_updated_successfully() throws Throwable {
		LOG.info("Verifying all the remaining Partnership details have been updated Successfully.");
		
		assertTrue(websiteManager.partnershipInformationPage.checkAboutTheOrganisation());
	}

	@When("^the user clicks the add another trading name link but leaves the text field empty$")
	public void the_user_clicks_the_add_another_trading_name_link_but_leaves_the_text_field_empty() throws Throwable {
		LOG.info("Adding a new Trading Name but leaving the field empty.");
		
		websiteManager.partnershipInformationPage.addAnotherTradingName();
		websiteManager.tradingPage.enterTradingName("");
		websiteManager.tradingPage.clickSaveButton();
	}

	@When("^the user enters a new trading name: \"([^\"]*)\"$")
	public void the_user_enters_a_new_trading_name(String tradingName) throws Throwable {
		LOG.info("Adding a new Trading Name.");
		DataStore.saveValue(UsableValues.TRADING_NAME, tradingName);
		websiteManager.tradingPage.enterTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
		websiteManager.tradingPage.clickSaveButton();
	}

	@Then("^the new trading name is added successfully$")
	public void the_new_trading_name_is_added_successfully() throws Throwable {
		LOG.info("Verifying the Trading Name was added Successfully.");
		
		assertTrue(websiteManager.partnershipInformationPage.verifyTradingName());
	}

	@When("^the user edits the trading name but leaves the text field empty$")
	public void the_user_edits_the_trading_name_but_leaves_the_text_field_empty() throws Throwable {
		LOG.info("Updating a Trading Name but leaving the field empty.");
		
		websiteManager.partnershipInformationPage.editTradingName();
		websiteManager.tradingPage.enterTradingName("");
		websiteManager.tradingPage.clickSaveButton();
	}

	@When("^the user updates the trading name: \"([^\"]*)\"$")
	public void the_user_updates_the_trading_name(String tradingName) throws Throwable {
		LOG.info("Updating a Trading Name.");
		
		DataStore.saveValue(UsableValues.TRADING_NAME, tradingName);
		websiteManager.tradingPage.enterTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
		websiteManager.tradingPage.clickSaveButton();
	}

	@Then("^the trading name is updated successfully$")
	public void the_trading_name_is_updated_successfully() throws Throwable {
		LOG.info("Verifying the Trading Name was updated Successfully.");
		
		assertTrue(websiteManager.partnershipInformationPage.verifyTradingName());
		websiteManager.partnershipInformationPage.clickSaveButton();
	}
	
	@When("^the user does not choose the type of legal entity$")
	public void the_user_does_not_choose_the_type_of_legal_entity() throws Throwable {
		LOG.info("Not selecting a Legal Entity type.");
		websiteManager.partnershipInformationPage.selectAmendLegalEntitiesLink();
		websiteManager.legalEntityTypePage.clickContinueButton();
	}

	@When("^the user chooses \"([^\"]*)\" legal entity type but does not enter the number$")
	public void the_user_chooses_legal_entity_type_but_does_not_enter_the_number(String type) throws Throwable {
		LOG.info("Choosing a Legal Entity type but not entering its Registration number.");
		websiteManager.legalEntityTypePage.selectLegalEntityType(type);
		websiteManager.legalEntityTypePage.clickContinueButton();
	}

	@When("^the user chooses the \"([^\"]*)\" legal entity type but does not choose the structure or enter the name$")
	public void the_user_chooses_the_legal_entity_type_but_does_not_choose_the_structure_or_enter_the_name(String type) throws Throwable {
		LOG.info("Choosing a Legal Entity trype but not selecting its Structure or entering its name.");
		websiteManager.legalEntityTypePage.selectLegalEntityType(type);
		websiteManager.legalEntityTypePage.clickContinueButton();
	}

	@When("^the user chooses the unregistered entity structure but does not enter the name$")
	public void the_user_chooses_the_unregistered_entity_structure_but_does_not_enter_the_name() throws Throwable {
		LOG.info("Choosing a Legal Entity trype and selecting its Structure but not entering its name.");
		DataStore.saveValue(UsableValues.ENTITY_TYPE, "Sole trader");
		
		websiteManager.legalEntityTypePage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), "");
		websiteManager.legalEntityTypePage.clickContinueButton();
	}

	@When("^the user adds a legal entity amendment with the name: \"([^\"]*)\"$")
	public void the_user_adds_a_legal_entity_amendment_with_the_name(String entityName) throws Throwable {
		LOG.info("Choosing a Legal Entity trype, selecting its Structure and entering its name.");
		DataStore.saveValue(UsableValues.ENTITY_NAME, "Error Message Testing Co.");
		
		websiteManager.legalEntityTypePage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		websiteManager.legalEntityTypePage.clickContinueButton();
		websiteManager.legalEntityReviewPage.clickContinueButton();
	}
	
	@When("^the user selects the confirm amendments link$")
	public void the_user_selects_the_confirm_amendments_link() throws Throwable {
		LOG.info("Selecting the Confirm legal aentity amendments link.");
		websiteManager.partnershipInformationPage.selectConfirmLegalEntitiesLink();
	}
	
	@When("^the user clicks the nominate amendments link$")
	public void the_user_clicks_the_nominate_amendments_link() throws Throwable {
		LOG.info("Selecting the Nominate legal entity amendments link.");
		websiteManager.partnershipInformationPage.selectNominateLegalEntitiesLink();
	}

	@When("^the user does not confirm the amendment$")
	public void the_user_does_not_confirm_the_amendment() throws Throwable {
		LOG.info("Submitting the Legal Entity Amendments without confirming they are correct.");
		websiteManager.confirmThisAmendmentPage.deselectConfirmationCheckbox();
		websiteManager.confirmThisAmendmentPage.selectSubmitAmendmentButton();
	}

	@When("^the user confirms the legal entity amendment$")
	public void the_user_confirms_the_legal_entity_amendment() throws Throwable {
		LOG.info("Confirming the Legal Entity Amendments.");
		websiteManager.confirmThisAmendmentPage.selectConfirmationCheckbox();
		websiteManager.confirmThisAmendmentPage.selectSubmitAmendmentButton();
		
		websiteManager.amendmentCompletedPage.clickDoneButton();
	}
	
	@When("^the user nominates the legal entity amendment$")
	public void the_user_nominates_the_legal_entity_amendment() throws Throwable {
		LOG.info("Nominating the Legal Entity Amendments.");
		websiteManager.confirmThisAmendmentPage.selectConfirmationCheckbox();
		websiteManager.confirmThisAmendmentPage.goToAmendmentCompletedPage();
		
		websiteManager.amendmentCompletedPage.clickDoneButton();
	}
	
	@When("^the user selects the add another authority contact link$")
	public void the_user_selects_the_add_another_authority_contact_link() throws Throwable {
		LOG.info("Selecting the Add another authority contact link.");
		websiteManager.partnershipInformationPage.addAnotherAuthorityContactButton();
	}
	
	@When("^the user selects the add another organisation contact link$")
	public void the_user_selects_the_add_another_organisation_contact_link() throws Throwable {
		LOG.info("Selecting the Add another organisation contact link.");
		websiteManager.partnershipInformationPage.addAnotherOrganisationContactButton();
	}

	@When("^the user enters the following authority contact details:$")
	public void the_user_enters_the_following_authority_contact_details(DataTable details) throws Throwable {
		LOG.info("Entering the Authority contact details.");
		websiteManager.contactDetailsPage.addContactDetails(details);
		websiteManager.contactDetailsPage.selectRandomPreferredCommunication();
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.CONTACT_NOTES, data.get("ContactNotes"));
		}
		
		websiteManager.contactDetailsPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		websiteManager.contactDetailsPage.clickContinueButton();
		
		LOG.info("Reviewing the Contact Details.");
		websiteManager.profileReviewPage.clickSaveButton();
	}

	@Then("^the new contact is added successfully$")
	public void the_new_contact_is_added_successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact is added successfully.");
		
		String fullName = DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);
		String workNumber = DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER);
		String mobileNumber = DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER);
		String email = DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL).toLowerCase();
		String contactNote = DataStore.getSavedValue(UsableValues.CONTACT_NOTES);
		
		// Need to assert each element separately and run the test one at a time per assert to debug the Failure.
		
		assertTrue(fullName +", "+ workNumber +", "+ mobileNumber +","+ email +", "+ contactNote, websiteManager.partnershipInformationPage.checkContactDetails());
	}

	@When("^the user removes the new Primary Authority Contact$")
	public void the_user_removes_the_new_Primary_Authority_Contact() throws Throwable {
		LOG.info("Selecting the Remove contact link.");
		websiteManager.partnershipInformationPage.removeContactsDetailsButton();
		
		LOG.info("Removing the contact.");
		websiteManager.removePage.clickRemoveButton();
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
	
	@When("^the user selects the see all advice link$")
	public void the_user_selects_the_see_all_advice_link() throws Throwable {
		LOG.info("Navigate to the See All Advice page.");
		websiteManager.partnershipAdvancedSearchPage.selectPartnershipLink();
		websiteManager.partnershipInformationPage.selectSeeAllAdviceNotices();
	}

	@When("^the user selects upload without choosing a file$")
	public void the_user_selects_upload_without_choosing_a_file() throws Throwable {
		LOG.info("Navigate to the Upload Advice Documents page.");
		websiteManager.adviceNoticeSearchPage.selectUploadLink();
		
		LOG.info("Select the Upload button without Uploading a file to receive an Error Message.");
		websiteManager.uploadAdviceNoticePage.selectUploadButton();
	}

	@When("^the user uploads an advice file$")
	public void the_user_uploads_an_advice_file() throws Throwable {
		LOG.info("Upload an Advice document.");
		websiteManager.uploadAdviceNoticePage.chooseFile("link.txt");
		websiteManager.uploadAdviceNoticePage.selectUploadButton();
	}

	@When("^the user does not enter advice details$")
	public void the_user_does_not_enter_advice_details() throws Throwable {
		LOG.info("Select the Save button without entering Advice Details receive an Error Message.");
		websiteManager.adviceNoticeDetailsPage.clearAllFields();
		websiteManager.adviceNoticeDetailsPage.selectSaveButton();
	}

	@When("^the user enters the following advice details:$")
	public void the_user_enters_the_following_advice_details(DataTable details) throws Throwable {
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ADVICENOTICE_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_TYPE, data.get("Type of Advice"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_REGFUNCTION, data.get("Reg Function"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.adviceNoticeDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		websiteManager.adviceNoticeDetailsPage.selectAdviceType(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TYPE));
		websiteManager.adviceNoticeDetailsPage.selectRegulatoryFunction(DataStore.getSavedValue(UsableValues.ADVICENOTICE_REGFUNCTION));
		websiteManager.adviceNoticeDetailsPage.enterDescription(DataStore.getSavedValue(UsableValues.ADVICENOTICE_DESCRIPTION));
		websiteManager.adviceNoticeDetailsPage.selectSaveButton();
	}

	@Then("^the advice is created successfully$")
	public void the_advice_is_created_successfully() throws Throwable {
		LOG.info("Verify the Advice was created successfully and has an Active status.");
		Assert.assertTrue(websiteManager.adviceNoticeSearchPage.getAdviceStatus().equalsIgnoreCase("Active"));
	}
	
	@When("^the user selects the edit link$")
	public void the_user_selects_the_edit_link() throws Throwable {
		LOG.info("Searching for the newly added Advice notice.");
		
		websiteManager.adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		websiteManager.adviceNoticeSearchPage.selectEditAdviceButton();
	}

	@Then("^the advice is updated successfully$")
	public void the_advice_is_updated_successfully() throws Throwable {
		LOG.info("Verify the Advice was updated successfully and has an Active status.");
		Assert.assertTrue("Failed: Status not set to \"Active\"", websiteManager.adviceNoticeSearchPage.getAdviceStatus().equalsIgnoreCase("Active"));
	}
	
	@When("^the user selects the archive link$")
	public void the_user_selects_the_archive_link() throws Throwable {
		LOG.info("Select the Archiving Advice Link.");
		
		websiteManager.adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		websiteManager.adviceNoticeSearchPage.selectArchiveAdviceButton();
	}

	@When("^the user does not enter a reason for archiving$")
	public void the_user_does_not_enter_a_reason_for_archiving() throws Throwable {
		LOG.info("LEave the Archive Reason empty and click Save.");
		websiteManager.adviceArchivePage.enterArchiveReason("");
		websiteManager.adviceArchivePage.selectSaveButton();
	}

	@When("^the user enters a reason for archiving the advice$")
	public void the_user_enters_a_reason_for_archiving_the_advice() throws Throwable {
		LOG.info("LEave the Archive Reason empty and click Save.");
		websiteManager.adviceArchivePage.enterArchiveReason("Sad Path Testing.");
		websiteManager.adviceArchivePage.selectSaveButton();
	}

	@Then("^the advice is archived successfully$")
	public void the_advice_is_archived_successfully() throws Throwable {
		LOG.info("Verify the Advice was Archived successfully and has the Archived status.");
		Assert.assertTrue(websiteManager.adviceNoticeSearchPage.getAdviceStatus().equalsIgnoreCase("Archived"));
	}
	
	@When("^the user selects the remove link$")
	public void the_user_selects_the_remove_link() throws Throwable {
		LOG.info("Select the Remove Advice Link.");
		
		websiteManager.adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		websiteManager.adviceNoticeSearchPage.selectRemoveAdviceButton();
	}

	@When("^the user does not enter a reason for removing$")
	public void the_user_does_not_enter_a_reason_for_removing() throws Throwable {
		LOG.info("Leave the remove text field empty and click the save button.");
		websiteManager.removePage.enterRemoveReason("");
		websiteManager.removePage.selectRemoveButton();
	}

	@When("^the user enters a reason for removing the advice$")
	public void the_user_enters_a_reason_for_removing_the_advice() throws Throwable {
		LOG.info("Enter a reason to remove the Advice.");
		websiteManager.removePage.enterRemoveReason("Sad Path Testing.");
		websiteManager.removePage.selectRemoveButton();
	}

	@Then("^the advice is removed successfully$")
	public void the_advice_is_removed_successfully() throws Throwable {
		LOG.info("Verify the Advice was Removed Successfully.");
		websiteManager.adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		
		Assert.assertTrue("Failed: Advice Notice was not Removed.", websiteManager.adviceNoticeSearchPage.checkNoResultsReturned());
	}
	
	@When("^the user selects the see all inspection plans link$")
	public void the_user_selects_the_see_all_inspection_plans_link() throws Throwable {
		LOG.info("Navigate to the See All Inspection Plans page.");
		websiteManager.partnershipAdvancedSearchPage.selectPartnershipLink();
		websiteManager.partnershipInformationPage.selectSeeAllInspectionPlans();
	}
	
	@When("^the user selects the upload inspection plan link$")
	public void the_user_selects_the_upload_inspection_plan_link() throws Throwable {
		LOG.info("Navigate to the Upload Inspection Plan page.");
		websiteManager.inspectionPlanSearchPage.selectUploadLink();
	}
	
	@When("^the user attempts to upload an inspection plan without choosing a file$")
	public void the_user_attempts_to_upload_an_inspection_plan_without_choosing_a_file() throws Throwable {
		LOG.info("Select the Upload Button without uploading an Inspection Plan Document.");
		websiteManager.uploadInspectionPlanPage.selectUploadButton();
	}

	@When("^the user uploads an inspection plan file$")
	public void the_user_uploads_an_inspection_plan_file() throws Throwable {
		LOG.info("Upload an Inspection Plan Document.");
		websiteManager.uploadInspectionPlanPage.chooseFile("link.txt");
		websiteManager.uploadInspectionPlanPage.selectUploadButton();
	}

	@When("^the user does not enter inspection plan details$")
	public void the_user_does_not_enter_inspection_plan_details() throws Throwable {
		LOG.info("Leave the Inspection Plan details fields empty and click the Save button.");
		websiteManager.inspectionPlanDetailsPage.clearAllFields();
		websiteManager.inspectionPlanDetailsPage.selectSaveButton();
	}

	@When("^the user enters the following inspection plan details:$")
	public void the_user_enters_the_following_inspection_plan_details(DataTable details) throws Throwable {
		LOG.info("Enter Inspection Plan details.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.inspectionPlanDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE));
		websiteManager.inspectionPlanDetailsPage.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
		websiteManager.inspectionPlanDetailsPage.selectSaveButton();
	}

	@When("^the user does not enter an expiry date$")
	public void the_user_does_not_enter_an_expiry_date() throws Throwable {
		LOG.info("Leave the Inpsection Plan Expiry date fields empty and click the Save button.");
		websiteManager.enterTheDatePage.clearDateFields();
		websiteManager.enterTheDatePage.selectSaveButton();
	}
	
	@When("^the user enters an inspection plan expiry date$")
	public void the_user_enters_an_inspection_plan_expiry_date() throws Throwable {
		LOG.info("Enter Inspection Plan Expiry Date.");
		websiteManager.enterTheDatePage.enterDate("ddMMYYYY");
		websiteManager.enterTheDatePage.selectSaveButton();
	}

	@Then("^the inspection plan is created successfully$")
	public void the_inspection_plan_is_created_successfully() throws Throwable {
		LOG.info("Verifying the Inpsection Plan was created successfully and has the Current status.");
		Assert.assertTrue("Failed: Inspection Plan Status is not set to Current.", websiteManager.inspectionPlanSearchPage.getPlanStatus().equalsIgnoreCase("Current"));
	}
	
	@When("^the user clicks the edit link$")
	public void the_user_clicks_the_edit_link() throws Throwable {
		LOG.info("Select the Inspection Plans Edit link.");
		websiteManager.inspectionPlanSearchPage.selectEditLink();
	}
	
	@When("^the user clicks the revoke link$")
	public void the_user_clicks_the_revoke_link() throws Throwable {
		LOG.info("Select the Inspection Plans Revoke link.");
		websiteManager.inspectionPlanSearchPage.selectRevokeLink();
	}

	@When("^the user does not enter a reason for revoking$")
	public void the_user_does_not_enter_a_reason_for_revoking() throws Throwable {
		LOG.info("Leave the revoke reason field empty and click the save button.");
		websiteManager.revokePage.enterReasonForRevocation("");
		websiteManager.revokePage.selectRevokeButton();
	}

	@When("^the user enters a reason to revoke the inspection plan$")
	public void the_user_enters_a_reason_to_revoke_the_inspection_plan() throws Throwable {
		LOG.info("Revoke the Inspection Plan.");
		websiteManager.revokePage.enterReasonForRevocation("Sad Path Test.");
		websiteManager.revokePage.selectRevokeButton();
	}
	
	@When("^the user clicks the remove link$")
	public void the_user_clicks_the_remove_link() throws Throwable {
		LOG.info("Select the Inspection Plans Remove link.");
		websiteManager.inspectionPlanSearchPage.selectRemoveLink();
	}

	@When("^the user enters a reason to remove the inspection plan$")
	public void the_user_enters_a_reason_to_remove_the_inspection_plan() throws Throwable {
		LOG.info("Remove the Inspection Plan.");
		websiteManager.removePage.enterRemoveReason("Sad Path Test.");
		websiteManager.removePage.selectRemoveButton();
	}

	@Then("^the inspection plan is removed successfully$")
	public void the_inspection_plan_is_removed_successfully() throws Throwable {
		LOG.info("Verifying the Inspection Plan was Removed Successfully.");
		assertEquals("Failed: Inspection Plan was not Removed.", websiteManager.inspectionPlanSearchPage.getPlanStatus(), "No results returned");
	}
	
	@When("^the user selects the Request to deviate from the inspection plan link$")
	public void the_user_selects_the_Request_to_deviate_from_the_inspection_plan_link() throws Throwable {
		LOG.info("Attempt to create a Deviation Request.");
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		websiteManager.partnershipInformationPage.clickDeviateInspectionPlan();
		websiteManager.chooseAnInspectionPlanPage.selectContinueButton();
	}
	
	@When("^the user selects the Submit feedback following an inspection link$")
	public void the_user_selects_the_Submit_feedback_following_an_inspection_link() throws Throwable {
		LOG.info("Attempt to send Inspection Feedback.");
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		websiteManager.partnershipInformationPage.clickSendInspectionFeedbk();
		websiteManager.chooseAnInspectionPlanPage.selectContinueButton();
	}
	
	@When("^the user clicks the Send a notification of a proposed enforcement action link$")
	public void the_user_clicks_the_Send_a_notification_of_a_proposed_enforcement_action_link() throws Throwable {
		LOG.info("Navigate to the Partnership.");
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		
		LOG.info("Start the Enforcement Notice Creation.");
		websiteManager.partnershipInformationPage.createEnforcement();
		websiteManager.enforcementNotificationPage.clickContinueButton();
	}

	@When("^the user leaves the enforcement officer contact detail fields empty$")
	public void the_user_leaves_the_enforcement_officer_contact_detail_fields_empty() throws Throwable {
		LOG.info("Leave the Enforcement Officer contact details empty.");
		websiteManager.enforcementOfficerContactDetailsPage.clearAllFIelds();
		websiteManager.enforcementOfficerContactDetailsPage.clickContinueButton();
	}

	@When("^the user enters the following enforcement officer contact details:$")
	public void the_user_enters_the_following_enforcement_officer_contact_details(DataTable details) throws Throwable {
	    LOG.info("Enter the Enforcement Officer contact details.");
	    websiteManager.enforcementOfficerContactDetailsPage.setContactDetails(details);
	    websiteManager.enforcementOfficerContactDetailsPage.enterFirstname(DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME));
	    websiteManager.enforcementOfficerContactDetailsPage.enterLastname(DataStore.getSavedValue(UsableValues.PERSON_LASTNAME));
	    websiteManager.enforcementOfficerContactDetailsPage.enterWorkPhoneNumber(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		websiteManager.enforcementOfficerContactDetailsPage.clickContinueButton();
	}

	@When("^the user does not enter the name of the legal entity$")
	public void the_user_does_not_enter_the_name_of_the_legal_entity() throws Throwable {
		LOG.info("Leave the Legal Entity field empty.");
		websiteManager.enforceLegalEntityPage.enterLegalEntityName("");
		websiteManager.enforceLegalEntityPage.clickContinueButton();
	}

	@When("^the user enters the name of the legal entity$")
	public void the_user_enters_the_name_of_the_legal_entity() throws Throwable {
		LOG.info("Enter the Legal Entity name.");
		websiteManager.enforceLegalEntityPage.enterLegalEntityName(DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		websiteManager.enforceLegalEntityPage.clickContinueButton();
	}

	@When("^the user does not provide a summary of the enforcement details$")
	public void the_user_does_not_provide_a_summary_of_the_enforcement_details() throws Throwable {
		LOG.info("Leave the Enforcement Notification summary field empty.");
		websiteManager.enforcementDetailsPage.enterEnforcementDescription("");
		websiteManager.enforcementDetailsPage.clickContinueButton();
	}
	
	@When("^the user enters a summary with the enforcement details:$")
	public void the_user_enters_a_summary_with_the_enforcement_details(DataTable details) throws Throwable {
		LOG.info("Choose the Enforcment action type and enter a summary.");
		websiteManager.enforcementDetailsPage.setEnforcementDetails(details);
		websiteManager.enforcementDetailsPage.selectEnforcementType(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TYPE));
		websiteManager.enforcementDetailsPage.enterEnforcementDescription(DataStore.getSavedValue(UsableValues.ENFORCEMENT_SUMMARY));
		websiteManager.enforcementDetailsPage.clickContinueButton();
	}

	@When("^the user leaves the enforcement action detail fields empty$")
	public void the_user_leaves_the_enforcement_action_detail_fields_empty() throws Throwable {
		LOG.info("Leave the Enforcement action fields empty.");
		websiteManager.enforcementActionPage.clearAllFields();
		websiteManager.enforcementActionPage.clickContinueButton();
	}

	@When("^the user enters the following details for an enforcement action:$")
	public void the_user_enters_the_following_details_for_an_enforcement_action(DataTable details) throws Throwable {
		LOG.info("Enter the Enforcment Action details.");
		websiteManager.enforcementActionPage.setEnforcementActionDetails(details);
		websiteManager.enforcementActionPage.enterTitle(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE));
	    websiteManager.enforcementActionPage.selectRegulatoryFunctions(DataStore.getSavedValue(UsableValues.ENFORCEMENT_REGFUNC));
		websiteManager.enforcementActionPage.enterEnforcementDescription(DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION).toLowerCase());
		websiteManager.enforcementActionPage.chooseFile(DataStore.getSavedValue(UsableValues.ENFORCEMENT_FILENAME));
		websiteManager.enforcementActionPage.clickContinueButton();
	}

	@Then("^the enforcement notice is created successfully$")
	public void the_enforcement_notice_is_created_successfully() throws Throwable {
	    LOG.info("Choose the Enforcment action type and enter a summary.");
	    Assert.assertTrue("Failed: Enforcement Details are not Correct.", websiteManager.enforcementReviewPage.checkEnforcementCreation());
		
		websiteManager.enforcementReviewPage.clickSaveButton();
		websiteManager.enforcementCompletionPage.clickDoneButton();
	}
	
	@When("^the user selects the block button without entering a reason to block$")
	public void the_user_selects_the_block_button_without_entering_a_reason_to_block() throws Throwable {
		LOG.info("Select Block Radio and leave the Reason field empty.");
		websiteManager.proposedEnforcementPage.selectBlock();
		websiteManager.proposedEnforcementPage.clickContinueButton();
	}
	
	@When("^the user selects the Remove enforcement action link$")
	public void the_user_selects_the_Remove_enforcement_action_link() throws Throwable {
		LOG.info("Selecting the Remove Enforcement action link..");
		websiteManager.enforcementSearchPage.removeEnforcement();
	}

	@When("^the user does not select a reason or enter a description for the removal$")
	public void the_user_does_not_select_a_reason_or_enter_a_description_for_the_removal() throws Throwable {
		LOG.info("Not selecting a Removal Reason or enterting an Removal Description..");
		websiteManager.removeEnforcementPage.clickContinueButton();
	}

	@When("^the user provides the following reason and a description for the removal:$")
	public void the_user_provides_the_following_reason_and_a_description_for_the_removal(DataTable details) throws Throwable {
		LOG.info("Entering the Enforcement Notice Removal reason and description.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.REMOVAL_REASON, data.get("RemovalReason"));
			DataStore.saveValue(UsableValues.REMOVAL_DESCRIPTION, data.get("RemovalDescription"));
		}
		
		websiteManager.removeEnforcementPage.selectRemovalReason(DataStore.getSavedValue(UsableValues.REMOVAL_REASON));
		websiteManager.removeEnforcementPage.enterRemovalDescription(DataStore.getSavedValue(UsableValues.REMOVAL_DESCRIPTION));
		websiteManager.removeEnforcementPage.clickContinueButton();
	}

	@When("^the user does not confirm they want to remove the enforcement notice$")
	public void the_user_does_not_confirm_they_want_to_remove_the_enforcement_notice() throws Throwable {
		LOG.info("Not Confirming the Enforcement Notice removal.");
		websiteManager.declarationPage.deselectConfirmCheckbox();
		websiteManager.declarationPage.clickContinueButton();
	}

	@When("^the user confirms they want the enforcement notice removing$")
	public void the_user_confirms_they_want_the_enforcement_notice_removing() throws Throwable {
		LOG.info("Confirming the Enforcement Notice removal.");
		websiteManager.declarationPage.selectConfirmCheckbox();
		websiteManager.declarationPage.goToEnforcementSearchPage(); // Test fails due to the button not being clicked properly, Selenium "hovers" on the button as it is shown as highlighted.
	}
	
	@When("^the user clicks the Request to deviate from the inspection plan link$")
	public void the_user_clicks_the_Request_to_deviate_from_the_inspection_plan_link() throws Throwable {
		LOG.info("Navigating to and select Send Deviation Request.");
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		websiteManager.partnershipInformationPage.selectDeviateInspectionPlan();
	}

	@When("^the user does not enter the deviation request details$")
	public void the_user_does_not_enter_the_deviation_request_details() throws Throwable {
		LOG.info("Leave the Description and File Upload empty.");
		websiteManager.requestDeviationPage.clearFields();
		websiteManager.requestDeviationPage.clickContinueButton();
	}

	@When("^the user enters the deviation request with the following details:$")
	public void the_user_enters_the_deviation_request_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Enter the Deviation Request details.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.DEVIATION_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.requestDeviationPage.enterDescription(DataStore.getSavedValue(UsableValues.DEVIATION_DESCRIPTION));
		websiteManager.requestDeviationPage.chooseFile("link.txt");
		websiteManager.requestDeviationPage.clickContinueButton();
	}

	@Then("^the deviation request is created successfully$")
	public void the_deviation_request_is_created_successfully() throws Throwable {
		LOG.info("Verify the Deviation Request is created Successfully.");
		
		Assert.assertTrue("Failed: Deviation Request details are not displayed.", websiteManager.deviationReviewPage.checkDeviationCreation());
		websiteManager.deviationReviewPage.clickSaveChanges();
		websiteManager.deviationCompletionPage.clickDoneButton();
	}
	
	@When("^the user selects blocks the deviation without providing a reason$")
	public void the_user_selects_blocks_the_deviation_without_providing_a_reason() throws Throwable {
		LOG.info("Block the deviation request without providing a reason.");
		
		websiteManager.deviationApprovalPage.selectBlock();
		websiteManager.deviationApprovalPage.clearAllFields();
		websiteManager.deviationApprovalPage.clickContinueButton();
	}
	
	
	@When("^the user selects the approved deviation request$")
	public void the_user_selects_the_approved_deviation_request() throws Throwable {
		LOG.info("Select the latest Deviation Request.");
		websiteManager.deviationSearchPage.selectDeviationRequest();
	}
	
	@When("^the user tries to submit a response without any details$")
	public void the_user_tries_to_submit_a_response_without_any_details() throws Throwable {
		LOG.info("Attempt to submit a Deviation Request response without any details.");
		websiteManager.deviationReviewPage.clickSubmitResponse();
		
		websiteManager.replyDeviationRequestPage.clearAllFields();
		websiteManager.replyDeviationRequestPage.clickSaveButton();
	}

	@When("^the user enters the following response: \"([^\"]*)\"$")
	public void the_user_enters_the_following_response(String response) throws Throwable {
		LOG.info("Submit reply to the deviation request.");
		DataStore.saveValue(UsableValues.MESSAGE_RESPONSE, response);
		
		websiteManager.replyDeviationRequestPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.MESSAGE_RESPONSE));
		websiteManager.replyDeviationRequestPage.chooseFile("link.txt");
		websiteManager.replyDeviationRequestPage.clickSaveButton();
	}
	
	@When("^the user clicks the Submit Feedback following an inspection link$")
	public void the_user_clicks_the_Submit_Feedback_following_an_inspection_link() throws Throwable {
		LOG.info("Submit Feedback following an Inspection.");
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		websiteManager.partnershipInformationPage.selectSendInspectionFeedbk();
	}

	@When("^the user does not enter inspection plan feedback$")
	public void the_user_does_not_enter_inspection_plan_feedback() throws Throwable {
		LOG.info("Leave feedback details fields empty.");
		websiteManager.inspectionFeedbackDetailsPage.clearAllFields();
		websiteManager.inspectionFeedbackDetailsPage.clickContinueButton();
	}

	@When("^the user enters the following inspection plan feedback \"([^\"]*)\"$")
	public void the_user_enters_the_following_inspection_plan_feedback(String feedback) throws Throwable {
		LOG.info("Complete feedback fields.");
		DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION, feedback);
		
		websiteManager.inspectionFeedbackDetailsPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION));
		websiteManager.inspectionFeedbackDetailsPage.chooseFile("link.txt");
		websiteManager.inspectionFeedbackDetailsPage.clickContinueButton();
	}

	@Then("^the inspection plan feedback is created successfully$")
	public void the_inspection_plan_feedback_is_created_successfully() throws Throwable {
		LOG.info("Verifying the Inspection Feedback Details.");
		Assert.assertTrue("Failed: Inspection Feedback Details are Incorrect.", websiteManager.inspectionFeedbackConfirmationPage.checkInspectionFeedback());
		
		websiteManager.inspectionFeedbackConfirmationPage.clickSaveButton();
		websiteManager.inspectionFeedbackCompletionPage.clickDoneButton();
	}
	
	@When("^the user clicks the Send a general enquiry to the primary authority link$")
	public void the_user_clicks_the_Send_a_general_enquiry_to_the_primary_authority_link() throws Throwable {
		LOG.info("Send a General Enquiry.");
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		websiteManager.partnershipInformationPage.sendGeneralEnquiry();
	}

	@When("^the user does not enter the general enquiry details$")
	public void the_user_does_not_enter_the_general_enquiry_details() throws Throwable {
		LOG.info("Leave the Enquiry detail fields empty.");
		websiteManager.requestEnquiryPage.clearAllFields();
		websiteManager.requestEnquiryPage.clickContinueButton();
	}

	@When("^the user enters the following general enquiry details \"([^\"]*)\"$")
	public void the_user_enters_the_following_general_enquiry_details(String description) throws Throwable {
		LOG.info("Enter Enquiry details.");
		DataStore.saveValue(UsableValues.ENQUIRY_DESCRIPTION, description);
		
		websiteManager.requestEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION));
		websiteManager.requestEnquiryPage.chooseFile("link.txt");
		websiteManager.requestEnquiryPage.clickContinueButton();
	}

	@Then("^the general enquiry is created successfully$")
	public void the_general_enquiry_is_created_successfully() throws Throwable {
		LOG.info("Verify the enquiry is created.");
		Assert.assertTrue("Failed: Enquiry details are not correct.", websiteManager.enquiryReviewPage.checkEnquiryDescription());
		
		websiteManager.enquiryReviewPage.clickSaveChanges();
		websiteManager.enquiryCompletionPage.clickDoneButton();
	}
	
	@When("^the user adds a new organisation member$")
	public void the_user_adds_a_new_organisation_member() throws Throwable {
		LOG.info("Add a Single Member Organisation to a Co-ordinated Partnership.");
		websiteManager.partnershipAdvancedSearchPage.selectOrganisationLink();
		websiteManager.partnershipInformationPage.selectShowMembersListLink();
		websiteManager.memberListPage.selectAddAMemberLink();
	}

	@When("^the user leaves the member organisation name field empty$")
	public void the_user_leaves_the_member_organisation_name_field_empty() throws Throwable {
		LOG.info("Clear the Member Organisation Text Field.");
		websiteManager.addOrganisationNamePage.clearOrganisationNameField();
		websiteManager.addOrganisationNamePage.clickContinueButton();
	}

	@When("^the user enters the following organisation name \"([^\"]*)\"$")
	public void the_user_enters_the_following_organisation_name(String name) throws Throwable {
		LOG.info("Entering the Member Organisation's Name.");
		DataStore.saveValue(UsableValues.MEMBER_ORGANISATION_NAME, name);
		websiteManager.addOrganisationNamePage.enterMemberName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		websiteManager.addOrganisationNamePage.clickContinueButton();
	}

	@When("^the user leaves the address fields empty$")
	public void the_user_leaves_the_address_fields_empty() throws Throwable {
		LOG.info("Clear the Member Organisation Address Text Fields.");
		websiteManager.authorityAddressDetailsPage.clearMemberAddressTextFields();
		websiteManager.authorityAddressDetailsPage.clickContinueButton();
	}

	@When("^the user leaves the membership start date fields empty$")
	public void the_user_leaves_the_membership_start_date_fields_empty() throws Throwable {
		LOG.info("Clear the Membership Start Date.");
		websiteManager.enterTheDatePage.clearDateFields();
		websiteManager.enterTheDatePage.clickContinueButton();
	}

	@When("^the user enters a membership start date$")
	public void the_user_enters_a_membership_start_date() throws Throwable {
		LOG.info("Entering the Member Organisation's Membership Start Date.");
		websiteManager.enterTheDatePage.enterCurrentDate();
		websiteManager.enterTheDatePage.clickContinueButton();
	}
	
	@When("^the user leaves the member trading name field empty$")
	public void the_user_leaves_the_member_trading_name_field_empty() throws Throwable {
		LOG.info("Clear the Member Organisation's Trading Name Text Field.");
		websiteManager.tradingPage.enterTradingNameForMember("");
		websiteManager.tradingPage.clickContinueButton();
	}

	@When("^the user enters the member trading name \"([^\"]*)\"$")
	public void the_user_enters_the_member_trading_name(String name) throws Throwable {
		LOG.info("Entering the Member's Trading Name.");
		DataStore.saveValue(UsableValues.TRADING_NAME, name);
		websiteManager.tradingPage.enterTradingNameForMember(DataStore.getSavedValue(UsableValues.TRADING_NAME));
		websiteManager.tradingPage.clickContinueButton();
	}
	
	@When("^the user confirms the legal entity for the new member$")
	public void the_user_confirms_the_legal_entity_for_the_new_member() throws Throwable {
		LOG.info("Confirm the Legal Entity.");
		websiteManager.legalEntityReviewPage.clickContinueButton();
	}

	@When("^the user confirms the addition of the new member organisation$")
	public void the_user_confirms_the_addition_of_the_new_member_organisation() throws Throwable {
		LOG.info("Confirming the Member Organisation is covered by the Inspection Plan.");
		websiteManager.inspectionPlanCoveragePage.selectYesRadial();
		websiteManager.inspectionPlanCoveragePage.selectContinueButton();
		
		LOG.info("Saving the Member Organisation's Details.");
		websiteManager.memberOrganisationSummaryPage.clickSaveButton();
		websiteManager.memberOrganisationAddedConfirmationPage.clickDoneButton();
	}

	@Then("^the new member organistion has been added to the partnership successfully$")
	public void the_new_member_organistion_has_been_added_to_the_partnership_successfully() throws Throwable {
		LOG.info("Verify the Member Organisation was added to the Co-ordinated Partnership Successfully.");
		
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		Assert.assertTrue("Failed: Member Organisation was not Created.", websiteManager.memberListPage.checkMemberCreated());
	}
	
	@When("^the user searches for the last created organisation member$")
	public void the_user_searches_for_the_last_created_organisation_member() throws Throwable {
		LOG.info("Navigate to the Member summary page for a Member Organisation.");
		websiteManager.partnershipAdvancedSearchPage.selectOrganisationLink();
		websiteManager.partnershipInformationPage.selectShowMembersListLink();
		
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
	}

	@Then("^the user is on the organisation members summary page$")
	public void the_user_is_on_the_organisation_members_summary_page() throws Throwable {
		LOG.info("Verify the user is on the Member Summary page.");
		websiteManager.memberListPage.selectMembersName();
		Assert.assertTrue("Failed: Member Organisation was not Created.", websiteManager.memberOrganisationSummaryPage.checkMemberOrganisationSummaryPage());
	}

	@When("^the user selects the edit organisation name link$")
	public void the_user_selects_the_edit_organisation_name_link() throws Throwable {
		LOG.info("Select the Edit Member Organisation's Name Link.");
		websiteManager.memberOrganisationSummaryPage.selectEditOrganisationName();
	}

	@When("^the user leaves the organisation name text field empty$")
	public void the_user_leaves_the_organisation_name_text_field_empty() throws Throwable {
		LOG.info("Clear the Member Organisation's Name Text Field.");
		websiteManager.addOrganisationNamePage.clearOrganisationNameField();
		websiteManager.addOrganisationNamePage.clickSaveButton();
	}

	@When("^the user enters an organisation name \"([^\"]*)\"$")
	public void the_user_enters_an_organisation_name(String name) throws Throwable {
		LOG.info("Updating the Member Organisation's Name.");
		DataStore.saveValue(UsableValues.MEMBER_ORGANISATION_NAME, name);
		websiteManager.addOrganisationNamePage.enterMemberName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		websiteManager.addOrganisationNamePage.clickSaveButton();
	}

	@When("^the user selects the edit address link$")
	public void the_user_selects_the_edit_address_link() throws Throwable {
		LOG.info("Select the Edit Member Organisation's Address Link.");
		websiteManager.memberOrganisationSummaryPage.selectEditAddress();
	}

	@When("^the user leaves the organisation address text fields empty$")
	public void the_user_leaves_the_organisation_address_text_fields_empty() throws Throwable {
		LOG.info("Clear the Member Organisation's Address Fields.");
		websiteManager.addAddressPage.clearAddressFields();
		websiteManager.addAddressPage.clickSaveButton();
	}

	@When("^the user enters the following organisation addess:$")
	public void the_user_enters_the_following_organisation_addess(DataTable details) throws Throwable {
		LOG.info("Updating the Member Organisation's Address.");
		websiteManager.addAddressPage.editAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		
		websiteManager.addAddressPage.clickSaveButton();
	}

	@When("^the user selects the edit membership start date link$")
	public void the_user_selects_the_edit_membership_start_date_link() throws Throwable {
		LOG.info("Select the Edit Member Organisation's Membership Start Date Link.");
		websiteManager.memberOrganisationSummaryPage.selectEditMembershipStartDate();
	}

	@When("^the user leaves the date fields empty$")
	public void the_user_leaves_the_date_fields_empty() throws Throwable {
		LOG.info("Clear the Member Organisation's Membership Start Date Fields.");
		websiteManager.enterTheDatePage.clearDateFields();
		websiteManager.enterTheDatePage.selectSaveButton();
	}
	
	@When("^the user edits the member organisation membership start date$")
	public void the_user_edits_the_member_organisation_membership_start_date() throws Throwable {
		LOG.info("Enter the Member Organisation's Membership Start Date.");
		websiteManager.enterTheDatePage.enterCurrentDate();
		websiteManager.enterTheDatePage.selectSaveButton();
	}

	@When("^the user selects the edit person link$")
	public void the_user_selects_the_edit_person_link() throws Throwable {
		LOG.info("Select the Edit Member Organisation's Contact Details Link.");
		websiteManager.memberOrganisationSummaryPage.selectEditPerson();
	}

	@When("^the user leaves the contact detail text fields empty$")
	public void the_user_leaves_the_contact_detail_text_fields_empty() throws Throwable {
		LOG.info("Clear the Member Organisation's Contact Details Fields.");
		websiteManager.contactDetailsPage.clearAllFields();
		websiteManager.contactDetailsPage.clickSaveButton();
	}

	@When("^the user enters the following contact details:$")
	public void the_user_enters_the_following_contact_details(DataTable details) throws Throwable {
		LOG.info("Updating the Member Organisation's Contact Details.");
		websiteManager.contactDetailsPage.addContactDetails(details);
		websiteManager.contactDetailsPage.clickSaveButton();
	}

	@When("^the user selects the add another legal entity link$")
	public void the_user_selects_the_add_another_legal_entity_link() throws Throwable {
		LOG.info("Select the Add Member Organisation's Legal Entity Link.");
		websiteManager.memberOrganisationSummaryPage.selectAddAnotherLegalEntity();
	}
	
	@When("^the user does not select a registered, charity or unregistered legal entity for the member$")
	public void the_user_does_not_select_a_registered_charity_or_unregistered_legal_entity_for_the_member() throws Throwable {
		LOG.info("Not selecting a Legal Entity type.");
		websiteManager.updateLegalEntityPage.clickSaveButton();
	}
	
	@When("^the user selects an \"([^\"]*)\" legal entity for the member$")
	public void the_user_selects_an_legal_entity_for_the_member(String type) throws Throwable {
	    LOG.info("Selecting a Legal Entity type.");
		websiteManager.updateLegalEntityPage.selectUnregisteredEntity(type, "");
	}
	
	@When("^the user does not select a legal entity type or enter a legal entity name for the member$")
	public void the_user_does_not_select_a_legal_entity_type_or_enter_a_legal_entity_name_for_the_member() throws Throwable {
		LOG.info("Entering a Legal Entity type but not selecting an entity structure or entering an entity name.");
		websiteManager.updateLegalEntityPage.clickSaveButton();
	}
	
	@When("^the user chooses a legal entity with the following details for the member:$")
	public void the_user_chooses_a_legal_entity_with_the_following_details_for_the_member(DataTable details) throws Throwable {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENTITY_NAME, data.get("Legal Entity Name"));
			DataStore.saveValue(UsableValues.ENTITY_TYPE, data.get("Legal Entity Type"));
		}
		
		LOG.info("Entering a Legal Entity.");
		websiteManager.updateLegalEntityPage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		websiteManager.updateLegalEntityPage.clickSaveButton();
	}

	@When("^the user selects the add another trading name link$")
	public void the_user_selects_the_add_another_trading_name_link() throws Throwable {
		LOG.info("Select the Edit Member Organisation's Trading Name.");
		websiteManager.memberOrganisationSummaryPage.selectEditTradingName();
	}

	@When("^the user leaves the trading name text field empty$")
	public void the_user_leaves_the_trading_name_text_field_empty() throws Throwable {
		LOG.info("Clear the Member Organisation's Trading Name Field.");
		websiteManager.tradingPage.clearTradingName();
		websiteManager.tradingPage.clickSaveButton();
	}

	@When("^the user enters the following trading name \"([^\"]*)\"$")
	public void the_user_enters_the_following_trading_name(String name) throws Throwable {
		LOG.info("Updating the Member Organisation's Trading Name.");
		DataStore.saveValue(UsableValues.MEMBER_ORGANISATION_NAME, name);
		websiteManager.tradingPage.editMemberTradingName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		websiteManager.tradingPage.clickSaveButton();
	}

	@Then("^the member organisation is updated successfully$")
	public void the_member_organisation_is_updated_successfully() throws Throwable {
		LOG.info("Verifying All Member Details are Correct.");
		Assert.assertTrue(websiteManager.memberOrganisationSummaryPage.checkMemberDetails());
		websiteManager.memberOrganisationSummaryPage.clickDoneButton();
		
		LOG.info("Verify the Updated Member Organisation Name is Displayed on the Members List.");
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		Assert.assertTrue("Failed: Member Organisation was not Updated.", websiteManager.memberListPage.checkMemberCreated());
	}
	
	@When("^the user selects the cease membership link$")
	public void the_user_selects_the_cease_membership_link() throws Throwable {
		LOG.info("Selecting the Cease membership link..");
		websiteManager.memberListPage.selectCeaseMembership();
	}

	@When("^the user leaves the memebrship cease date fields empty$")
	public void the_user_leaves_the_memebrship_cease_date_fields_empty() throws Throwable {
		LOG.info("Leaving the Date fields empty.");
		websiteManager.enterTheDatePage.clearDateFields();
		websiteManager.enterTheDatePage.clickContinueButton();
	}

	@When("^the user enter the membership cease date$")
	public void the_user_enter_the_membership_cease_date() throws Throwable {
		LOG.info("Entering the Current Date for the Cessation to Happen.");
		websiteManager.enterTheDatePage.enterCurrentDate();
		websiteManager.enterTheDatePage.clickContinueButton();
		
		websiteManager.membershipCeasedPage.clickCeaseButton();
	}

	@Then("^the organisations membership is cesased successfully$")
	public void the_organisations_membership_is_cesased_successfully() throws Throwable {
		LOG.info("Verify the Member Organisation has been Ceased Successfully.");
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		Assert.assertTrue("Failed: Links are still present.", websiteManager.memberListPage.checkMembershipActionButtons());
		Assert.assertEquals("Failed: Dates do not match.", DataStore.getSavedValue(UsableValues.MEMBERSHIP_CEASE_DATE), websiteManager.memberListPage.getMembershipCeasedDate());
	}
	
	@When("^the user selects the upload a member list link\\.$")
	public void the_user_selects_the_upload_a_member_list_link() throws Throwable {
		LOG.info("Navigate to the Member Organisation List Upload.");
		websiteManager.partnershipAdvancedSearchPage.selectOrganisationLink();
		websiteManager.partnershipInformationPage.selectShowMembersListLink();
		
		LOG.info("Select the Member Organisation List Upload Link.");
		websiteManager.memberListPage.selectUploadMembersListLink();
	}

	@When("^the user does not upload the member list csv file$")
	public void the_user_does_not_upload_the_member_list_csv_file() throws Throwable {
		LOG.info("Do not Uploade the Members List CSV File and click the Upload button.");
		websiteManager.uploadListOfMembersPage.clearCSVFile();
		websiteManager.uploadListOfMembersPage.clickUploadButton();
	}

	@When("^the user uploads a members list csv file$")
	public void the_user_uploads_a_members_list_csv_file() throws Throwable {
		LOG.info("Uploading the Members List CSV File.");
		websiteManager.uploadListOfMembersPage.chooseCSVFile();
		websiteManager.uploadListOfMembersPage.clickUploadButton();
		
		LOG.info("Confirm the Uploade of the Members List CSV File.");
		websiteManager.confirmMemberUploadPage.selectUploadButton();
		websiteManager.memberListUploadedPage.clickDoneButton();
	}

	@Then("^the members list was upload successfully$")
	public void the_members_list_was_upload_successfully() throws Throwable {
		LOG.info("Verify the Members List was Uploaded Successfully.");
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		Assert.assertTrue("FAILED: Business names are not displayed in the table.", websiteManager.memberListPage.checkMembersListUploaded());
	}
}
