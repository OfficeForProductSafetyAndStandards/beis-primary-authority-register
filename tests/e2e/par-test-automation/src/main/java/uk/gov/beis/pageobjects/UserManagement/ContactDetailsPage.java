package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Map;
import java.util.Random;

import org.apache.commons.lang3.RandomStringUtils;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import cucumber.api.DataTable;
import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.AccountInvitePage;
import uk.gov.beis.pageobjects.EnterTheDatePage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MemberOrganisationSummaryPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.SICCodePage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;
import uk.gov.beis.utility.DataStore;

public class ContactDetailsPage extends BasePageObject {
	
	@FindBy(id = "edit-salutation")
	private WebElement titleField;
	
	@FindBy(id = "edit-first-name")
	private WebElement firstnameField;
	
	@FindBy(id = "edit-last-name")
	private WebElement lastnameField;
	
	@FindBy(id = "edit-work-phone")
	private WebElement workPhoneField;
	
	@FindBy(id = "edit-mobile-phone")
	private WebElement mobilePhoneField;
	
	@FindBy(id = "edit-email")
	private WebElement emailAddressField;
	
	@FindBy(id = "edit-preferred-contact-communication-email")
	private WebElement preferredEmailCheckbox;
	
	@FindBy(id = "edit-preferred-contact-communication-phone")
	private WebElement preferredWorkphoneCheckbox;
	
	@FindBy(id = "edit-preferred-contact-communication-mobile")
	private WebElement preferredMobilephoneCheckbox;
	
	@FindBy(id = "edit-notes")
	private WebElement contactNotesTextfield;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public ContactDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterEmailAddress(String email) {
		emailAddressField.clear();
		emailAddressField.sendKeys(email);
		
		DataStore.saveValue(UsableValues.BUSINESS_EMAIL, email);
	}
	
	public void addContactDetails(DataTable details) {
		clearAllFields();
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.PERSON_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.BUSINESS_FIRSTNAME, data.get("Firstname")); 
			DataStore.saveValue(UsableValues.BUSINESS_LASTNAME, data.get("Lastname"));
			DataStore.saveValue(UsableValues.PERSON_WORK_NUMBER, data.get("WorkNumber"));
			DataStore.saveValue(UsableValues.PERSON_MOBILE_NUMBER, data.get("MobileNumber"));
			DataStore.saveValue(UsableValues.BUSINESS_EMAIL, data.get("Email"));
		}
		
		titleField.sendKeys(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		firstnameField.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME));
		lastnameField.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME));
		workPhoneField.sendKeys(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		mobilePhoneField.sendKeys(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		emailAddressField.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
	}
	
	public void enterContactWithRandomName(DataTable person) {
		clearAllFields();
		
		String firstName = RandomStringUtils.randomAlphabetic(8);
		String lastName = RandomStringUtils.randomAlphabetic(8);
		String emailAddress = firstName + "@" + lastName + ".com";

		DataStore.saveValue(UsableValues.BUSINESS_FIRSTNAME, firstName); 
		DataStore.saveValue(UsableValues.BUSINESS_LASTNAME, lastName);
		DataStore.saveValue(UsableValues.BUSINESS_EMAIL, emailAddress);
		
		for (Map<String, String> data : person.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.PERSON_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.PERSON_WORK_NUMBER, data.get("WorkNumber"));
			DataStore.saveValue(UsableValues.PERSON_MOBILE_NUMBER, data.get("MobileNumber"));
		}
		
		titleField.sendKeys(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		firstnameField.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME));
		lastnameField.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME));
		workPhoneField.sendKeys(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		mobilePhoneField.sendKeys(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		emailAddressField.sendKeys(emailAddress);
	}
	
	public void addContactDetailsWithRandomName(DataTable person) {
		clearAllFields();
		clearPreferredCommunicationMethods();
		
		String firstName = RandomStringUtils.randomAlphabetic(8);
		String lastName = RandomStringUtils.randomAlphabetic(8);
		String emailAddress = firstName + "@" + lastName + ".com";

		DataStore.saveValue(UsableValues.BUSINESS_FIRSTNAME, firstName); 
		DataStore.saveValue(UsableValues.BUSINESS_LASTNAME, lastName);
		DataStore.saveValue(UsableValues.BUSINESS_EMAIL, emailAddress);
		
		setContactDetails(person);
		
		emailAddressField.sendKeys(emailAddress);
	}
	
	public void editContactDetailsWithRandomName(DataTable person) {
		clearEditJourneyFields();
		clearPreferredCommunicationMethods();
		
		String firstName = RandomStringUtils.randomAlphabetic(8);
		String lastName = RandomStringUtils.randomAlphabetic(8);

		DataStore.saveValue(UsableValues.BUSINESS_FIRSTNAME, firstName); 
		DataStore.saveValue(UsableValues.BUSINESS_LASTNAME, lastName);
		
		setContactDetails(person);
	}
	
	public void selectPreferredEmail() {
		if(!preferredEmailCheckbox.isSelected()) {
			preferredEmailCheckbox.click();
			
			DataStore.saveValue(UsableValues.PREFERRED_CONTACT_METHOD, "Email");
		}
	}
	
	public void selectPreferredWorkphone() {
		if(!preferredWorkphoneCheckbox.isSelected()) {
			preferredWorkphoneCheckbox.click();
			
			DataStore.saveValue(UsableValues.PREFERRED_CONTACT_METHOD, "Workphone");
		}
	}
	
	public void selectPreferredMobilephone() {
		if(!preferredMobilephoneCheckbox.isSelected()) {
			preferredMobilephoneCheckbox.click();
			
			DataStore.saveValue(UsableValues.PREFERRED_CONTACT_METHOD, "Mobilephone");
		}
	}
	
	public void enterContactNote(String note) {
		contactNotesTextfield.clear();
		contactNotesTextfield.sendKeys(note);
	}
	
	public AccountInvitePage goToInviteUserAccountPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, AccountInvitePage.class);
	}
	
	public SICCodePage goToSICCodePage() {
		continueBtn.click();
		return PageFactory.initElements(driver, SICCodePage.class);
	}
	
	public EnterTheDatePage clickContinueButtonForMemberContact() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnterTheDatePage.class);
	}
	
	public GiveUserAccountPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, GiveUserAccountPage.class);
	}
	
	public UserRoleTypePage selectContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, UserRoleTypePage.class);
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public MemberOrganisationSummaryPage goToMemberOrganisationSummaryPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
	
	public ContactCommunicationPreferencesPage goToContactCommunicationPreferencesPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, ContactCommunicationPreferencesPage.class);
	}
	
	private void setContactDetails(DataTable person) {
		for (Map<String, String> data : person.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.PERSON_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.PERSON_WORK_NUMBER, data.get("WorkNumber"));
			DataStore.saveValue(UsableValues.PERSON_MOBILE_NUMBER, data.get("MobileNumber"));
			DataStore.saveValue(UsableValues.CONTACT_NOTES, data.get("ContactNotes"));
		}
		
		titleField.sendKeys(DataStore.getSavedValue(UsableValues.PERSON_TITLE));
		firstnameField.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME));
		lastnameField.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME));
		workPhoneField.sendKeys(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
		mobilePhoneField.sendKeys(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER));
		
		selectRandomPreferredCommunication();
		
		contactNotesTextfield.sendKeys(DataStore.getSavedValue(UsableValues.CONTACT_NOTES));
	}
	
	private void selectRandomPreferredCommunication() {
		ArrayList<WebElement> communicationMethods = new ArrayList<WebElement>();
		communicationMethods.add(preferredEmailCheckbox);
		communicationMethods.add(preferredWorkphoneCheckbox);
		communicationMethods.add(preferredMobilephoneCheckbox);
		
		Random rand = new Random();
		
		WebElement chosenMethod = communicationMethods.get(rand.nextInt(communicationMethods.size()));
		chosenMethod.click();
		
		if(chosenMethod == preferredEmailCheckbox) {
			DataStore.saveValue(UsableValues.PREFERRED_CONTACT_METHOD, "Email");
		}
		else if(chosenMethod == preferredWorkphoneCheckbox) {
			DataStore.saveValue(UsableValues.PREFERRED_CONTACT_METHOD, "Workphone");
		}
		else if(chosenMethod == preferredMobilephoneCheckbox) {
			DataStore.saveValue(UsableValues.PREFERRED_CONTACT_METHOD, "Mobilephone");
		}
	}
	
	private void clearAllFields() {
		clearEditJourneyFields();
		
		emailAddressField.clear();
	}
	
	private void clearEditJourneyFields() {
		titleField.clear();
		firstnameField.clear();
		lastnameField.clear();
		workPhoneField.clear();
		mobilePhoneField.clear();
	}
	
	private void clearPreferredCommunicationMethods() {
		if(preferredEmailCheckbox.isSelected()) {
			preferredEmailCheckbox.click();
		}
		
		if(preferredWorkphoneCheckbox.isSelected()) {
			preferredWorkphoneCheckbox.click();
		}
		
		if(preferredMobilephoneCheckbox.isSelected()) {
			preferredMobilephoneCheckbox.click();
		}
		
		contactNotesTextfield.clear();
	}
}