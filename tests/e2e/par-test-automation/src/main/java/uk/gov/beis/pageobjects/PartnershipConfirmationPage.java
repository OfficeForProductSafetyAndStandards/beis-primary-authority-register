package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.UserManagement.PersonContactDetailsPage;
import uk.gov.beis.utility.DataStore;

public class PartnershipConfirmationPage extends BasePageObject {

	public PartnershipConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	//private boolean twopartjourney = false;

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
	
	@FindBy(linkText = "edit trading name")
	private WebElement editTradingNameLink;
	
	// Data Fields
	@FindBy(id = "edit-regulatory-functions")
	private WebElement regulatoryFunctionText;
	
	@FindBy(id = "edit-about")
	private WebElement aboutOrganisationText;
	
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

	public PartnershipSearchPage clickDone() {
		doneBtn.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}

	public PartnershipAdvancedSearchPage clickSave() {
		saveButton.click();
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}

	String partnershipDetails = "//div/p[contains(text(),'?')]";
	String partnershipRegFunc = "//ul/li[contains(text(),'?')]";
	String aboutTheOrganisation = "//div/p[contains(text(),'?')]";
	
	String businessname = "//div[contains(text(),'?')]";
	
	String businessAddress1 = "//div/p[contains(text(),'?')]";
	String businessAddress2 = "//div/p[contains(text(),'?')]";
	String businessTown1 = "//div/p[contains(text(),'?')]";
	String businessPCode = "//div/p[contains(text(),'?')]";
	String businessCountry = "//div/p[contains(text(),'?')]";
	
	String businessFName = "//div[contains(text(),'?')]";
	String businessLName = "//div[contains(text(),'?')]";
	
	String businessEmailid = "//a[contains(text(),'?')]";
	String authorityName = "//div[contains(text(),'?')]";
	
	String sic = "//div[contains(text(),'?')]";
	
	String noEmplyees = "//div[contains(text(),'?')]";
	String entName = "//div[contains(text(),'?')]";
	String entType = "//div[contains(text(),'?')]";
	String regNo = "//div[contains(text(),'?')]";
	
	String tradename = "//div[contains(text(),'?')]";
	String membersize = "//div[contains(text(),'?')]";
	
	String contactFullName = "//div[contains(text(),'?')]";
	String contactWorkNumber = "//div[contains(text(),'?')]";
	String contactMobileNumber = "//div[contains(text(),'?')]";
	String contactEmailAddress = "//a[contains(text(),'?')]";
	String contactCommunicationNotes = "//p[contains(text(),'?')]";
	String editContactLink = "//a[contains(text(),'?')]";
	String removeContactLink = "//a[contains(text(),'?')]";

	public EnforcementNotificationPage createEnforcement() {
		craeteEnforcementBtn.click();
		return PageFactory.initElements(driver, EnforcementNotificationPage.class);
	}

	public PartnershipConfirmationPage confirmDetailsAsAuthority() {
		WebElement checkbox = driver.findElement(By.id("edit-terms-authority-agreed"));
		
		if (!checkbox.isSelected())
			checkbox.click();
		
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public PartnershipConfirmationPage confirmDetails() {
		WebElement checkbox = ScenarioContext.secondJourneyPart ? driver.findElement(By.id("edit-terms-organisation-agreed")) : driver.findElement(By.id("edit-terms-authority-agreed"));
		
		if (!checkbox.isSelected())
			checkbox.click();
		
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}

	public PartnershipCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipCompletionPage.class);
	}

	public InspectionPlanSearchPage selectSeeAllInspectionPlans() {
		seeAllInspectionPlans.click();
		return PageFactory.initElements(driver, InspectionPlanSearchPage.class);
	}

	public AdviceNoticeSearchPage selectSeeAllAdviceNotices() {
		seeAllAdvice.click();
		return PageFactory.initElements(driver, AdviceNoticeSearchPage.class);
	}
  
	public EnquiryContactDetailsPage sendGeneralEnquiry() {
		generalEnquiryLink.click();
		return PageFactory.initElements(driver, EnquiryContactDetailsPage.class);
	}

	public InspectionContactDetailsPage selectSendInspectionFeedbk() {
		sendInspectionFeedbackBtn.click();
		return PageFactory.initElements(driver, InspectionContactDetailsPage.class);
	}

	public EnforcementContactDetailsPage selectDeviateInspectionPlan() {
		reqToDeviateFromInspectionPlan.click();
		return PageFactory.initElements(driver, EnforcementContactDetailsPage.class);
	}

	// Update Partnership Details
	public PartnershipDescriptionPage editAboutPartnership() {
		editPartnershipLink.click();
		return PageFactory.initElements(driver, PartnershipDescriptionPage.class);
	}
	
	public RegulatoryFunctionPage editRegulatoryFunctions() {
		editRegulatoryFunctionsLink.click();
		return PageFactory.initElements(driver, RegulatoryFunctionPage.class);
	}
	
	public EditRegisteredAddressPage editOrganisationAddress() {
		editOrganisationAddressLink.click();
		return PageFactory.initElements(driver, EditRegisteredAddressPage.class);
	}
	
	public PartnershipDescriptionPage editAboutTheOrganisation() {
		editAboutOrganisationLink.click();
		return PageFactory.initElements(driver, PartnershipDescriptionPage.class); //Test
	}
	
	public SICCodePage editSICCode() {
		editSICCodeLink.click();
		return PageFactory.initElements(driver, SICCodePage.class);
	}
	
	public TradingPage editTradingName() {
		editTradingNameLink.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
	
	public PersonContactDetailsPage addAnotherAuthorityContactButton() {
		addAuthorityContactLink.click();
		return PageFactory.initElements(driver, PersonContactDetailsPage.class);
	}
	
	public PersonContactDetailsPage addAnotherOrganisationContactButton() {
		addOrganisationContactLink.click();
		return PageFactory.initElements(driver, PersonContactDetailsPage.class);
	}
	
	public PersonContactDetailsPage editContactsDetailsButton() {
		WebElement editLink = driver.findElement(By.xpath(editContactLink.replace("?", "edit " + getContactsName().toLowerCase())));
		editLink.click();
		
		return PageFactory.initElements(driver, PersonContactDetailsPage.class);
	}
	
	public PersonContactDetailsPage removeContactsDetailsButton() {
		WebElement removeLink = driver.findElement(By.xpath(editContactLink.replace("?", "remove " + getContactsName().toLowerCase())));
		removeLink.click();
		
		return PageFactory.initElements(driver, PersonContactDetailsPage.class);
	}
	
	// Check Partnership Details
	public boolean checkPartnershipInfo() {
		WebElement partnershipDets = driver.findElement(By.xpath(partnershipDetails.replace("?", DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO))));
		return partnershipDets.isDisplayed();
	}

	public boolean checkRegulatoryFunctions() {
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
	
	public boolean checkTradingName() {
		tradingNameText = driver.findElement(By.xpath(tradename.replace("?", DataStore.getSavedValue(UsableValues.TRADING_NAME))));
		return tradingNameText.isDisplayed();
	}
	
	public boolean checkContactDetails() {
		String fullContactName = DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + getContactsName();
		
		WebElement fullName = driver.findElement(By.xpath(contactFullName.replace("?", fullContactName)));
		WebElement workphoneNumber = driver.findElement(By.xpath(contactWorkNumber.replace("?", DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER))));
		WebElement mobilephoneNumber = driver.findElement(By.xpath(contactMobileNumber.replace("?", DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER))));
		WebElement emailAddress = driver.findElement(By.xpath(contactEmailAddress.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL).toLowerCase())));
		WebElement contactNotes = driver.findElement(By.xpath(contactCommunicationNotes.replace("?", DataStore.getSavedValue(UsableValues.CONTACT_NOTES))));
		
		WebElement editLink = driver.findElement(By.linkText("edit " + getContactsName().toLowerCase()));
		WebElement removeLink = driver.findElement(By.linkText("remove " + getContactsName().toLowerCase() + " from this partnership"));
		
		return fullName.isDisplayed() && workphoneNumber.isDisplayed() && mobilephoneNumber.isDisplayed() && emailAddress.isDisplayed() && contactNotes.isDisplayed() && editLink.isDisplayed() && removeLink.isDisplayed()
				&& chooseRandomPreferredCommunicationMethod();
	}
	
	public boolean checkContactExists() {
		String fullContactName = DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + getContactsName();
		return driver.findElements(By.xpath(contactFullName.replace("?", fullContactName))).isEmpty();
	}
	
	// Coordinated Partnership Details
	public boolean checkNoEmployees() {
		WebElement nEmplyees = driver
				.findElement(By.xpath(noEmplyees.replace("?", DataStore.getSavedValue(UsableValues.NO_EMPLOYEES))));
		return nEmplyees.isDisplayed();
	}

	public boolean checkMemberSize() {
		WebElement memsize = driver.findElement(
				By.xpath(membersize.replace("?", DataStore.getSavedValue(UsableValues.MEMBERLIST_SIZE)).toLowerCase()));
		return memsize.isDisplayed();
	}

	public boolean checkPartnershipApplication() {
		WebElement businessNm = driver
				.findElement(By.xpath(businessname.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME))));
		WebElement addLine1 = driver.findElement(
				By.xpath(businessAddress1.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1))));
		WebElement businessTown = driver
				.findElement(By.xpath(businessTown1.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_TOWN))));
		WebElement businessPostcode = driver.findElement(
				By.xpath(businessPCode.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE))));
		WebElement businessFirstName = driver.findElement(
				By.xpath(businessFName.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME))));
		WebElement businessLastName = driver.findElement(
				By.xpath(businessLName.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME))));
		WebElement businessEmail = driver.findElement(
				By.xpath(businessEmailid.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL))));

		return (businessNm.isDisplayed() && addLine1.isDisplayed() && businessTown.isDisplayed()
				&& businessPostcode.isDisplayed() && businessFirstName.isDisplayed() && businessLastName.isDisplayed()
				&& businessEmail.isDisplayed());
	}

	public boolean checkEntityName() {
		WebElement entityNm = driver
				.findElement(By.xpath(entName.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));
		return (entityNm.isDisplayed());
	}

	public boolean checkRegNo() {
		WebElement regNo1 = driver
				.findElement(By.xpath(regNo.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NUMBER))));
		return (regNo1.isDisplayed());
	}

	public boolean checkPartnershipApplicationSecondPart() {
		WebElement sicCd = driver
				.findElement(By.xpath(sic.replace("?", DataStore.getSavedValue(UsableValues.SIC_CODE))));

		WebElement tradeNm = driver
				.findElement(By.xpath(tradename.replace("?", DataStore.getSavedValue(UsableValues.TRADING_NAME))));

		return (checkPartnershipApplication() && sicCd.isDisplayed() && tradeNm.isDisplayed());
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
}
