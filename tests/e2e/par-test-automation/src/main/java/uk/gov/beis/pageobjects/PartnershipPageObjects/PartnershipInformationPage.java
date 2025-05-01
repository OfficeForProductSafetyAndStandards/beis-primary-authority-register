package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class PartnershipInformationPage extends BasePageObject {

	@FindBy(linkText = "edit about the partnership")
	private WebElement editPartnershipLink;

	@FindBy(linkText = "edit the regulatory functions")
	private WebElement editRegulatoryFunctionsLink;

	@FindBy(linkText = "edit address")
	private WebElement editOrganisationAddressLink;

	@FindBy(linkText = "edit about the organisation")
	private WebElement editAboutOrganisationLink;

	@FindBy(linkText = "edit sic code")
	private WebElement editSICCodeLink;

	@FindBy(linkText = "show members list")
	private WebElement showMembersListLink;

	@FindBy(linkText = "change the list type")
	private WebElement changeMembersListTypeLink;

	//@FindBy(xpath = "//a[contains(normalize-space(), 'Amend the legal entities')]")
	@FindBy(linkText = "Amend the legal entities") // Amend the legal entities
	private WebElement amendLegalEntitiesLink;

	@FindBy(linkText = "Confirm the amendments")
	private WebElement confirmLegalEntitiesLink;

	@FindBy(linkText = "Nominate the amendments")
	private WebElement nominateLegalEntitiesLink;

	@FindBy(linkText = "edit trading name")
	private WebElement editTradingNameLink;

	@FindBy(linkText = "add another trading name")
	private WebElement addAnotherTradingNameLink;

	// Data Fields
	@FindBy(id = "edit-regulatory-functions")
	private WebElement regulatoryFunctionText;

	@FindBy(id = "edit-about")
	private WebElement aboutOrganisationText;

	@FindBy(id = "edit-par-component-partnership-members")
	private WebElement partnershipMembersSection;

	@FindBy(id = "edit-trading-names")
	private WebElement tradingNameText;

	@FindBy(linkText = "add another authority contact")
	private WebElement addAuthorityContactLink;

	@FindBy(linkText = "add another organisation contact")
	private WebElement addOrganisationContactLink;

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	private WebElement saveBtn;

	@FindBy(linkText = "Send a notification of a proposed enforcement action")
	private WebElement craeteEnforcementBtn;

	@FindBy(linkText = "Submit feedback following an inspection")
	private WebElement sendInspectionFeedbackBtn;

	@FindBy(linkText = "Request to deviate from the inspection plan")
	private WebElement reqToDeviateFromInspectionPlan;

	@FindBy(linkText = "See all Inspection Plans")
	private WebElement seeAllInspectionPlans;

	@FindBy(linkText = "See all Advice")
	private WebElement seeAllAdvice;

	@FindBy(linkText = "Send a general enquiry to the primary authority")
	private WebElement generalEnquiryLink;

	@FindBy(linkText = "Done")
	private WebElement doneBtn;

	@FindBy(id = "edit-done")
	private WebElement saveButton;

	private String authorityNameLocator = "//div/h2[contains(text(),'?')]";

	private String partnershipDetails = "//div/p[contains(text(),'?')]";
	private String partnershipRegFunc = "//ul/li[contains(text(),'?')]";
	private String aboutTheOrganisation = "//div/p[contains(text(),'?')]";

	private String businessAddress1 = "//div/p[contains(text(),'?')]";
	private String businessAddress2 = "//div/p[contains(text(),'?')]";
	private String businessTown1 = "//div/p[contains(text(),'?')]";
	private String businessPCode = "//div/p[contains(text(),'?')]";
	private String businessCountry = "//div/p[contains(text(),'?')]";

	private String sic = "//div/p[contains(text(),'?')]";

	private String legalEntityNameLocator = "//tr/td/div/div/div[contains(text(), '?')]";
	private String legalEntityStatusLocator = "./../../../../td/span[contains(text(), '?')]";
	private String legalEntityActionLinksLocator = "./../../../../td/div/p/a[contains(text(), '?')]";

	private String tradename = "//div/p[contains(text(),'?')]";

	private String contactFullName = "//div[contains(text(),'?')]";
	private String contactWorkNumber = "//div[contains(text(),'?')]";
	private String contactMobileNumber = "//div[contains(text(),'?')]";
	private String contactEmailAddress = "//a[contains(text(),'?')]";
	private String contactCommunicationNotes = "//p[contains(text(),'?')]";
	private String editContactLink = "//a[contains(text(),'?')]";
	private String removeContactLink = "//a[contains(text(),'?')]";

	public PartnershipInformationPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void createEnforcement() {
		craeteEnforcementBtn.click();
	}

	public void confirmDetailsAsAuthority() {
		WebElement checkbox = driver.findElement(By.id("edit-terms-authority-agreed"));

		if (!checkbox.isSelected())
		{
			checkbox.click();
		}
	}

	public void confirmDetails() {
		WebElement checkbox = ScenarioContext.secondJourneyPart ? driver.findElement(By.id("edit-terms-organisation-agreed")) : driver.findElement(By.id("edit-terms-authority-agreed"));

		if (!checkbox.isSelected())
		{
			checkbox.click();
		}
	}

	public void saveChanges() {
		saveBtn.click();
	}

	public void selectSeeAllInspectionPlans() {
		seeAllInspectionPlans.click();
	}

	public void selectSeeAllAdviceNotices() {
		seeAllAdvice.click();
	}

	public void sendGeneralEnquiry() {
		generalEnquiryLink.click();
	}

	public void selectSendInspectionFeedbk() {
		sendInspectionFeedbackBtn.click();
	}

	public void clickSendInspectionFeedbk() {
		reqToDeviateFromInspectionPlan.click();
	}

	public void selectDeviateInspectionPlan() {
		reqToDeviateFromInspectionPlan.click();
	}

	public void clickDeviateInspectionPlan() {
		reqToDeviateFromInspectionPlan.click();
	}

	// Update Partnership Details
	public void editAboutPartnership() {
		editPartnershipLink.click();
	}

	public void editRegulatoryFunctions() {
        waitForElementToBeClickable(By.linkText("edit the regulatory functions"), 2000);
		editRegulatoryFunctionsLink.click();
	}

	public void editOrganisationAddress() {
		editOrganisationAddressLink.click();
	}

	public void editAboutTheOrganisation() {
		editAboutOrganisationLink.click();
	}

	public void editSICCode() {
		editSICCodeLink.click();
	}

	public void selectShowMembersListLink()
    {
        waitForElementToBeVisible(By.linkText("show members list"), 2000);
		showMembersListLink.click();
	}

	public void selectChangeMembersListTypeLink() {
		changeMembersListTypeLink.click();
	}

	public void selectAmendLegalEntitiesLink() {
		amendLegalEntitiesLink.click();
	}

	public void selectConfirmLegalEntitiesLink() {
		confirmLegalEntitiesLink.click();
	}

	public void selectNominateLegalEntitiesLink() {
		nominateLegalEntitiesLink.click();
	}

	public void selectRevokeLegalEntitiesLink() {
		WebElement legalEntityName = driver.findElement(By.xpath(legalEntityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));

		WebElement revokeLink = legalEntityName.findElement(By.xpath(legalEntityActionLinksLocator.replace("?", "Revoke")));
		revokeLink.click();
	}

	public void selectReinstateLegalEntitiesLink() {
		WebElement legalEntityName = driver.findElement(By.xpath(legalEntityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));

		WebElement revokeLink = legalEntityName.findElement(By.xpath(legalEntityActionLinksLocator.replace("?", "Reinstate")));
		revokeLink.click();
	}

	public void selectRemoveLegalEntitiesLink() {
		WebElement legalEntityName = driver.findElement(By.xpath(legalEntityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));

		WebElement revokeLink = legalEntityName.findElement(By.xpath(legalEntityActionLinksLocator.replace("?", "Remove")));
		revokeLink.click();
	}

	public void editTradingName() {
		editTradingNameLink.click();
	}

	public void addAnotherTradingName() {
		addAnotherTradingNameLink.click();
	}

	public void addAnotherAuthorityContactButton() {
		addAuthorityContactLink.click();
	}

	public void addAnotherOrganisationContactButton() {
		addOrganisationContactLink.click();
	}

	public void editContactsDetailsButton() {
		WebElement editLink = driver.findElement(By.xpath(editContactLink.replace("?", "edit " + getContactsName().toLowerCase())));
		editLink.click();
	}

	public void removeContactsDetailsButton() {
		WebElement removeLink = driver.findElement(By.xpath(removeContactLink.replace("?", "remove " + getContactsName().toLowerCase())));
		removeLink.click();
	}

	// Check Partnership Details
	public boolean verifyOrganisationName() {

		return driver.findElement(By.id("block-par-theme-page-title")).isDisplayed();
	}

	public boolean verifyPrimaryAuthorityName() {
		WebElement authority = driver.findElement(By.xpath(authorityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_NAME))));
		return authority.isDisplayed();
	}

	public boolean verifyAboutThePartnership() {
		WebElement partnershipDets = driver.findElement(By.xpath(partnershipDetails.replace("?", DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO))));
		return partnershipDets.isDisplayed();
	}

	public boolean checkRegulatoryFunctions() {
        waitForElementToBeVisible(By.xpath(partnershipRegFunc.replace("?", DataStore.getSavedValue(UsableValues.PARTNERSHIP_REGFUNC))), 3000);
		regulatoryFunctionText = driver.findElement(By.xpath(partnershipRegFunc.replace("?", DataStore.getSavedValue(UsableValues.PARTNERSHIP_REGFUNC))));
		return regulatoryFunctionText.isDisplayed();
	}

	public boolean checkOrganisationAddress() {
		WebElement addressLine1 = driver.findElement(By.xpath(businessAddress1.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1))));
		WebElement addressLine2 = driver.findElement(By.xpath(businessAddress2.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2))));
		WebElement townCity = driver.findElement(By.xpath(businessTown1.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_TOWN))));
		WebElement postCode = driver.findElement(By.xpath(businessPCode.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE))));
		WebElement country = driver.findElement(By.xpath(businessCountry.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY))));

		// There is no County or Nation displayed

		return (addressLine1.isDisplayed() && addressLine2.isDisplayed() && townCity.isDisplayed() && postCode.isDisplayed() && country.isDisplayed());
	}

	public boolean checkAboutTheOrganisation() {
		aboutOrganisationText = driver.findElement(By.xpath(aboutTheOrganisation.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_DESC))));
		return aboutOrganisationText.isDisplayed();
	}

	public boolean checkSICCode() {
		WebElement sicCd = driver.findElement(By.xpath(sic.replace("?", DataStore.getSavedValue(UsableValues.SIC_CODE))));
		return sicCd.isDisplayed();
	}

	public boolean checkMembersListType(String text) {
		String listTypeLocator = "//div[@id='edit-list']/p[normalize-space()='?']";

		return partnershipMembersSection.findElement(By.xpath(listTypeLocator.replace("?", text))).isDisplayed();
	}

	public boolean verifyLegalEntity(String status) {
		WebElement legalEntityName = driver.findElement(By.xpath(legalEntityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));
		WebElement legalEntityStatus = legalEntityName.findElement(By.xpath(legalEntityStatusLocator.replace("?", status)));
		return legalEntityName.isDisplayed() && legalEntityStatus.isDisplayed();
	}

	public boolean verifyLegalEnityExists() {
		return driver.findElements(By.xpath(legalEntityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME)))).isEmpty();
	}

	public boolean verifyTradingName() {
		tradingNameText = driver.findElement(By.xpath(tradename.replace("?", DataStore.getSavedValue(UsableValues.TRADING_NAME))));
		return tradingNameText.isDisplayed();
	}

	public boolean verifyContactAtTheOrganisation() {
		WebElement fullname = driver.findElement(By.xpath(contactFullName.replace("?", getFullname())));
		WebElement workPhone = driver.findElement(By.xpath(contactWorkNumber.replace("?", DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER))));
		WebElement mobilePhone = driver.findElement(By.xpath(contactMobileNumber.replace("?", DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER))));
		WebElement email = driver.findElement(By.xpath(contactEmailAddress.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL))));
		return fullname.isDisplayed() && workPhone.isDisplayed() && mobilePhone.isDisplayed() && email.isDisplayed();
	}

	public boolean checkContactDetails() {
		String fullContactName = DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + getContactsName();

		WebElement fullName = driver.findElement(By.xpath(contactFullName.replace("?", fullContactName)));
		WebElement workphoneNumber = driver.findElement(By.xpath(contactWorkNumber.replace("?", DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER))));
		WebElement mobilephoneNumber = driver.findElement(By.xpath(contactMobileNumber.replace("?", DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER))));
		WebElement emailAddress = driver.findElement(By.xpath(contactEmailAddress.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL).toLowerCase())));
		WebElement contactNotes = driver.findElement(By.xpath(contactCommunicationNotes.replace("?", DataStore.getSavedValue(UsableValues.CONTACT_NOTES))));

		WebElement removeLink = driver.findElement(By.linkText("remove " + getContactsName().toLowerCase() + " from this partnership"));

		return fullName.isDisplayed() && workphoneNumber.isDisplayed() && mobilephoneNumber.isDisplayed() && emailAddress.isDisplayed() && contactNotes.isDisplayed() && removeLink.isDisplayed()
				&& chooseRandomPreferredCommunicationMethod();
	}

	public boolean checkContactExists() {
		String fullContactName = DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + getContactsName();
		return driver.findElements(By.xpath(contactFullName.replace("?", fullContactName))).isEmpty();
	}

	public boolean checkPreviouslyKnownAsText() {

		String previouslyKownAsLocator = "//div/p[contains(text(), 'Previously known as: Partnership between ? and £')]";

		String result = previouslyKownAsLocator.replace("?", DataStore.getSavedValue(UsableValues.PREVIOUS_AUTHORITY_NAME)).replace("£", DataStore.getSavedValue(UsableValues.BUSINESS_NAME));

		return driver.findElement(By.xpath(result)).isDisplayed();
	}

	public void clickDoneButton() {
        waitForElementToBeVisible(By.linkText("done"), 2000);
        doneBtn.click();
        waitForPageLoad();
	}

	public void clickSaveButton() {
        waitForElementToBeVisible(By.id("edit-done"), 2000);
        saveBtn.click();
        waitForPageLoad();
	}

	private String getContactsName() {
		return DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);
	}

	private boolean chooseRandomPreferredCommunicationMethod() {
		WebElement preferredMethod = null;

		if(DataStore.getSavedValue(UsableValues.PREFERRED_CONTACT_METHOD) == "Email") {
			WebElement emailLink = driver.findElement(By.xpath(contactEmailAddress.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL).toLowerCase())));
			preferredMethod = emailLink.findElement(By.xpath("./.."));
		}
		else if(DataStore.getSavedValue(UsableValues.PREFERRED_CONTACT_METHOD) == "Workphone") {
			preferredMethod = driver.findElement(By.xpath(contactWorkNumber.replace("?", DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER))));
		}
		else if(DataStore.getSavedValue(UsableValues.PREFERRED_CONTACT_METHOD) == "Mobilephone") {
			preferredMethod = driver.findElement(By.xpath(contactMobileNumber.replace("?", DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER))));
		}

		return preferredMethod.getText().contains(" (preferred)");
	}

	private String getFullname() {
		return DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);
	}
}
