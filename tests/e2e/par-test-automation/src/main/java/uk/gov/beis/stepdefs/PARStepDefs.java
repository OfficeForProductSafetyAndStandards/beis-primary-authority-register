package uk.gov.beis.stepdefs;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.io.IOException;
import java.util.Map;

import org.apache.commons.lang3.RandomStringUtils;
import org.junit.Assert;

import cucumber.api.DataTable;
import cucumber.api.java.en.Given;
import cucumber.api.java.en.Then;
import cucumber.api.java.en.When;
import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.helper.LOG;
import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;

import uk.gov.beis.pageobjects.WebsiteManager;

import uk.gov.beis.utility.DataStore;
import uk.gov.beis.utility.RandomStringGenerator;

public class PARStepDefs {

	private WebsiteManager websiteManager;
	
	public PARStepDefs() throws ClassNotFoundException, IOException {
		websiteManager = new WebsiteManager();
	}

	@Given("^the user is on the PAR home page$")
	public void the_user_is_on_the_PAR_home_page() throws Throwable {
		LOG.info("Navigating to PAR Home page but first accepting cookies if present");
		websiteManager.homePage.navigateToUrl();
	}

	@Given("^the user is on the PAR login page$")
	public void the_user_is_on_the_PAR_login_page() throws Throwable {
		LOG.info("Navigating to PAR login page - logging out user first if already logged in");
		websiteManager.loginPage.navigateToUrl();
	}

	@Given("^the user visits the login page$")
	public void the_user_wants_to_login() throws Throwable {
		websiteManager.homePage.selectLogin();
	}

	@When("^the user logs in with the \"([^\"]*)\" user credentials$")
	public void the_user_logs_in_with_the_user_credentials(String user) throws Throwable {
		DataStore.saveValue(UsableValues.LOGIN_USER, user);
		String pass = PropertiesUtil.getConfigPropertyValue(user);
		
		LOG.info("Logging in user with credentials; username: " + user + " and password " + pass);
		websiteManager.loginPage.enterLoginDetails(user, pass);
		websiteManager.loginPage.clickSignIn();
	}

	@Then("^the user is on the dashboard page$")
	public void the_user_is_on_the_dashboard_page() throws Throwable {
		LOG.info("Verify the user is on the Dashboard Page.");
		Assert.assertTrue("Failed: Dashboard Header was not found.", websiteManager.dashboardPage.checkPage());
	}
	
	@When("^the user accepts the analytics cookies$")
	public void the_user_accepts_the_analytics_cookies() throws Throwable {
		websiteManager.dashboardPage.acceptCookies();
	}

	@Then("^analytical cookies have been accepted successfully$")
	public void analytical_cookies_have_been_accepted_successfully() throws Throwable {
		LOG.info("Verifying the Analytical Cookies have been Accepted.");
		Assert.assertTrue("Failed: Analytics Cookies have not been Accepted.", websiteManager.dashboardPage.checkCookiesAccepted());
		
		websiteManager.dashboardPage.hideCookieBanner();
		
		LOG.info("Verifying the Cookie Banner is not Displayed.");
		Assert.assertTrue("Failed: The Cookie Banner is still Displayed.", websiteManager.dashboardPage.checkCookieBannerExists());
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
		websiteManager.dashboardPage.selectApplyForNewPartnership();
		
		LOG.info("Choose authority");
		websiteManager.parAuthorityPage.selectAuthority(authority);
		
		LOG.info("Select partnership type");
		websiteManager.parPartnershipTypePage.selectPartnershipType(type);
		
		LOG.info("Accepting terms");
		websiteManager.parPartnershipTermsPage.acceptTerms();
		
		LOG.info("Entering partnership description");
		websiteManager.parPartnershipDescriptionPage.enterDescription(DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO));
		websiteManager.parPartnershipDescriptionPage.gotToBusinessNamePage();
		
		LOG.info("Entering business/organisation name");
		websiteManager.businessNamePage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		websiteManager.businessNamePage.goToAddressPage();
		
		LOG.info("Enter address details");
		websiteManager.addAddressPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY),
				DataStore.getSavedValue(UsableValues.BUSINESS_NATION), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		websiteManager.addAddressPage.goToAddContactDetailsPage();
		
		LOG.info("Enter contact details");
		websiteManager.contactDetailsPage.addContactDetails(details);
		websiteManager.contactDetailsPage.goToInviteUserAccountPage();
		
		LOG.info("Send invitation to user");
		websiteManager.accountInvitePage.sendInvite();
	}

	@Then("^the first part of the partnership application is successfully completed$")
	public void the_first_part_of_the_partnership_application_is_successfully_completed() throws Throwable {
		LOG.info("Verifying Partnership Details on the Review Page.");
		
		Assert.assertTrue("About the Partnership is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyAboutThePartnership());
		Assert.assertTrue("Organisation Name is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyOrganisationName());
		Assert.assertTrue("Organisation Address is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyOrganisationAddress());
		Assert.assertTrue("Organisation Contact is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyContactAtTheOrganisation());
		Assert.assertTrue("Primary Authority name is not Displayed.", websiteManager.checkPartnershipInformationPage.verifyPrimaryAuthorityName());
		
		LOG.info("Complete Partnership Application.");
		websiteManager.checkPartnershipInformationPage.completeApplication();
		websiteManager.parPartnershipCompletionPage.clickDoneButton();
	}
	
	@When("^the user searches for the last created partnership$")
	public void the_user_searches_for_the_last_created_partnership() throws Throwable {
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
		case ("senior_administrator@example.com"):
		case ("secretary_state@example.com"):
			LOG.info("Selecting Search partnerships");
			websiteManager.helpDeskDashboardPage.selectSearchPartnerships();
			websiteManager.partnershipAdvancedSearchPage.searchPartnerships();
			break;
		case ("par_enforcement_officer@example.com"):
			LOG.info("Selecting Search for partnerships");
			websiteManager.dashboardPage.selectSearchforPartnership();
			websiteManager.partnershipSearchPage.searchPartnerships();
			break;
		case ("par_business_manager@example.com"):
		case ("par_business@example.com"):
			LOG.info("Selecting See your partnerships");
			websiteManager.dashboardPage.selectSeePartnerships();
			websiteManager.partnershipSearchPage.searchPartnerships();
			websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
			break;
		default:
			LOG.info("Search partnerships");
			websiteManager.dashboardPage.selectSeePartnerships();
			LOG.info("Select organisation link details");
			websiteManager.partnershipSearchPage.searchPartnerships();

			// select business/organisation link if still first part of journey
			if (!ScenarioContext.secondJourneyPart)
				websiteManager.partnershipSearchPage.selectBusinessNameLink();

			// select authority link if in second part of journey
			if (ScenarioContext.secondJourneyPart)
				websiteManager.partnershipSearchPage.selectAuthority(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
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
		websiteManager.declarationPage.selectConfirmCheckbox();
		websiteManager.declarationPage.goToBusinessDetailsPage();
		
		LOG.info("Add business description");
		websiteManager.aboutTheOrganisationPage.enterDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		websiteManager.aboutTheOrganisationPage.goToAddressPage();
		
		LOG.info("Confirming address details");
		websiteManager.addAddressPage.goToAddContactDetailsPage();
		
		LOG.info("Confirming contact details");
		websiteManager.contactDetailsPage.selectPreferredEmail();
		websiteManager.contactDetailsPage.selectPreferredWorkphone();
		websiteManager.contactDetailsPage.selectPreferredMobilephone();
		websiteManager.contactDetailsPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		websiteManager.contactDetailsPage.goToSICCodePage();
		
		LOG.info("Selecting SIC Code");
		websiteManager.sicCodePage.selectSICCode(DataStore.getSavedValue(UsableValues.SIC_CODE));
		
		switch (DataStore.getSavedValue(UsableValues.PARTNERSHIP_TYPE).toLowerCase()) {

		case ("direct"):
			LOG.info("Selecting No of Employees.");
			websiteManager.employeesPage.selectNoEmployees(DataStore.getSavedValue(UsableValues.NO_EMPLOYEES));
			break;

		case ("co-ordinated"):
			LOG.info("Selecting Membership List size.");
			websiteManager.memberListPage.selectMemberSize(DataStore.getSavedValue(UsableValues.MEMBERLIST_SIZE));
			break;
		}

		LOG.info("Entering business trading name.");
		websiteManager.tradingPage.enterTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
		websiteManager.tradingPage.goToLegalEntityTypePage();
		
		LOG.info("Entering a Legal Entity.");
		websiteManager.legalEntityTypePage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		websiteManager.legalEntityTypePage.goToLegalEntityReviewPage();
		
		websiteManager.legalEntityReviewPage.goToCheckPartnershipInformationPage();
		
		LOG.info("Set second part of journey part to true");
		ScenarioContext.secondJourneyPart = true;
	}
	
	@Then("^the second part of the partnership application is successfully completed$")
	public void the_second_part_of_the_partnership_application_is_successfully_completed() throws Throwable {
		LOG.info("Check and confirm changes");
		
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
	}
	
	@Then("^the partnership application is completed successfully$")
	public void the_partnership_application_is_completed_successfully() throws Throwable {
		LOG.info("Verifying Partnership Information is Displayed.");
		
		assertTrue("Failed: The Organisation Name is not Correct.", websiteManager.partnershipInformationPage.verifyOrganisationName());
		assertTrue("Failed: The Primary Authority name is not Correct.", websiteManager.partnershipInformationPage.verifyPrimaryAuthorityName());
		assertTrue("Failed: About the Partnership is not Correct.", websiteManager.partnershipInformationPage.verifyAboutThePartnership());
		
		assertTrue("Failed: Organisation Address is not Correct.", websiteManager.partnershipInformationPage.checkOrganisationAddress());
		assertTrue("Failed: About the Organisation is not Correct.", websiteManager.partnershipInformationPage.checkAboutTheOrganisation());
		assertTrue("Failed The SIC Code is not Correct.", websiteManager.partnershipInformationPage.checkSICCode());
		assertTrue("Failed: The Legal Entity is not Correct.", websiteManager.partnershipInformationPage.verifyLegalEntity("Confirmed by the Organisation"));
		assertTrue("Failed: The Trading Name is not Correct.", websiteManager.partnershipInformationPage.verifyTradingName());
		assertTrue("Failed: The Organisation Contact is not Correct.", websiteManager.partnershipInformationPage.verifyContactAtTheOrganisation());
	}
	
	@When("^the user approves the partnership$")
	public void the_user_approves_the_partnership() throws Throwable {
		LOG.info("Approving last created partnership");
		websiteManager.partnershipAdvancedSearchPage.selectApproveBusinessNameLink();
		
		websiteManager.declarationPage.selectAuthorisedCheckbox();
		websiteManager.declarationPage.goToRegulatoryFunctionsPage();
		
		websiteManager.regulatoryFunctionPage.selectNormalOrSequencedFunctions();
		websiteManager.regulatoryFunctionPage.goToPartnershipApprovedPage();
		
		websiteManager.partnershipApprovalPage.completeApplication();
	}

	@When("^the user searches again for the last created partnership$")
	public void the_user_searches_again_for_the_last_created_partnership() throws Throwable {
		LOG.info("Searching for last created partnership");
		websiteManager.partnershipAdvancedSearchPage.searchPartnerships();
	}
	
	@When("^the user revokes the partnership$")
	public void the_user_revokes_the_partnership() throws Throwable {
		LOG.info("Revoking last created partnership");
		websiteManager.partnershipAdvancedSearchPage.selectRevokeBusinessNameLink();
		
		websiteManager.revokePage.enterReasonForRevocation("Test Revoke.");
		websiteManager.revokePage.goToPartnershipRevokedPage();
		
		websiteManager.partnershipRevokedPage.goToAdvancedPartnershipSearchPage();
	}

	@When("^the user restores the partnership$")
	public void the_user_restores_the_partnership() throws Throwable {
		LOG.info("Restoring last revoked partnership");
		websiteManager.partnershipAdvancedSearchPage.selectRestoreBusinessNameLink();
		
		websiteManager.reinstatePage.goToPartnershipRestoredPage();
		websiteManager.partnershipRestoredPage.goToAdvancedPartnershipSearchPage();
	}

	@Then("^the partnership is displayed with Status \"([^\"]*)\" and Actions \"([^\"]*)\"$")
	public void the_partnership_is_displayed_with_Status_and_Actions(String status, String action) throws Throwable {
		LOG.info("Check status of partnership is: " + status + " and action is: " + action);
		websiteManager.partnershipAdvancedSearchPage.checkPartnershipDetails(status, action);
	}
	
	@When("^the user searches for the last created partnership Authority$")
	public void the_user_searches_for_the_last_created_partnership_Authority() throws Throwable {
		LOG.info("Searching for and selecting the latest Partnerships Primary Authority.");
		
		websiteManager.helpDeskDashboardPage.selectSearchPartnerships();
		
		websiteManager.partnershipAdvancedSearchPage.searchPartnerships();
		websiteManager.partnershipAdvancedSearchPage.selectPrimaryAuthorityLink();
	}
	
	@When("^the user updates the About the Partnership and Regulatory Functions:$")
	public void the_user_updates_the_About_the_Partnership_and_Regulatory_Functions(DataTable details) throws Throwable {
		LOG.info("Updating about the Partnership and Regulatory Functions.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.PARTNERSHIP_INFO, data.get("About the Partnership"));
		}
		
		websiteManager.partnershipInformationPage.editAboutPartnership();
		websiteManager.parPartnershipDescriptionPage.enterDescription(DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO));
		websiteManager.parPartnershipDescriptionPage.goToPartnershipInformationPage();
		
		websiteManager.partnershipInformationPage.editRegulatoryFunctions();
		websiteManager.regulatoryFunctionPage.updateRegFunction();
	}

	@Then("^the About the Partnership and Regulatory Functions are updated Successfully$")
	public void the_About_the_Partnership_and_Regulatory_Functions_are_updated_Successfully() throws Throwable {
		LOG.info("Verifying About the Partnership and Regulatory Functions have been updated Successfully.");
		
		assertTrue(websiteManager.partnershipInformationPage.verifyAboutThePartnership());
		assertTrue(websiteManager.partnershipInformationPage.checkRegulatoryFunctions());
		
		websiteManager.partnershipInformationPage.clickSave();
	}
	
	@When("^the user searches for the last created partnership Organisation$")
	public void the_user_searches_for_the_last_created_partnership_Organisation() throws Throwable {
		LOG.info("Searching for and selecting the latest Partnerships Organisation.");
		
		websiteManager.partnershipAdvancedSearchPage.searchPartnerships();
		websiteManager.partnershipAdvancedSearchPage.selectOrganisationLink();
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
		
		websiteManager.partnershipInformationPage.editOrganisationAddress();
		websiteManager.addAddressPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY), 
				DataStore.getSavedValue(UsableValues.BUSINESS_NATION), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		websiteManager.addAddressPage.saveGoToPartnershipInformationPage();
		
		LOG.info("Selected Country: " + DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY));
		LOG.info("Selected Nation: " + DataStore.getSavedValue(UsableValues.BUSINESS_NATION));
		
		websiteManager.partnershipInformationPage.editAboutTheOrganisation();
		websiteManager.parPartnershipDescriptionPage.updateBusinessDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		websiteManager.parPartnershipDescriptionPage.goToPartnershipInformationPage();
		
		websiteManager.partnershipInformationPage.editSICCode();
		websiteManager.sicCodePage.editSICCode(DataStore.getSavedValue(UsableValues.SIC_CODE));
		
		websiteManager.partnershipInformationPage.editTradingName();
		websiteManager.tradingPage.goToPartnershipInformationPage(DataStore.getSavedValue(UsableValues.TRADING_NAME));
	}

	@Then("^all of the Partnership details have been updated successfully$")
	public void all_of_the_Partnership_details_have_been_updated_successfully() throws Throwable {
		LOG.info("Verifying all the remaining Partnership details have been updated Successfully.");

		assertTrue(websiteManager.partnershipInformationPage.checkOrganisationAddress());
		assertTrue(websiteManager.partnershipInformationPage.checkAboutTheOrganisation());
		assertTrue(websiteManager.partnershipInformationPage.checkSICCode());
		assertTrue(websiteManager.partnershipInformationPage.verifyTradingName());
		
		websiteManager.partnershipInformationPage.clickSave();
	}
	
	@When("^the user Amends the legal entities with the following details:$")
	public void the_user_Amends_the_legal_entities_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Creating the Legal Entity Amendment as the Authority User.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENTITY_TYPE, data.get("Entity Type"));
			DataStore.saveValue(UsableValues.ENTITY_NAME, data.get("Entity Name"));
		}
		
		websiteManager.partnershipInformationPage.selectAmendLegalEntitiesLink();
		
		websiteManager.legalEntityTypePage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		websiteManager.legalEntityTypePage.goToLegalEntityReviewPage();
		websiteManager.legalEntityReviewPage.goToConfirmThisAmendmentPage();
		
		websiteManager.confirmThisAmendmentPage.confirmLegalEntityAmendments();
		
		websiteManager.amendmentCompletedPage.goToPartnershipDetailsPage();
	}
	
	@Then("^the user verifies the amendments are created successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_amendments_are_created_successfully_with_status(String status) throws Throwable {
		LOG.info("Verify the Legal Entity was Created Successfully.");
		assertTrue(websiteManager.partnershipInformationPage.verifyLegalEntity(status));
	}

	@When("^the user confirms the legal entity amendments$")
	public void the_user_confirms_the_legal_entity_amendments() throws Throwable {
		LOG.info("Confirm the Legal Entity as the Business User.");
		
		websiteManager.partnershipInformationPage.selectConfirmLegalEntitiesLink();
		websiteManager.confirmThisAmendmentPage.confirmLegalEntityAmendments();
		websiteManager.amendmentCompletedPage.goToDashBoardPage();
	}
	
	@Then("^the user verifies the amendments are confirmed successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_amendments_are_confirmed_successfully_with_status(String status) throws Throwable {
		LOG.info("Search for the Partnership to Verify the Amendment.");
		websiteManager.dashboardPage.selectSeePartnerships();
		websiteManager.partnershipSearchPage.searchPartnerships();
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		
		LOG.info("Verify the Legal Entity was Confirmed Successfully.");
		assertTrue(websiteManager.partnershipInformationPage.verifyLegalEntity(status));
	}

	@When("^the user nominates the legal entity amendments$")
	public void the_user_nominates_the_legal_entity_amendments() throws Throwable {
		LOG.info("Nominate the Legal Entity as the Help Desk User.");
		
		websiteManager.partnershipInformationPage.selectNominateLegalEntitiesLink();
		websiteManager.confirmThisAmendmentPage.confirmLegalEntityAmendments();
		websiteManager.amendmentCompletedPage.goToPartnershipDetailsPage();
	}
	
	@Then("^the user verifies the amendments are nominated successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_amendments_are_nominated_successfully_with_status(String status) throws Throwable {
		LOG.info("Verify the Legal Entity was Nominated Successfully.");
		assertTrue(websiteManager.partnershipInformationPage.verifyLegalEntity(status));
	}
	
	@When("^the user revokes the legal entity with the reason \"([^\"]*)\"$")
	public void the_user_revokes_the_legal_entity_with_the_reason(String reason) throws Throwable {
		LOG.info("Revoke the Legal Entity as the Help Desk User.");
		
		websiteManager.partnershipInformationPage.selectRevokeLegalEntitiesLink();
		
		websiteManager.revokePage.enterReasonForRevocation(reason);
		websiteManager.revokePage.goToPartnershipDetailsPage();
	}

	@Then("^the user verifies the legal entity was revoked successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_legal_entity_was_revoked_successfully_with_status(String status) throws Throwable {
		LOG.info("Verify the Legal Entity was Revoked Successfully.");
		assertTrue(websiteManager.partnershipInformationPage.verifyLegalEntity(status));
	}
	
	@When("^the user reinstates the legal entity$")
	public void the_user_reinstates_the_legal_entity() throws Throwable {
		LOG.info("Reinstate the Legal Entity as the Help Desk User.");
		
		websiteManager.partnershipInformationPage.selectReinstateLegalEntitiesLink();
		websiteManager.reinstatePage.goToPartnershipDetailsPage();
	}

	@Then("^the user verifies the legal entity was reinstated successfully with status \"([^\"]*)\"$")
	public void the_user_verifies_the_legal_entity_was_reinstated_successfully_with_status(String status) throws Throwable {
		LOG.info("Verify the Legal Entity was Reinstated Successfully.");
		assertTrue(websiteManager.partnershipInformationPage.verifyLegalEntity(status));
	}
	
	@When("^the user removes the legal entity$")
	public void the_user_removes_the_legal_entity() throws Throwable {
		LOG.info("Remove the Legal Entity as the Help Desk User.");
		
		websiteManager.partnershipInformationPage.selectRemoveLegalEntitiesLink();
		websiteManager.removePage.goToPartnershipDetailsPage();
	}

	@Then("^the user verifies the legal entity was removed successfully$")
	public void the_user_verifies_the_legal_entity_was_removed_successfully() throws Throwable {
		LOG.info("Verify the Legal Entity was Removed Successfully.");
		assertTrue(websiteManager.partnershipInformationPage.verifyLegalEnityExists());
	}
	
	@When("^the user adds a Primary Authority contact to be Invited with the following details:$")
	public void the_user_adds_a_Primary_Authority_contact_to_be_Invited_with_the_following_details(DataTable details) throws Throwable {
		
		websiteManager.partnershipInformationPage.addAnotherAuthorityContactButton();

		LOG.info("Adding new contact details.");
		websiteManager.contactDetailsPage.setContactDetailsWithRandomName(details);
		
		websiteManager.contactDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		websiteManager.contactDetailsPage.enterFirstName(DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME));
		websiteManager.contactDetailsPage.enterLastName(DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME));
		websiteManager.contactDetailsPage.enterWorkNumber(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		websiteManager.contactDetailsPage.enterMobileNumber(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		websiteManager.contactDetailsPage.enterEmail(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		
		websiteManager.contactDetailsPage.selectRandomPreferredCommunication();
		websiteManager.contactDetailsPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		
		websiteManager.contactDetailsPage.selectContinueButton();
		
		LOG.info("Reviewing the Contact Details..");
		websiteManager.profileReviewPage.clickSaveButton();
	}
	
	@Then("^the new Primary Authority contact is added Successfully$")
	public void the_new_Primary_Authority_contact_is_added_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact is added successfully.");
		assertTrue("Failed: Contact Details are not Displayed Correctly.", websiteManager.partnershipInformationPage.checkContactDetails());
	}
	
	@When("^the user removes the new Primary Authority contact$")
	public void the_user_removes_the_new_Primary_Authority_contact() throws Throwable {
		websiteManager.partnershipInformationPage.removeContactsDetailsButton();
		
		LOG.info("Removing the contact.");
		websiteManager.removePage.goToPartnershipDetailsPage();
	}

	@Then("^the new Primary Authority contact is removed Successfully$")
	public void the_new_Primary_Authority_contact_is_removed_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact was removed successfully.");
		assertTrue("Failed: Contact was not Removed.", websiteManager.partnershipInformationPage.checkContactExists());
	}
	
	@When("^the user adds a new Organisation contact to be Invited with the following details:$")
	public void the_user_adds_a_new_Organisation_contact_to_be_Invited_with_the_following_details(DataTable details) throws Throwable {
		websiteManager.partnershipInformationPage.addAnotherOrganisationContactButton();

		LOG.info("Adding new contact details.");
		websiteManager.contactDetailsPage.setContactDetailsWithRandomName(details);
		
		websiteManager.contactDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		websiteManager.contactDetailsPage.enterFirstName(DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME));
		websiteManager.contactDetailsPage.enterLastName(DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME));
		websiteManager.contactDetailsPage.enterWorkNumber(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		websiteManager.contactDetailsPage.enterMobileNumber(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		websiteManager.contactDetailsPage.enterEmail(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		
		websiteManager.contactDetailsPage.selectRandomPreferredCommunication();
		websiteManager.contactDetailsPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		
		websiteManager.contactDetailsPage.selectContinueButton();
		
		LOG.info("Reviewing the Contact Details.");
		websiteManager.profileReviewPage.clickSaveButton();
	}

	@Then("^the new Organisation contact is added Successfully$")
	public void the_new_Organisation_contact_is_added_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact is added successfully.");
		assertTrue("Failed: Contact Details are not Displayed Correctly.", websiteManager.partnershipInformationPage.checkContactDetails());
	}
	
	@When("^the user removes the new Organisation contact$")
	public void the_user_removes_the_new_Organisation_contact() throws Throwable {
		websiteManager.partnershipInformationPage.removeContactsDetailsButton();
		
		LOG.info("Removing the contact.");
		websiteManager.removePage.goToPartnershipDetailsPage();
	}

	@Then("^the new Organisation contact is removed Successfully$")
	public void the_new_Organisation_contact_is_removed_Successfully() throws Throwable {
		LOG.info("Verifying the new Authority contact was removed successfully.");
		assertTrue("Failed: Contact was not Removed.", websiteManager.partnershipInformationPage.checkContactExists());
	}
	
	@When("^the user uploads an inspection plan against the partnership with the following details:$")
	public void the_user_uploads_an_inspection_plan_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Upload inspection plan and save details");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.partnershipAdvancedSearchPage.selectPartnershipLink();
		websiteManager.partnershipInformationPage.selectSeeAllInspectionPlans();
		
		websiteManager.inspectionPlanSearchPage.selectUploadLink();
		
		websiteManager.uploadInspectionPlanPage.chooseFile("link.txt");
		websiteManager.uploadInspectionPlanPage.uploadFile();
		
		websiteManager.inspectionPlanDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE));
		websiteManager.inspectionPlanDetailsPage.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
		websiteManager.inspectionPlanDetailsPage.clickSave();
		
		websiteManager.enterTheDatePage.enterDate("ddMMYYYY");
		websiteManager.enterTheDatePage.goToInspectionPlanSearchPage();
	}
	
	@Then("^the inspection plan is uploaded successfully$")
	public void the_inspection_plan_is_uploaded_successfully() throws Throwable {
		LOG.info("Verifying the Inpsection Plan Status is set to Current.");
		Assert.assertTrue("Failed: Inspection Plan Status is not set to Current.", websiteManager.inspectionPlanSearchPage.getPlanStatus().equalsIgnoreCase("Current"));
	}

	@When("^the user updates the last created inspection plan against the partnership with the following details:$")
	public void the_user_updates_the_last_created_inspection_plan_against_the_partnership_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Edit inspection plan and save details.");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.INSPECTIONPLAN_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.partnershipAdvancedSearchPage.selectPartnershipLink();
		websiteManager.partnershipInformationPage.selectSeeAllInspectionPlans();
		
		websiteManager.inspectionPlanSearchPage.selectEditLink();
		
		websiteManager.inspectionPlanDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE));
		websiteManager.inspectionPlanDetailsPage.enterInspectionDescription(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION));
		websiteManager.inspectionPlanDetailsPage.clickSave();
		
		websiteManager.enterTheDatePage.goToInspectionPlanSearchPage();
		websiteManager.inspectionPlanSearchPage.selectInspectionPlan();
	}

	@Then("^the inspection plan is updated correctly$")
	public void the_inspection_plan_is_updated_correctly() throws Throwable {
		LOG.info("Verify the Inspection Plan details are correct.");
		Assert.assertTrue("Failed: The Inspection Plan details are not correct.", websiteManager.inspectionPlanReviewPage.checkInspectionPlan());
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
		
		websiteManager.partnershipAdvancedSearchPage.selectPartnershipLink();
		websiteManager.partnershipInformationPage.selectSeeAllAdviceNotices();
		
		websiteManager.adviceNoticeSearchPage.selectUploadLink();
		
		websiteManager.uploadAdviceNoticePage.chooseFile("link.txt");
		websiteManager.uploadAdviceNoticePage.uploadFile();
		
		websiteManager.adviceNoticeDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		websiteManager.adviceNoticeDetailsPage.selectAdviceType(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TYPE));
		websiteManager.adviceNoticeDetailsPage.selectRegulatoryFunction(DataStore.getSavedValue(UsableValues.ADVICENOTICE_REGFUNCTION));
		websiteManager.adviceNoticeDetailsPage.enterDescription(DataStore.getSavedValue(UsableValues.ADVICENOTICE_DESCRIPTION));
		websiteManager.adviceNoticeDetailsPage.clickSave();
	}
	
	@Then("^the advice notice it uploaded successfully and set to active$")
	public void the_advice_notice_it_uploaded_successfully_and_set_to_active() throws Throwable {
		LOG.info("Checking Advice notice status is set to \"Active\"");
		Assert.assertTrue("Failed: Status not set to \"Active\"", websiteManager.adviceNoticeSearchPage.getAdviceStatus().equalsIgnoreCase("Active"));
	}

	@When("^the user selects the edit advice action link$")
	public void the_user_selects_the_edit_advice_action_link() throws Throwable {
		LOG.info("Searching for the newly added Advice notice.");
		
		websiteManager.adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		websiteManager.adviceNoticeSearchPage.selectEditAdviceButton();
	}

	@When("^the user edits the advice notice with the following details:$")
	public void the_user_edits_the_advice_notice_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Editing Advice notice details.");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ADVICENOTICE_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_TYPE, data.get("Type of Advice"));
			DataStore.saveValue(UsableValues.ADVICENOTICE_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.adviceNoticeDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		websiteManager.adviceNoticeDetailsPage.selectAdviceType(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TYPE));
		websiteManager.adviceNoticeDetailsPage.enterDescription(DataStore.getSavedValue(UsableValues.ADVICENOTICE_DESCRIPTION));
		websiteManager.adviceNoticeDetailsPage.clickSave();
	}

	@Then("^the advice notice it updated successfully$")
	public void the_advice_notice_it_updated_successfully() throws Throwable {
		LOG.info("Verifying Advice Notice status is set to Active.");
		Assert.assertTrue("Failed: Status not set to \"Active\"", websiteManager.adviceNoticeSearchPage.getAdviceStatus().equalsIgnoreCase("Active"));
	}

	@When("^the user archives the advice notice with the following reason \"([^\"]*)\"$")
	public void the_user_archives_the_advice_notice_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Archiving Advice Notice.");
		
		websiteManager.adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		websiteManager.adviceNoticeSearchPage.selectArchiveAdviceButton();
		
		websiteManager.adviceArchivePage.enterReasonForArchiving(reason);
	}

	@Then("^the advice notice is archived successfully$")
	public void the_advice_notice_is_archived_successfully() throws Throwable {
		LOG.info("Check Advice notice status is set to \"Archived\"");
		Assert.assertTrue("Failed: Status not set to \"Archived\"", websiteManager.adviceNoticeSearchPage.getAdviceStatus().equalsIgnoreCase("Archived"));
	}
	
	@When("^the user removes the advice notice with the following reason \"([^\"]*)\"$")
	public void the_user_removes_the_advice_notice_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Removing Advice Notice.");
		
		websiteManager.adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		websiteManager.adviceNoticeSearchPage.selectRemoveAdviceButton();
		
		websiteManager.removePage.enterRemoveReason(reason);
		websiteManager.removePage.goToAdviceNoticeSearchPage();
	}

	@Then("^the advice notice is removed successfully$")
	public void the_advice_notice_is_removed_successfully() throws Throwable {
		LOG.info("Check Advice notice was Removed.");
		websiteManager.adviceNoticeSearchPage.searchForAdvice(DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE));
		
		Assert.assertTrue("Failed: Advice Notice was not Removed.", websiteManager.adviceNoticeSearchPage.checkNoResultsReturned());
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
		
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		
		LOG.info("Create enformcement notification against partnership");
		websiteManager.partnershipInformationPage.createEnforcement();
		websiteManager.enforcementNotificationPage.clickContinue();
		
		websiteManager.enforcementOfficerContactDetailsPage.goToEnforceLegalEntityPage();
		
		websiteManager.enforceLegalEntityPage.enterLegalEntityName(DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		websiteManager.enforceLegalEntityPage.clickContinue();
		
		websiteManager.enforcementDetailsPage.selectEnforcementType(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TYPE));
		websiteManager.enforcementDetailsPage.enterEnforcementDescription(DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION));
		websiteManager.enforcementDetailsPage.clickContinue();
		
		websiteManager.enforcementActionPage.selectRegulatoryFunctions(DataStore.getSavedValue(UsableValues.ENFORCEMENT_REGFUNC));
		websiteManager.enforcementActionPage.chooseFile(DataStore.getSavedValue(UsableValues.ENFORCEMENT_FILENAME));
		websiteManager.enforcementActionPage.enterEnforcementDescription(DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION).toLowerCase());
		websiteManager.enforcementActionPage.enterTitle(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE));
		websiteManager.enforcementActionPage.clickContinue();
	}

	@Then("^all the fields for the enforcement notice are updated correctly$")
	public void all_the_fields_for_the_enforcement_are_updated_correctly() throws Throwable {
		LOG.info("Verify Enforcement Details are Correct.");
		Assert.assertTrue("Failed: Enforcement Details are not Correct.", websiteManager.enforcementReviewPage.checkEnforcementCreation());
		
		websiteManager.enforcementReviewPage.saveChanges();
		websiteManager.enforcementCompletionPage.goToPartnershipConfirmationPage();
	}

	@When("^the user selects the last created enforcement notice$")
	public void the_user_selects_the_last_created_enforcement() throws Throwable {
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_enforcement_officer@example.com"):
			LOG.info("Select last created enforcement");
			websiteManager.dashboardPage.selectSeeEnforcementNotices();
			websiteManager.enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
			websiteManager.enforcementSearchPage.selectEnforcement();
			break;

		case ("par_authority@example.com"):
			LOG.info("Select last created enforcement");
			websiteManager.dashboardPage.selectSeeEnforcementNotices();
			websiteManager.enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
			websiteManager.enforcementSearchPage.selectEnforcement();
			break;

		case ("par_helpdesk@example.com"):
			LOG.info("Searching for an Enforcement Notice.");
			websiteManager.helpDeskDashboardPage.selectManageEnforcementNotices();
			websiteManager.enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
			websiteManager.enforcementSearchPage.selectEnforcement();
			break;

		default:
			// do nothing
		}
	}

	@When("^the user approves the enforcement notice$")
	public void the_user_approves_the_enforcement_notice() throws Throwable {
		LOG.info("Approve the EnforcementNotice.");
		websiteManager.proposedEnforcementPage.selectAllow();
		websiteManager.proposedEnforcementPage.clickContinue();
		
		websiteManager.enforcementReviewPage.saveChanges();
		websiteManager.enforcementCompletionPage.clickDone();
	}

	@Then("^the enforcement notice is set to approved status$")
	public void the_enforcement_notice_is_set_to_approved_status() throws Throwable {
		LOG.info("Verify the Enforcement Notice was Approved.");
		websiteManager.enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		
		Assert.assertTrue("Failed: Enforcement Status is not correct.", websiteManager.enforcementSearchPage.getStatus().equalsIgnoreCase("Approved"));
	}
	
	@When("^the user searches for the last created enforcement notice$")
	public void the_user_searches_for_the_last_created_enforcement_notice() throws Throwable {
		LOG.info("Select last created enforcement");
		websiteManager.helpDeskDashboardPage.selectManageEnforcementNotices();
		websiteManager.enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		websiteManager.enforcementSearchPage.removeEnforcement();
	}
	
	@When("^the user removes the enforcement notice$")
	public void the_user_removes_the_enforcement_notice() throws Throwable {
		LOG.info("Remove the Enforcement Notice.");
		websiteManager.removeEnforcementPage.selectReasonForRemoval("This is a duplicate enforcement");
		websiteManager.removeEnforcementPage.enterReasonForRemoval("Test Remove.");
		websiteManager.removeEnforcementPage.clickContinue();
		
		websiteManager.declarationPage.selectConfirmCheckbox();
		websiteManager.declarationPage.goToEnforcementSearchPage();
	}

	@Then("^the enforcement notice is removed successfully$")
	public void the_enforcement_notice_is_removed_successfully() throws Throwable {
		LOG.info("Verify the Enforcement Notice was Removed Successfully.");
		websiteManager.enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		
		Assert.assertTrue("Failed: Enforcement Notice was not Removed.", websiteManager.enforcementSearchPage.confirmNoReturnedResults());
	}
	
	@When("^the user blocks the enforcement notice with the following reason: \"([^\"]*)\"$")
	public void the_user_blocks_the_enforcement_notice_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Block the Enforcement Notice.");
		websiteManager.proposedEnforcementPage.selectBlock();
		websiteManager.proposedEnforcementPage.enterReasonForBlockingEnforcement(reason);
		websiteManager.proposedEnforcementPage.clickContinue();
		
		websiteManager.enforcementReviewPage.saveChanges();
		websiteManager.enforcementCompletionPage.clickDone();
	}
	
	@Then("^the enforcement notice is set to blocked status$")
	public void the_enforcement_notice_is_set_to_blocked_status() throws Throwable {
		LOG.info("Verify the Enformcement Notice is Blocked.");
		websiteManager.enforcementSearchPage.searchForEnforcementNotice(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		
		Assert.assertTrue("Failed: Enforcement was not Blocked.", websiteManager.enforcementSearchPage.getStatus().equalsIgnoreCase("Blocked"));
	}
	
	@When("^the user submits a deviation request against an inspection plan with the following details:$")
	public void the_user_submits_a_deviation_request_against_an_inspection_plan_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit Deviation Request");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.DEVIATION_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		websiteManager.partnershipInformationPage.selectDeviateInspectionPlan();
		websiteManager.enforcementOfficerContactDetailsPage.goToDeviationRequestPage();
		
		websiteManager.requestDeviationPage.enterDescription(DataStore.getSavedValue(UsableValues.DEVIATION_DESCRIPTION));
		websiteManager.requestDeviationPage.chooseFile("link.txt");
		websiteManager.requestDeviationPage.clickContinue();
	}

	@Then("^the Deviation Request is created Successfully$")
	public void the_Deviation_Request_is_created_Successfully() throws Throwable {
		LOG.info("Verify the Deviation Request is created Successfully.");
		
		Assert.assertTrue("Failed: Deviation Request details are not displayed.", websiteManager.deviationReviewPage.checkDeviationCreation());
		websiteManager.deviationReviewPage.saveChanges();
		websiteManager.deviationCompletionPage.complete();
	}
	
	@When("^the user searches for the last created deviation request$")
	public void the_user_searches_for_the_last_created_deviation_request() throws Throwable {
		LOG.info("Search for last created deviation request");
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
			websiteManager.helpDeskDashboardPage.selectManageDeviationRequests();
			websiteManager.deviationSearchPage.selectDeviationRequest();
			break;
		default:
			websiteManager.dashboardPage.selectSeeDeviationRequests();
			websiteManager.deviationSearchPage.selectDeviationRequest();
		}
	}
	
	@When("^the user blocks the deviation request with the following reason: \"([^\"]*)\"$")
	public void the_user_blocks_the_deviation_request_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Block the deviation request.");
		
		websiteManager.deviationApprovalPage.selectBlock();
		websiteManager.deviationApprovalPage.enterReasonForBlocking(reason);
		websiteManager.deviationApprovalPage.clickContinue();
	}

	@Then("^the deviation request is set to blocked status$")
	public void the_deviation_request_is_set_to_blocked_status() throws Throwable {
		LOG.info("Check the Deviation Request is Blocked on the Review Page.");
		
		Assert.assertTrue("Failed: Deviation request status is not Set to Blocked.", websiteManager.deviationReviewPage.checkDeviationStatusBlocked());
		websiteManager.deviationReviewPage.saveChanges();
		websiteManager.deviationCompletionPage.complete();
	}

	@Then("^the user successfully approves the deviation request$")
	public void the_user_successfully_approves_the_deviation_request() throws Throwable {
		LOG.info("Approve the deviation request");
		websiteManager.deviationApprovalPage.selectAllow();
		websiteManager.deviationApprovalPage.clickContinue();
		
		Assert.assertTrue("Failed: Deviation request status not correct", websiteManager.deviationReviewPage.checkDeviationStatusApproved());
		websiteManager.deviationReviewPage.saveChanges();
		websiteManager.deviationCompletionPage.complete();
	}

	@When("^the user submits a response to the deviation request with the following details:$")
	public void the_user_submits_a_response_to_the_deviation_request_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit response to the deviation request");
		websiteManager.deviationSearchPage.selectDeviationRequest();
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1, data.get("Description"));
		}
		
		websiteManager.deviationReviewPage.submitResponse();
		
		websiteManager.replyDeviationRequestPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1));
		websiteManager.replyDeviationRequestPage.chooseFile("link.txt");
		websiteManager.replyDeviationRequestPage.clickSave();
	}
	
	@Then("^the response is displayed successfully$")
	public void the_response_is_displayed_successfully() throws Throwable {
		LOG.info("Verify the Deviation Request Response was Successful.");
		Assert.assertTrue("Failed: Deviation response is not displayed.", websiteManager.deviationReviewPage.checkDeviationResponse());
	}

	@When("^the user sends a reply to the deviation request message with the following details:$")
	public void the_user_sends_a_reply_to_the_deviation_request_message_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit reply to the deviation request");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1, data.get("Description"));
		}
		
		websiteManager.deviationReviewPage.submitResponse();
		
		websiteManager.replyDeviationRequestPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1));
		websiteManager.replyDeviationRequestPage.chooseFile("link.txt");
		websiteManager.replyDeviationRequestPage.clickSave();
	}

	@Then("^the deviation reply received successfully$")
	public void the_deviation_reply_received_successfully() throws Throwable {
		LOG.info("Verify the deviation response");
		Assert.assertTrue("Failed: Deviation Reply is not DIsplayed.", websiteManager.deviationReviewPage.checkDeviationResponse());
	}
	
	@When("^the user submits an inspection feedback against the inspection plan with the following details:$")
	public void the_user_submits_an_inspection_feedback_against_the_inspection_plan_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit inspection feedback against partnership");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		websiteManager.partnershipInformationPage.selectSendInspectionFeedbk();
		
		websiteManager.enforcementOfficerContactDetailsPage.goToInspectionFeedbackDetailsPage();
		
		websiteManager.inspectionFeedbackDetailsPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION));
		websiteManager.inspectionFeedbackDetailsPage.chooseFile("link.txt");
		websiteManager.inspectionFeedbackDetailsPage.clickContinue();
	}
	
	@Then("^the inspection feedback is created successfully$")
	public void the_inspection_feedback_is_created_successfully() throws Throwable {
		LOG.info("Verifying the Inspection Feedback Details.");
		Assert.assertTrue("Failed: Inspection Feedback Details are Incorrect.", websiteManager.inspectionFeedbackConfirmationPage.checkInspectionFeedback());
		
		websiteManager.inspectionFeedbackConfirmationPage.goToInspectionFeedbackCompletionPage();
		websiteManager.inspectionFeedbackCompletionPage.complete();
	}

	@When("^the user searches for the last created inspection feedback$")
	public void the_user_searches_for_the_last_created_inspection_feedback() throws Throwable {
		LOG.info("Search for the last created Inspection Feedback.");
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
			websiteManager.helpDeskDashboardPage.selectManageInspectionFeedback();
			websiteManager.inspectionFeedbackSearchPage.selectInspectionFeedbackNotice();
			break;
		default:
			websiteManager.dashboardPage.selectSeeInspectionFeedbackNotices();
			websiteManager.inspectionFeedbackSearchPage.selectInspectionFeedbackNotice();
		}
	}

	@Then("^the user successfully approves the inspection feedback$")
	public void the_user_successfully_approves_the_inspection_feedback() throws Throwable {
		LOG.info("Verify the inspection feedback description");
		Assert.assertTrue("Failed: Inspection feedback description is not correct.", websiteManager.inspectionFeedbackConfirmationPage.checkInspectionFeedback());
	}
	
	@When("^the user submits a response to the inspection feedback with the following details:$")
	public void the_user_submits_a_response_to_the_inspection_feedback_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit response to inspection feedback request");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1, data.get("Description"));
		}

		websiteManager.inspectionFeedbackConfirmationPage.submitResponse();
		
		websiteManager.replyInspectionFeedbackPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1));
		websiteManager.replyInspectionFeedbackPage.chooseFile("link.txt");
		websiteManager.replyInspectionFeedbackPage.clickSave();
	}
	
	@When("^the user sends a reply to the inspection feedback message with the following details:$")
	public void the_user_sends_a_reply_to_the_inspection_feedback_message_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit reply to inspection feedback response");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1, data.get("Description"));
		}
		
		websiteManager.inspectionFeedbackConfirmationPage.submitResponse();
		
		websiteManager.replyInspectionFeedbackPage.enterFeedbackDescription(DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1));
		websiteManager.replyInspectionFeedbackPage.chooseFile("link.txt");
		websiteManager.replyInspectionFeedbackPage.clickSave();
	}
	
	@Then("^the inspection feedback response is displayed successfully$")
	public void the_inspection_feedback_response_is_displayed_successfully() throws Throwable {
		LOG.info("Verifying the Inspection Plan Feedback Response.");
		Assert.assertTrue("Failed: Inspection Feeback Response is not Displayed.", websiteManager.inspectionFeedbackConfirmationPage.checkInspectionResponse());
	}
	
	@When("^the user submits a general enquiry with the following details:$")
	public void the_user_submits_a_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Send general query");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENQUIRY_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		websiteManager.partnershipInformationPage.sendGeneralEnquiry();
		
		websiteManager.enforcementOfficerContactDetailsPage.goToRequestEnquiryPage();
		
		websiteManager.requestEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION));
		websiteManager.requestEnquiryPage.chooseFile("link.txt");
		websiteManager.requestEnquiryPage.clickContinue();
	}
	
	@When("^the user sends a general enquiry for an enforcement notice with the following details:$")
	public void the_user_sends_a_general_enquiry_for_an_enforcement_notice_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Send general query");
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENQUIRY_DESCRIPTION, data.get("Description"));
		}
		
		websiteManager.partnershipSearchPage.selectBusinessNameLinkFromPartnership();
		websiteManager.partnershipInformationPage.createEnforcement();
		
		websiteManager.enforcementNotificationPage.selectDiscussEnforcement();
		
		websiteManager.enforcementOfficerContactDetailsPage.goToRequestEnquiryPage();
		
		websiteManager.requestEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION));
		websiteManager.requestEnquiryPage.chooseFile("link.txt");
		websiteManager.requestEnquiryPage.clickContinue();
	}

	@Then("^the Enquiry is created Successfully$")
	public void the_Enquiry_is_created_Successfully() throws Throwable {
		LOG.info("Verify the enquiry is created.");
		Assert.assertTrue("Failed: Enquiry details are not correct.", websiteManager.enquiryReviewPage.checkEnquiryDescription());
		
		websiteManager.enquiryReviewPage.saveChanges();
		websiteManager.enquiryCompletionPage.complete();
	}

	@When("^the user searches for the last created general enquiry$")
	public void the_user_searches_for_the_last_created_general_enquiry() throws Throwable {
		LOG.info("Search for last created enquiry");
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
			websiteManager.helpDeskDashboardPage.selectManageGeneralEnquiry();
			websiteManager.enquiriesSearchPage.selectEnquiry();
			break;
		default:
			websiteManager.dashboardPage.selectGeneralEnquiries();
			websiteManager.enquiriesSearchPage.selectEnquiry();
		}
	}
	
	@Then("^the general enquiry is recieved successfully$")
	public void the_general_enquiry_is_recieved_successfully() throws Throwable {
		LOG.info("Verifying the General Enquiry is Recieved.");
		Assert.assertTrue("Failed: Enquiry details are not correct.", websiteManager.enquiryReviewPage.checkEnquiryDetails());
	}
	
	@When("^the user submits a response to the general enquiry with the following details:$")
	public void the_user_submits_a_response_to_the_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		LOG.info("Submit reply to the enquiry");
		
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENQUIRY_REPLY, data.get("Description"));
		}
		
		websiteManager.enquiryReviewPage.submitResponse();
		
		websiteManager.replyEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY));
		websiteManager.replyEnquiryPage.chooseFile("link.txt");
		websiteManager.replyEnquiryPage.clickSave();
	}

	@When("^the user sends a reply to the general enquiry with the following details:$")
	public void the_user_sends_a_reply_to_the_general_enquiry_with_the_following_details(DataTable dets) throws Throwable {
		for (Map<String, String> data : dets.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENQUIRY_REPLY, data.get("Description"));
		}
		
		websiteManager.enquiryReviewPage.submitResponse();
		
		websiteManager.replyEnquiryPage.enterDescription(DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY));
		websiteManager.replyEnquiryPage.chooseFile("link.txt");
		websiteManager.replyEnquiryPage.clickSave();
	}
	
	@Then("^the general enquiry response is displayed successfully$")
	public void the_general_enquiry_response_is_displayed_successfully() throws Throwable {
		LOG.info("Verifying General Enquiry Response.");
		Assert.assertTrue("Failed: General Enquiry Response is not Displayed Correctly.", websiteManager.enquiryReviewPage.checkEnquiryResponse());
	}
	
	@When("^the user revokes the last created inspection plan$")
	public void the_user_revokes_the_last_created_inspection_plan() throws Throwable {
		LOG.info("Revoking the last created Inspection Plan.");
		
		websiteManager.partnershipAdvancedSearchPage.selectPartnershipLink();
		websiteManager.partnershipInformationPage.selectSeeAllInspectionPlans();
		
		websiteManager.inspectionPlanSearchPage.selectRevokeLink();
		
		websiteManager.revokePage.enterReasonForRevocation("Test Revoke.");
		websiteManager.revokePage.goToInspectionPlanSearchPage();
	}

	@Then("^the inspection plan is revoked successfully$")
	public void the_inspection_plan_is_revoked_successfully() throws Throwable {
		LOG.info("Verifying the Inspection Plan was Revoked Successfully.");
		assertEquals("Failed: Inspection Plan was not Revoked.", websiteManager.inspectionPlanSearchPage.getPlanStatus(), "Revoked");
	}
	
	@When("^the user has revoked the last created inspection plan$")
	public void the_user_has_revoked_the_last_created_inspection_plan() throws Throwable {
		LOG.info("Removing the Inspection Plan.");
		websiteManager.inspectionPlanSearchPage.selectRemoveLink();
		
		websiteManager.removePage.enterRemoveReason("Test Remove.");
		websiteManager.removePage.goToInspectionPlanSearchPage();
	}

	@Then("^the inspection plan is successfully removed$")
	public void the_inspection_plan_is_successfully_removed() throws Throwable {
		LOG.info("Verifying the Inspection Plan was Removed Successfully.");
		assertEquals("Failed: Inspection Plan was not Removed.", websiteManager.inspectionPlanSearchPage.getPlanStatus(), "No results returned");
	}
	
	@When("^the user visits the maillog page and extracts the invite link$")
	public void the_user_visits_the_maillog_page_and_extracts_the_invite_link() throws Throwable {
		websiteManager.mailLogPage.navigateToUrl();
		websiteManager.mailLogPage.searchForUserAccountInvite(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		websiteManager.mailLogPage.selectEamilAndGetINviteLink(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
	}

	@When("^the user follows the invitation link$")
	public void the_user_follows_the_invitation_link() throws Throwable {
		websiteManager.loginPage.navigateToInviteLink();
	}

	@When("^the user completes the user creation journey$")
	public void the_user_completes_the_user_creation_journey() throws Throwable {
		LOG.info("Completing the User Creation Journey.");
		
		websiteManager.passwordPage.enterPassword("TestPassword", "TestPassword");
		websiteManager.passwordPage.selectRegister();
		
		websiteManager.declarationPage.selectDataPolicyCheckbox();
		websiteManager.declarationPage.goToContactDetailsPage();
		
		websiteManager.contactDetailsPage.goToContactCommunicationPreferencesPage();
		
		DataStore.saveValue(UsableValues.CONTACT_NOTES, "Test User Creation Note.");
		
		websiteManager.contactCommunicationPreferencesPage.enterContactNote(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
		websiteManager.contactCommunicationPreferencesPage.selectContinueButton();
		
		websiteManager.contactUpdateSubscriptionPage.subscribeToPARNews();
		websiteManager.contactUpdateSubscriptionPage.selectContinueButton();
	}

	@Then("^the user journey creation is successful$")
	public void the_user_journey_creation_is_successful() throws Throwable {
		LOG.info("Verify User Details are Correct.");
		
		assertTrue("Failed: Contact Details are not Displayed Correctly.", websiteManager.profileReviewPage.checkContactDetails());
		
		websiteManager.profileReviewPage.goToProfileCompletionPage();
		websiteManager.profileCompletionPage.goToDashboardPage();
	}
	
	@When("^the user Deletes the Partnership with the following reason: \"([^\"]*)\"$")
	public void the_user_Deletes_the_Partnership_with_the_following_reason(String reason) throws Throwable {
		LOG.info("Delete the Partnership.");
		websiteManager.partnershipAdvancedSearchPage.selectDeletePartnershipLink();
		
		websiteManager.deletePage.enterReasonForDeletion(reason);
		websiteManager.deletePage.clickDeleteForPartnership();
		
		websiteManager.completionPage.clickDoneForPartnership();
	}

	@Then("^the Partnership was Deleted Successfully$")
	public void the_Partnership_was_Deleted_Successfully() throws Throwable {
		LOG.info("Verify the Partnership was Deleted Successfully.");
		
		websiteManager.partnershipAdvancedSearchPage.searchPartnerships();
		Assert.assertTrue("Failed: Partnership was not Deleted.", websiteManager.partnershipAdvancedSearchPage.checkPartnershipExists());
	}
	
	@When("^the user adds a single member organisation to the patnership with the following details:$")
	public void the_user_adds_a_single_member_organisation_to_the_patnership_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Add a Single Member Organisation to a Co-ordinated Partnership.");
		
		websiteManager.partnershipAdvancedSearchPage.selectOrganisationLink();
		
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
		
		websiteManager.partnershipInformationPage.selectShowMembersListLink();
		websiteManager.memberListPage.selectAddAMemberLink();
		
		LOG.info("Entering the Member Organisation's Name.");
		websiteManager.addOrganisationNamePage.enterMemberOrganisationName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		LOG.info("Entering the Member Organisation's Address.");
		websiteManager.authorityAddressDetailsPage.enterMemberOrganisationAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		
		LOG.info("Entering the Member Organisation's Contact Details.");
		websiteManager.contactDetailsPage.enterContactWithRandomName(details);
		websiteManager.contactDetailsPage.clickContinueButtonForMemberContact();
		
		LOG.info("Entering the Member Organisation's Membership Start Date.");
		websiteManager.enterTheDatePage.clickContinueButtonForMembershipBegan();
		
		LOG.info("Entering the Member Organisation's Trading Name.");
		websiteManager.tradingPage.addTradingNameForMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		LOG.info("Entering the Member Organisation's Legal Entity.");
		websiteManager.legalEntityTypePage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		websiteManager.legalEntityTypePage.goToLegalEntityReviewPage();
		websiteManager.legalEntityReviewPage.clickContinueForMember();
		
		LOG.info("Confirming the Member Organisation is covered by the Inspection Plan.");
		websiteManager.inspectionPlanCoveragePage.selectYesRadial();
		websiteManager.inspectionPlanCoveragePage.selectContinueForMember();
		
		LOG.info("Saving the Member Organisation's Details.");
		websiteManager.memberOrganisationSummaryPage.selectSave();
		websiteManager.memberOrganisationAddedConfirmationPage.selectDone();
	}

	@Then("^the user member organistion has been added to the partnership successfully$")
	public void the_user_member_organistion_has_been_added_to_the_partnership_successfully() throws Throwable {
		LOG.info("Verify the Member Organisation was added to the Co-ordinated Partnership Successfully.");
		
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		Assert.assertTrue("Failed: Member Organisation was not Created.", websiteManager.memberListPage.checkMemberCreated());
	}
	
	@When("^the user updates a single member organisation of the patnership with the following details:$")
	public void the_user_updates_a_single_member_organisation_of_the_patnership_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Update a Single Member Organisation to a Co-ordinated Partnership.");
		
		websiteManager.partnershipAdvancedSearchPage.selectOrganisationLink();
		websiteManager.partnershipInformationPage.selectShowMembersListLink();
		
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		websiteManager.memberListPage.selectMembersName();
		
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
		websiteManager.memberOrganisationSummaryPage.selectEditOrganisationName();
		websiteManager.addOrganisationNamePage.editMemberOrganisationName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		LOG.info("Updating the Member Organisation's Address.");
		websiteManager.memberOrganisationSummaryPage.selectEditAddress();
		
		websiteManager.addAddressPage.editAddressDetails(DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.BUSINESS_TOWN), DataStore.getSavedValue(UsableValues.BUSINESS_COUNTY), DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE));
		
		websiteManager.addAddressPage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Membership Start Date.");
		websiteManager.memberOrganisationSummaryPage.selectEditMembershipStartDate();
		websiteManager.enterTheDatePage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Contact Details.");
		websiteManager.memberOrganisationSummaryPage.selectEditPerson();
		websiteManager.contactDetailsPage.enterContactWithRandomName(details);
		websiteManager.contactDetailsPage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Legal Entity.");
		websiteManager.memberOrganisationSummaryPage.selectAddAnotherLegalEntity();
		websiteManager.updateLegalEntityPage.selectUnregisteredEntity(DataStore.getSavedValue(UsableValues.ENTITY_TYPE), DataStore.getSavedValue(UsableValues.ENTITY_NAME));
		websiteManager.updateLegalEntityPage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Updating the Member Organisation's Trading Name.");
		websiteManager.memberOrganisationSummaryPage.selectEditTradingName();
		websiteManager.tradingPage.editMemberTradingName(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		websiteManager.tradingPage.goToMemberOrganisationSummaryPage();
		
		LOG.info("Confirming the Member Organisation is not covered by the Inspection Plan.");
		websiteManager.memberOrganisationSummaryPage.selectEditCoveredByInspectionPlan();
		websiteManager.inspectionPlanCoveragePage.selectNoRadial();
		websiteManager.inspectionPlanCoveragePage.selectSaveForMember();
	}

	@Then("^the member organistion has been updated successfully$")
	public void the_member_organistion_has_been_updated_successfully() throws Throwable {
		LOG.info("Verifying All Member Details are Correct.");
		Assert.assertTrue(websiteManager.memberOrganisationSummaryPage.checkMemberDetails());
		websiteManager.memberOrganisationSummaryPage.selectDone();
		
		LOG.info("Verify the Updated Member Organisation Name is Displayed on the Members List.");
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		Assert.assertTrue("Failed: Member Organisation was not Updated.", websiteManager.memberListPage.checkMemberCreated());
	}
	
	@When("^the user Ceases a single member organisation of the patnership with the current date$")
	public void the_user_Ceases_a_single_member_organisation_of_the_patnership_with_the_current_date() throws Throwable {
		LOG.info("Cease a Single Member Organisation to a Co-ordinated Partnership.");
		
		websiteManager.partnershipAdvancedSearchPage.selectOrganisationLink();
		websiteManager.partnershipInformationPage.selectShowMembersListLink();
		
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		websiteManager.memberListPage.selectCeaseMembership();
		
		LOG.info("Entering the Current Date for the Cessation to Happen.");
		websiteManager.enterTheDatePage.enterCurrentDate();
		websiteManager.enterTheDatePage.goToMembershipCeasedPage();
		
		websiteManager.membershipCeasedPage.goToMembersListPage();
	}

	@Then("^the member organistion has been Ceased successfully$")
	public void the_member_organistion_has_been_Ceased_successfully() throws Throwable {
		LOG.info("Verify the Member Organisation has been Ceased Successfully.");
		
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		
		Assert.assertTrue("Failed: Links are still present.", websiteManager.memberListPage.checkMembershipActionButtons());
		Assert.assertEquals("Failed: Dates do not match.", DataStore.getSavedValue(UsableValues.MEMBERSHIP_CEASE_DATE), websiteManager.memberListPage.getMembershipCeasedDate());
	}
	
	@When("^the user Uploads a members list to the coordinated partnership with the following file \"([^\"]*)\"$")
	public void the_user_Uploads_a_members_list_to_the_coordinated_partnership_with_the_following_file(String file) throws Throwable {
		LOG.info("Uploading a Members List CSV File to a Co-ordinated Partnership.");
		
		websiteManager.partnershipAdvancedSearchPage.selectOrganisationLink();
		websiteManager.partnershipInformationPage.selectShowMembersListLink();
		
		websiteManager.memberListPage.selectUploadMembersListLink();
		
		LOG.info("Uploading the Members List CSV File..");
		websiteManager.uploadListOfMembersPage.chooseCSVFile();
		websiteManager.uploadListOfMembersPage.selectUpload();
		
		websiteManager.confirmMemberUploadPage.selectUpload();
		
		websiteManager.memberListUploadedPage.selectDone();
	}

	@Then("^the members list is uploaded successfully$")
	public void the_members_list_is_uploaded_successfully() throws Throwable {
		LOG.info("Verify the Members List was Uploaded Successfully.");
		
		websiteManager.memberListPage.searchForAMember(DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME));
		Assert.assertTrue("FAILED: Business names are not displayed in the table.", websiteManager.memberListPage.checkMembersListUploaded());
	}
	
	@When("^the user changes the members list type to \"([^\"]*)\"$")
	public void the_user_changes_the_members_list_type_to(String listType) throws Throwable {
		LOG.info("Uploading a Members List CSV File to a Co-ordinated Partnership.");
		
		websiteManager.partnershipAdvancedSearchPage.selectOrganisationLink();
		websiteManager.partnershipInformationPage.selectChangeMembersListTypeLink();
		
		websiteManager.membersListTypePage.selectMemberListType(listType);
		websiteManager.membersListTypePage.clickContinue();
		
		websiteManager.memberListCountPage.clickContinue();
		
		websiteManager.membersListUpToDatePage.selectYesRadial();
		websiteManager.membersListUpToDatePage.clicksave();
	}

	@Then("^the members list type is changed successfully$")
	public void the_members_list_type_is_changed_successfully() throws Throwable {
		LOG.info("Verifying the Members List Type has been Changed Successfully.");
		
		String requestText = "Please request a copy of the Primary Authority Membership List from the co-ordinator. ";
		
		String copyAvailableText = "The co-ordinator must make the copy available as soon as reasonably practicable and, in any event, "
				+ "not later than the third working day after the date of receiving the request at no charge.";
		
		Assert.assertTrue("FAILED: Memebers List Type was not Changed.", websiteManager.partnershipInformationPage.checkMembersListType(requestText + copyAvailableText));
	}
	
	@Given("^the user clicks the PAR Home page link$")
	public void the_user_clicks_the_PAR_Home_page_link() throws Throwable {
		LOG.info("Click PAR header to navigate to the PAR Home Page");
		websiteManager.parAuthorityPage.selectPageHeader();
	}

	@When("^the user is on the search for a partnership page$")
	public void the_user_is_on_the_search_for_a_partnership_page() throws Throwable {
		LOG.info("Click Search Public List of Partnerships to navigate to PAR Search for Partnership Page");
		websiteManager.homePage.selectPartnershipSearchLink();
	}

	@When("^the user can search for a PA Organisation Trading name Company number$")
	public void the_user_can_search_for_a_PA_Organisation_Trading_name_Company_number() throws Throwable {
		LOG.info("Enter business name and click the search button");
		websiteManager.publicRegistrySearchPage.searchForPartnership(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		websiteManager.publicRegistrySearchPage.clickSearchButton();
	}

	@Then("^the user is shown the information for that partnership$")
	public void the_user_is_shown_the_information_for_that_partnership() throws Throwable {
		LOG.info("Verify the Partnership contains the business name");
		assertTrue("Failed: Organisation was not found.", websiteManager.publicRegistrySearchPage.partnershipContains(DataStore.getSavedValue(UsableValues.BUSINESS_NAME)));
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
		websiteManager.helpDeskDashboardPage.selectManageAuthorities();
		
		LOG.info("Select add authority.");
		websiteManager.authoritiesSearchPage.selectAddAuthority();
		
		LOG.info("Provide authority name.");
		websiteManager.authorityNamePage.enterAuthorityName(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		websiteManager.authorityNamePage.clickContinue();
		
		LOG.info("Provide authority type.");
		websiteManager.authorityTypePage.selectAuthorityType(DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE));
		websiteManager.authorityTypePage.clickContinue();
		
		LOG.info("Enter authority contact details.");
		
		websiteManager.addAddressPage.enterAddressDetails(DataStore.getSavedValue(UsableValues.AUTHORITY_ADDRESSLINE1), DataStore.getSavedValue(UsableValues.AUTHORITY_ADDRESSLINE2),
				DataStore.getSavedValue(UsableValues.AUTHORITY_TOWN), DataStore.getSavedValue(UsableValues.AUTHORITY_COUNTY), DataStore.getSavedValue(UsableValues.AUTHORITY_COUNTRY), 
				DataStore.getSavedValue(UsableValues.AUTHORITY_NATION), DataStore.getSavedValue(UsableValues.AUTHORITY_POSTCODE));
		websiteManager.addAddressPage.goToONSCodePage();
		
		LOG.info("Provide ONS code.");
		websiteManager.onsCodePage.enterONSCode(DataStore.getSavedValue(UsableValues.ONS_CODE));
		websiteManager.onsCodePage.clickContinue();
		
		LOG.info("Select regulatory function.");
		websiteManager.regulatoryFunctionPage.selectRegFunction(DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION));
	}

	@Then("^the authority is created sucessfully$")
	public void the_authority_is_created_sucessfully() throws Throwable {
		LOG.info("On the Authorities Dashboard.");
		Assert.assertTrue("Details don't check out", websiteManager.authorityConfirmationPage.checkAuthorityDetails());
		websiteManager.authorityConfirmationPage.saveChanges();
	}

	@When("^the user searches for the last created authority$")
	public void the_user_searches_for_the_last_created_authority() throws Throwable {
		LOG.info("Search for last created authority");
		websiteManager.authoritiesSearchPage.searchAuthority(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		websiteManager.authoritiesSearchPage.selectManageAuthority();
	}

	@When("^the user updates all the fields for newly created authority$")
	public void the_user_updates_all_the_fields_for_newly_created_authority() throws Throwable {
		LOG.info("Updating all editble fields against selected authority");
		DataStore.saveValue(UsableValues.AUTHORITY_NAME, DataStore.getSavedValue(UsableValues.AUTHORITY_NAME) + " Updated");
		DataStore.saveValue(UsableValues.AUTHORITY_TYPE, "District");
		DataStore.saveValue(UsableValues.ONS_CODE, DataStore.getSavedValue(UsableValues.ONS_CODE) + " Updated");
		DataStore.saveValue(UsableValues.AUTHORITY_REGFUNCTION, "Alphabet learning");
		
		websiteManager.authorityConfirmationPage.editAuthorityName();
		
		websiteManager.authorityNamePage.enterAuthorityName(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		websiteManager.authorityNamePage.clickSave();
		
		websiteManager.authorityConfirmationPage.editAuthorityType();
		
		websiteManager.authorityTypePage.selectAuthorityType(DataStore.getSavedValue(UsableValues.AUTHORITY_TYPE));
		websiteManager.authorityTypePage.clickSave();
		
		websiteManager.authorityConfirmationPage.editONSCode();
		
		websiteManager.onsCodePage.enterONSCode(DataStore.getSavedValue(UsableValues.ONS_CODE));
		websiteManager.onsCodePage.clickSave();
		
		websiteManager.authorityConfirmationPage.editRegFunction();
		
		websiteManager.regulatoryFunctionPage.editRegFunction(DataStore.getSavedValue(UsableValues.AUTHORITY_REGFUNCTION));
	}

	@Then("^the update for the authority is successful$")
	public void the_update_for_the_authority_is_successful() throws Throwable {
		LOG.info("Check all updated changes check out");
		
		Assert.assertTrue("Failed: Authority was not Updated.", websiteManager.authorityConfirmationPage.checkAuthorityDetails());
		websiteManager.authorityConfirmationPage.saveChanges();
	}
	
	@When("^the user searches for an Authority with the same Regulatory Functions \"([^\"]*)\"$")
	public void the_user_searches_for_an_Authority_with_the_same_Regulatory_Functions(String authority) throws Throwable {
		LOG.info("Search for the Authority.");
		websiteManager.helpDeskDashboardPage.selectManageAuthorities();
		websiteManager.authoritiesSearchPage.searchAuthority(authority);
		DataStore.saveValue(UsableValues.PREVIOUS_AUTHORITY_NAME, authority);
		
		websiteManager.authoritiesSearchPage.selectTransferPartnerships();
	}

	@When("^the user completes the partnership transfer process$")
	public void the_user_completes_the_partnership_transfer_process() throws Throwable {
		LOG.info("Transferring a Partnership to the new Authority.");
		
		websiteManager.authorityTransferSelectionPage.searchAuthority(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		websiteManager.partnershipMigrationSelectionPage.selectPartnership(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		
		LOG.info("Confirm the Partnership Transfer.");
		websiteManager.enterTheDatePage.goToConfirmThisTranferPage();
		websiteManager.confirmThisTranferPage.confirmPartnershipTransfer();
		websiteManager.transferCompletedPage.selectDoneButton();
	}

	@Then("^the partnership is transferred to the new authority successfully$")
	public void the_partnership_is_transferred_to_the_new_authority_successfully() throws Throwable {
		LOG.info("Search for the Partnership with the New Authority.");
		
		websiteManager.authoritiesSearchPage.goToHelpDeskDashboard();
		
		websiteManager.helpDeskDashboardPage.selectSearchPartnerships();
		websiteManager.partnershipAdvancedSearchPage.searchPartnershipsPrimaryAuthority();
		websiteManager.partnershipAdvancedSearchPage.selectPrimaryAuthorityLink();
		
		LOG.info("Verify the Partnership Displays the Previously Known as Text.");
		Assert.assertTrue("FAILED: Previously Known as text is not Displayed", websiteManager.partnershipInformationPage.checkPreviouslyKnownAsText());
	}
	
	@When("^the user searches for the last created organisation$")
	public void the_user_searches_for_the_last_created_organisation() throws Throwable {
		LOG.info("Search and select last created organisation");
		websiteManager.helpDeskDashboardPage.selectManageOrganisations();
		websiteManager.organisationsSearchPage.searchOrganisation();
		websiteManager.organisationsSearchPage.selectOrganisation();
	}
	
	@When("^the user updates all the fields for last created organisation$")
	public void the_user_updates_all_the_fields_for_last_created_organisation() throws Throwable {
		LOG.info("Update all the organisation fields.");
		
		DataStore.saveValue(UsableValues.BUSINESS_NAME, DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + " Updated");
		DataStore.saveValue(UsableValues.BUSINESS_DESC, DataStore.getSavedValue(UsableValues.BUSINESS_DESC) + " Updated");
		DataStore.saveValue(UsableValues.TRADING_NAME, DataStore.getSavedValue(UsableValues.TRADING_NAME) + " Updated");
		DataStore.saveValue(UsableValues.SIC_CODE, "allow people to eat");
		
		websiteManager.businessDetailsPage.editOrganisationName();
		websiteManager.businessNamePage.enterBusinessName(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		websiteManager.businessNamePage.goToBusinessConfirmationPage();
		
		websiteManager.businessDetailsPage.editOrganisationDesc();
		websiteManager.aboutTheOrganisationPage.enterDescription(DataStore.getSavedValue(UsableValues.BUSINESS_DESC));
		websiteManager.aboutTheOrganisationPage.goToBusinessDetailsPage();
		
		websiteManager.businessDetailsPage.editTradingName();
		websiteManager.tradingPage.editTradingName(DataStore.getSavedValue(UsableValues.TRADING_NAME));
		websiteManager.tradingPage.goToBusinessDetailsPage();
		
		websiteManager.businessDetailsPage.editSICCode();
		websiteManager.sicCodePage.selectPrimarySICCode(DataStore.getSavedValue(UsableValues.SIC_CODE)); 
		websiteManager.sicCodePage.goToBusinessDetailsPage();
	}
	
	@Then("^all the fields are updated correctly$")
	public void all_the_fields_are_updated_correctly() throws Throwable {
		LOG.info("Check all updated changes check out");
		
		Assert.assertTrue("Failed: Organisation Details where not Updated.", websiteManager.businessDetailsPage.checkAuthorityDetails());
		websiteManager.businessDetailsPage.saveChanges();
	}
	
	@When("^the user selects a contact to update$")
	public void the_user_selects_a_contact_to_update() throws Throwable {
		DataStore.saveValue(UsableValues.BUSINESS_EMAIL, DataStore.getSavedValue(UsableValues.LOGIN_USER));
		websiteManager.helpDeskDashboardPage.selectManageProfileDetails();
		
		LOG.info("Selecting a Contact to Update.");
		websiteManager.contactRecordsPage.selectContactToUpdate();
		websiteManager.contactRecordsPage.selectContinueButton();
		
		LOG.info("Click Continue to Accept the Contact Details.");
		websiteManager.contactDetailsPage.goToContactCommunicationPreferencesPage();
		websiteManager.contactCommunicationPreferencesPage.selectContinueButton();
	}

	@Then("^the user can successfully subscribe to PAR News$")
	public void the_user_can_successfully_subscribe_to_PAR_News() throws Throwable {
		LOG.info("Click the Checkbox to Subscribe to the PAR News Letter.");
		websiteManager.contactUpdateSubscriptionPage.subscribeToPARNews();
		websiteManager.contactUpdateSubscriptionPage.selectContinueButton();
		
		LOG.info("Successfully subscribed from PAR news letter.");
		websiteManager.profileReviewPage.goToProfileCompletionPage();
		websiteManager.profileCompletionPage.goToDashboardPage();
	}

	@Then("^the user can successfully unsubscribe from PAR News$")
	public void the_user_can_successfully_unsubscribe_from_PAR_News() throws Throwable {
		LOG.info("Click the Checkbox to Unsubscribe from the PAR News Letter.");
		websiteManager.contactUpdateSubscriptionPage.unsubscribeFromPARNews();
		websiteManager.contactUpdateSubscriptionPage.selectContinueButton();
		
		LOG.info("Successfully unsubscribed from PAR news letter.");
		websiteManager.profileReviewPage.goToProfileCompletionPage();
		websiteManager.profileCompletionPage.goToDashboardPage();
	}
	
	@When("^the user is on the Subscriptions page$")
	public void the_user_is_on_the_Subscriptions_page() throws Throwable {
		LOG.info("Navigate to the Manage Subscriptions Page.");
		websiteManager.helpDeskDashboardPage.selectManageSubscriptions();
	}
	
	@When("^the user searches for the par_authority email$")
	public void the_user_searches_for_the_par_authority_email() throws Throwable {
		websiteManager.newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
		websiteManager.newsLetterSubscriptionPage.ClickSearchButton();
		
		LOG.info("Searching for the Authority Email: " + DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
	}
	
	@Then("^the user can verify the email is successfully in the Subscriptions List$")
	public void the_user_can_verify_the_email_is_successfully_in_the_Subscriptions_List() throws Throwable {
		LOG.info("Assert the Email is successfully added to the Subscription List.");
		assertTrue("Failed: Email address was not added to the PAR News Subscription List.", websiteManager.newsLetterSubscriptionPage.verifyTableElementExists());
	}

	@Then("^the user can verify the email is successfully removed from the Subscriptions List$")
	public void the_user_can_verify_the_email_is_successfully_removed_from_the_Subscriptions_List() throws Throwable {
		LOG.info("Assert the Email is removed successfully from the Subscription List.");
		assertTrue("Failed: Email address was not removed from the PAR News Subscription List.", websiteManager.newsLetterSubscriptionPage.verifyTableElementIsNull());
	}

	@When("^the user is on the Manage a subscription list page$")
	public void the_user_is_on_the_Manage_a_subscription_list_page() throws Throwable {
		LOG.info("Navigate to Manage Subscriptions Page.");
		websiteManager.helpDeskDashboardPage.selectManageSubscriptions();
		websiteManager.newsLetterSubscriptionPage.selectManageSubsciptions();
		
		LOG.info("Email with the largest number: " + DataStore.getSavedValue(UsableValues.LAST_PAR_NEWS_EMAIL));
	}

	@When("^the user enters a new email to add to the list \"([^\"]*)\"$")
	public void the_user_enters_a_new_email_to_add_to_the_list(String email) throws Throwable {
		LOG.info("Adding a new email to the subscription list.");
		DataStore.saveValue(UsableValues.PAR_NEWS_EMAIL, email);
		
		websiteManager.newsLetterManageSubscriptionListPage.selectInsertNewEmailRadioButton();
		websiteManager.newsLetterManageSubscriptionListPage.AddNewEmail(email);
		websiteManager.newsLetterManageSubscriptionListPage.clickContinueButton();
		
		websiteManager.newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify the new email was added successfully$")
	public void the_user_can_verify_the_new_email_was_added_successfully() throws Throwable {
		LOG.info("Verify the new email was added to the Subscription list.");
		websiteManager.newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.PAR_NEWS_EMAIL));
		websiteManager.newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue("Failed: Email was not added to the Subscriptions List.", websiteManager.newsLetterSubscriptionPage.verifyTableElementExists());
	}

	@When("^the user enters an email to be removed from the list \"([^\"]*)\"$")
	public void the_user_enters_an_email_to_be_removed_from_the_list(String email) throws Throwable {
		LOG.info("Removing an email from the subscription list.");
		DataStore.saveValue(UsableValues.PAR_NEWS_EMAIL, email);
		
		websiteManager.newsLetterManageSubscriptionListPage.selectRemoveEmailRadioButton();
		websiteManager.newsLetterManageSubscriptionListPage.RemoveEmail(email);
		websiteManager.newsLetterManageSubscriptionListPage.clickContinueButton();
		
		websiteManager.newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify the email was removed successfully$")
	public void the_user_can_verify_the_email_was_removed_successfully() throws Throwable {
		LOG.info("Verify the email was removed from the Subscription list.");
		websiteManager.newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.PAR_NEWS_EMAIL));
		websiteManager.newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue("Failed: Email was not Removed from the Subscriptions List.", websiteManager.newsLetterSubscriptionPage.verifyTableElementIsNull());
	}

	@When("^the user enters a list of new emails to replace the subscription list$")
	public void the_user_enters_a_list_of_new_emails_to_replace_the_subscription_list() throws Throwable {
		LOG.info("Adding a new list of emails to replace the original Subscription List.");
		websiteManager.newsLetterManageSubscriptionListPage.selectReplaceSubscriptionListRadioButton();
		websiteManager.newsLetterManageSubscriptionListPage.clickContinueButton();
		
		websiteManager.newsLetterSubscriptionReviewPage.clickUpdateListButton();
	}

	@Then("^the user can verify an email from the original list was removed successfully$")
	public void the_user_can_verify_an_email_from_the_original_list_was_removed_successfully() throws Throwable {
		LOG.info("Verify the Subscription list was replaced with the new list.");
		websiteManager.newsLetterSubscriptionPage.EnterEmail(DataStore.getSavedValue(UsableValues.LAST_PAR_NEWS_EMAIL));
		websiteManager.newsLetterSubscriptionPage.ClickSearchButton();

		assertTrue("Failed: The new list did not replace the original list.", websiteManager.newsLetterSubscriptionPage.verifyTableElementIsNull());
	}
	
	@When("^the user searches for the \"([^\"]*)\" user account$")
	public void the_user_searches_for_the_user_account(String userEmail) throws Throwable {
		LOG.info("Searching for the user.");
		
		switch (DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
		case ("par_helpdesk@example.com"):
		case ("senior_administrator@example.com"):
		case ("secretary_state@example.com"):
			LOG.info("Selecting Manage people.");
			websiteManager.helpDeskDashboardPage.selectManagePeople();
			break;
		case ("par_authority_manager@example.com"):
		case ("par_authority@example.com"):
		case ("par_business_manager@example.com"):
		case ("par_business@example.com"):
			LOG.info("Selecting Manage Colleagues.");
			websiteManager.dashboardPage.selectManageColleagues();
			break;
		}
		
		DataStore.saveValue(UsableValues.PERSON_EMAIL_ADDRESS, userEmail);
		
		websiteManager.managePeoplePage.enterNameOrEmail(userEmail);
		websiteManager.managePeoplePage.clickSubmit();
		
		
	}

	@When("^the user clicks the manage contact link$")
	public void the_user_clicks_the_manage_contact_link() throws Throwable {
		LOG.info("Clicking the Manage Contact link.");
		DataStore.saveValue(UsableValues.PERSON_FULLNAME_TITLE, websiteManager.managePeoplePage.GetPersonName());
		websiteManager.managePeoplePage.clickManageContact();
	}

	@Then("^the user can view the user account successfully$")
	public void the_user_can_view_the_user_account_successfully() throws Throwable {
		LOG.info("Verify the correct user profile is displayed.");
	    assertEquals(DataStore.getSavedValue(UsableValues.PERSON_EMAIL_ADDRESS), websiteManager.userProfilePage.getUserAccountEmail());
	}

	@When("^the user changes the users role to \"([^\"]*)\"$")
	public void the_user_changes_the_users_role_to(String roleType) throws Throwable {
		LOG.info("Selecting the Manage Roles Link.");
		websiteManager.userProfilePage.clickManageRolesLink();
		
		LOG.info("Deselecting all roles.");
		websiteManager.userTypePage.deselectAllMemberships();
		
		LOG.info("Selecting the new role.");
		DataStore.saveValue(UsableValues.ACCOUNT_TYPE, roleType);
		websiteManager.userTypePage.chooseMembershipRole(roleType);
		websiteManager.userTypePage.goToUserProfilePage();
	}

	@Then("^the user role was changed successfully$")
	public void the_user_role_was_changed_successfully() throws Throwable {
		LOG.info("Verify the User Account Type was changed successfully.");
		
	    assertTrue(websiteManager.userProfilePage.checkUserAccountType());
	}
	
	@When("^the user adds a new Authority membership$")
	public void the_user_adds_a_new_Authority_membership() throws Throwable {
		websiteManager.userProfilePage.clickAddMembershipLink();
		
		LOG.info("Choosing the person to add the new membership to.");
		websiteManager.choosePersonToAddPage.choosePerson();
		websiteManager.choosePersonToAddPage.clickContinueButton();
		
		LOG.info("Choosing the new Authority Membership.");
		websiteManager.userMembershipPage.chooseAuthorityMembership(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		websiteManager.userMembershipPage.clickContinueButton();
		
		websiteManager.addMembershipConfirmationPage.clickContinueButton();
	}

	@Then("^the Authority membership was added successfully$")
	public void the_Authority_membership_was_added_successfully() throws Throwable {
		LOG.info("Verify the new Authority membership was added successfully.");
		
		assertTrue(websiteManager.userProfilePage.checkUserMembershipDisplayed());
	}

	@When("^the user removes the last added Authority membership$")
	public void the_user_removes_the_last_added_Authority_membership() throws Throwable {
		LOG.info("Remove the last added Authority Membership.");
		websiteManager.userProfilePage.clickRemoveMembershipLink();
		websiteManager.removePage.goToUserProfilePage();
	}

	@Then("^the Authority membership was removed successfully$")
	public void the_Authority_membership_was_removed_successfully() throws Throwable {
		LOG.info("Verify the Authority membership was removed successfully.");
		
		assertTrue(websiteManager.userProfilePage.checkMembershipRemoved());
	}
	
	@When("^the user blocks the user account$")
	public void the_user_blocks_the_user_account() throws Throwable {
		LOG.info("Block the User Account.");
		websiteManager.userProfilePage.clickBlockUserAccountLink();
		websiteManager.blockPage.goToUserProfilePage();
	}

	@Then("^the user verifies the account was blocked successfully$")
	public void the_user_verifies_the_account_was_blocked_successfully() throws Throwable {
		LOG.info("Verifying the User account was blocked.");
		
		assertTrue(websiteManager.userProfilePage.checkUserAccountIsNotActive());
		assertTrue(websiteManager.userProfilePage.checkReactivateUserAccountLinkIsDisplayed());
	}

	@Then("^the user cannot sign in and receives an error message$")
	public void the_user_cannot_sign_in_and_receives_an_error_message() throws Throwable {
		LOG.info("Verifying the User cannot sign in and receives an error message.");
		
		assertTrue(websiteManager.loginPage.checkErrorSummary("The username national_regulator@example.com has not been activated or is blocked."));
		assertTrue(websiteManager.loginPage.checkErrorMessage("The username national_regulator@example.com has not been activated or is blocked."));
	}

	@When("^the user reinstates the user account$")
	public void the_user_reinstates_the_user_account() throws Throwable {
		LOG.info("Re-activate the User Account.");
		websiteManager.userProfilePage.clickReactivateUserAccountLink();
		websiteManager.reinstatePage.goToUserProfilePage();
	}

	@Then("^the user verifies the account is reinstated successfully$")
	public void the_user_verifies_the_account_is_reinstated_successfully() throws Throwable {
		LOG.info("Verifying the User account has been re-activated.");
		
		assertTrue(websiteManager.userProfilePage.checkLastSignInHeaderIsDisplayed());
		assertTrue(websiteManager.userProfilePage.checkBlockUserAccountLinkIsDisplayed());
	}
	
	@When("^the user creates a new person:$")
	public void the_user_creates_a_new_person(DataTable details) throws Throwable {
		websiteManager.helpDeskDashboardPage.selectManagePeople();
		websiteManager.managePeoplePage.selectAddPerson();
		
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
		websiteManager.contactDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		websiteManager.contactDetailsPage.enterFirstName(DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME));
		websiteManager.contactDetailsPage.enterLastName(DataStore.getSavedValue(UsableValues.PERSON_LASTNAME));
		websiteManager.contactDetailsPage.enterWorkNumber(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		websiteManager.contactDetailsPage.enterMobileNumber(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		websiteManager.contactDetailsPage.enterEmail(DataStore.getSavedValue(UsableValues.PERSON_EMAIL_ADDRESS));
		
		websiteManager.contactDetailsPage.goToUserProfilePage();
	}

	@Then("^the user can verify the person was created successfully and can send an account invitation$")
	public void the_user_can_verify_the_person_was_created_successfully_and_can_send_an_account_invitation() throws Throwable {
		
		assertTrue("Failed: Header does not contain the person's fullname and title.", websiteManager.userProfilePage.checkHeaderForName());
		assertTrue("Failed: Cannot find the User account invitation link.", websiteManager.userProfilePage.checkForUserAccountInvitationLink());
		assertTrue("Failed: Contact name field does not contain the person's fullname and title.", websiteManager.userProfilePage.checkContactName());
		assertTrue("Failed: Contact email field does not contain the correct email address.", websiteManager.userProfilePage.checkContactEmail());
		assertTrue("Failed: Contact numbers field does not contain the work and/or mobile phone numbers", websiteManager.userProfilePage.checkContactPhoneNumbers());
		assertTrue("Failed: Contact Locations are displayed.", websiteManager.userProfilePage.checkContactLocationsIsEmpty());
		
		websiteManager.userProfilePage.clickDoneButton();
	}

	@When("^the user searches for an existing person successfully$")
	public void the_user_searches_for_an_existing_person_successfully() throws Throwable {

		String personsName = DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.PERSON_LASTNAME);

		websiteManager.managePeoplePage.enterNameOrEmail(personsName);
		websiteManager.managePeoplePage.clickSubmit();

		websiteManager.managePeoplePage.clickManageContact();

		LOG.info("Found an existing user with the name: " + personsName);
	}

	@When("^the user updates an existing person:$")
	public void the_user_updates_an_existing_person_with_the_following_details(DataTable details) throws Throwable {
		LOG.info("Updating an existing person.");
		websiteManager.userProfilePage.clickUpdateUserButton();
		
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
		websiteManager.contactDetailsPage.enterTitle(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		websiteManager.contactDetailsPage.enterFirstName(DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME));
		websiteManager.contactDetailsPage.enterLastName(DataStore.getSavedValue(UsableValues.PERSON_LASTNAME));
		websiteManager.contactDetailsPage.enterWorkNumber(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		websiteManager.contactDetailsPage.enterMobileNumber(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		websiteManager.contactDetailsPage.enterEmail(DataStore.getSavedValue(UsableValues.PERSON_EMAIL_ADDRESS));
		
		websiteManager.contactDetailsPage.goToUserProfilePage();
	}

	@Then("^the user can verify the person was updated successfully and can send an account invitation$")
	public void the_user_can_verify_the_person_was_updated_successfully_and_can_send_an_account_invitation() throws Throwable {
		assertTrue("Failed: Header does not contain the person's fullname and title.", websiteManager.userProfilePage.checkHeaderForName());
		assertTrue("Failed: Cannot find the User account invitation link.", websiteManager.userProfilePage.checkForUserAccountInvitationLink());
		assertTrue("Failed: Contact name field does not contain the person's fullname and title.", websiteManager.userProfilePage.checkContactName());
		assertTrue("Failed: Contact email field does not contain the correct email address.", websiteManager.userProfilePage.checkContactEmail());
		assertTrue("Failed: Contact numbers field does not contain the work and/or mobile phone numbers", websiteManager.userProfilePage.checkContactPhoneNumbers());
		assertTrue("Failed: Contact Locations are displayed.", websiteManager.userProfilePage.checkContactLocationsIsEmpty());
	}
	
	@When("^the user updates their user account email address to \"([^\"]*)\"$")
	public void the_user_updates_their_user_account_email_address_to(String newEmail) throws Throwable {
		LOG.info("Update the User Account Email Address.");
		websiteManager.dashboardPage.selectManageProfileDetails();
		
		websiteManager.contactRecordsPage.selectContactToUpdate();
		websiteManager.contactRecordsPage.selectContinueButton();
		
		websiteManager.contactDetailsPage.enterEmailAddress(newEmail);
		websiteManager.contactDetailsPage.goToContactCommunicationPreferencesPage();
		
		websiteManager.contactCommunicationPreferencesPage.selectContinueButton();
		websiteManager.contactUpdateSubscriptionPage.selectContinueButton();
		
		websiteManager.profileReviewPage.confirmUserAccountEmail();
		websiteManager.profileReviewPage.goToProfileCompletionPage();
		
		websiteManager.profileCompletionPage.goToDashboardPage();
	}

	@Then("^the user can verify the new email address is displayed on the header$")
	public void the_user_can_verify_the_new_email_address_is_displayed_on_the_header() throws Throwable {
		LOG.info("Verifying the User Account Email was Updated Successfully.");
		
		Assert.assertTrue("Failed: The new User Account email address is not Displayed.", websiteManager.dashboardPage.checkUserAccountEmailAddress());
	}
	
	@When("^the user navigates to the statistics page$")
	public void the_user_navigates_to_the_statistics_page() throws Throwable {
		LOG.info("Navigating to the Statistics Page.");
		websiteManager.helpDeskDashboardPage.selectViewAllStatistics();
	}

	@Then("^the statistics page is dispalyed successfully$")
	public void the_statistics_page_is_dispalyed_successfully() throws Throwable {
		LOG.info("Verifying the Statistics Page is Displayed.");
		Assert.assertTrue("FAILED: Statistics Page is not Displayed!", websiteManager.parReportingPage.checkPageHeaderIsDisplayed());
	}
	
	@When("^the user selects the Read more about Primary Authority link$")
	public void the_user_selects_the_Read_more_about_Primary_Authority_link() throws Throwable {
		LOG.info("Selecting the Read More About Primary Authority Link.");
		
		websiteManager.homePage.selectReadMoreAboutPrimaryAuthorityLink();
	}

	@Then("^the user is taken to the GOV\\.UK Guidance page for Local regulation Primary Authority Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Guidance_page_for_Local_regulation_Primary_Authority_Successfully() throws Throwable {
		LOG.info("Verifying the Local regulation: Primary Authority Page is Displayed.");
		
		Assert.assertTrue("Failed: Local Regulation Primary Authority Header is not Displayed.", websiteManager.localRegulationPrimaryAuthorityPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Access tools and templates for local authorities link$")
	public void the_user_selects_the_Access_tools_and_templates_for_local_authorities_link() throws Throwable {
		LOG.info("Selecting the Access Tools and Templates Link.");
		
		websiteManager.homePage.selectAccessToolsAndTemplatesLink();
	}

	@Then("^the user is taken to the GOV\\.UK Collection page for Primary Authority Documents Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Collection_page_for_Primary_Authority_Documents_Successfully() throws Throwable {
		LOG.info("Verifying the Primary Authority documents page is Displayed.");
		
		Assert.assertTrue("Failed: Primary Authority Documents Header is not Displayed.", websiteManager.primaryAuthorityDocumentsPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Terms and Conditions link$")
	public void the_user_selects_the_Terms_and_Conditions_link() throws Throwable {
		LOG.info("Selecting the Terms and Conditions Link.");
		
		websiteManager.homePage.selectTermsAndConditionsLink();
	}

	@Then("^the user is taken to the GOV\\.UK Guidance page for Primary Authority terms and conditions Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Guidance_page_for_Primary_Authority_terms_and_conditions_Successfully() throws Throwable {
		LOG.info("Verifying the Primary Authority terms and conditions page is Displayed.");
		
		Assert.assertTrue("Failed: Primary Authority Terms and Conditions Header is not Displayed.", websiteManager.termsAndConditionsPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Cookies link$")
	public void the_user_selects_the_Cookies_link() throws Throwable {
		LOG.info("Selecting the Cookies Link from the Footer.");
		
		websiteManager.homePage.selectCookiesFooterLink();
	}

	@Then("^the user is taken to the Cookies page and can accept the Analytics Cookies Successfully$")
	public void the_user_is_taken_to_the_Cookies_page_and_can_accept_the_Analytics_Cookies_Successfully() throws Throwable {
		LOG.info("Verifying the Cookies page is Displayed and the User Accepts the Analytics Cookies.");
		
		Assert.assertTrue("Failed: Cookies Header is not Displayed.", websiteManager.cookiesPage.checkPageHeaderDisplayed());
		
		websiteManager.cookiesPage.acceptCookies();
		websiteManager.cookiesPage.selectSaveButton();
	}

	@When("^the user selects the Privacy link$")
	public void the_user_selects_the_Privacy_link() throws Throwable {
		LOG.info("Selecting the Privacy Link.");
		
		websiteManager.homePage.selectPrivacyLink();
	}

	@Then("^the user is taken to the GOV\\.UK Corporate report OPSS Privacy notice page Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Corporate_report_OPSS_Privacy_notice_page_Successfully() throws Throwable {
		LOG.info("Verifying the OPSS: privacy notice page is Displayed.");
		
		Assert.assertTrue("Failed: The OPSS Privacy Notice Header is not Dispalyed.", websiteManager.opssPrivacyNoticePage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Accessibility link$")
	public void the_user_selects_the_Accessibility_link() throws Throwable {
		LOG.info("Selecting the Accessibility Link.");
		
		websiteManager.homePage.selectAccessibilityLink();
	}

	@Then("^the user is taken to the GOV\\.UK Guidance page for the Primary Authority Register accessibility statement Successfully$")
	public void the_user_is_taken_to_the_GOV_UK_Guidance_page_for_the_Primary_Authority_Register_accessibility_statement_Successfully() throws Throwable {
		LOG.info("Verifying the Primary Authority Register: accessibility statement page is Displayed.");
		
		Assert.assertTrue("Failed: Accessibility Statement Header is not Displayed.", websiteManager.accessibilityStatementPage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Open Government Licence link$")
	public void the_user_selects_the_Open_Government_Licence_link() throws Throwable {
		LOG.info("Selecting the Open Government Licence Link.");
		
		websiteManager.homePage.selectOpenGovernmentLicenceLink();
	}

	@Then("^the user is taken to the Open Government Licence for public sector information page Successfully$")
	public void the_user_is_taken_to_the_Open_Government_Licence_for_public_sector_information_page_Successfully() throws Throwable {
		LOG.info("Verifying the Open Government Licence page is Displayed.");
		
		Assert.assertTrue("Failed: The Open Government Licence Header is not Displayed.", websiteManager.openGovernmentLicencePage.checkPageHeaderDisplayed());
	}

	@When("^the user selects the Crown copyright link$")
	public void the_user_selects_the_Crown_copyright_link() throws Throwable {
		LOG.info("Selecting the Crown copyright Link.");
		
		websiteManager.homePage.selectCrownCopyrightLink();
	}

	@Then("^the user is taken to the Crown copyright page Successfully$")
	public void the_user_is_taken_to_the_Crown_copyright_page_Successfully() throws Throwable {
		LOG.info("Verifying the Crown copyright page is Displayed.");
		
		Assert.assertTrue("Failed: The Crown Copright Header is not Displayed.", websiteManager.crownCopyrightPage.checkPageHeaderDisplayed());
	}
}
