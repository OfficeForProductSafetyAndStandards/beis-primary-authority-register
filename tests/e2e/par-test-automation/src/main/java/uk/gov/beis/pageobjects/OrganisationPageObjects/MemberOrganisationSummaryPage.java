package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.AddAddressPage;
import uk.gov.beis.pageobjects.EnterTheDatePage;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanCoveragePage;
import uk.gov.beis.pageobjects.LegalEntityPageObjects.LegalEntityTypePage;
import uk.gov.beis.pageobjects.UserManagement.PersonContactDetailsPage;
import uk.gov.beis.utility.DataStore;

public class MemberOrganisationSummaryPage extends BasePageObject {
	
	@FindBy(linkText = "edit organisation name")
	private WebElement editOrganisationNameLink;
	
	@FindBy(linkText = "edit address")
	private WebElement editAddressLink;
	
	@FindBy(linkText = "edit membership start date")
	private WebElement editMembershipStartDateLink;
	
	@FindBy(linkText = "edit person")
	private WebElement editPersonLink;
	
	@FindBy(linkText = "add another legal entity")
	private WebElement addAnotherLegalEntityLink;
	
	@FindBy(linkText = "edit trading name")
	private WebElement editTradingNameLink;
	
	@FindBy(linkText = "edit covered by inspection plan")
	private WebElement editCoveredByInspectionPlanLink;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	@FindBy(id = "edit-done")
	private WebElement doneBtn;
	
	String organisationNameLocator = "//fieldset[@id='edit-organisation-name']/div/fieldset/div[contains(text(), '?')]";
	String organisationAddressLocator = "//fieldset[@id='edit-member-registered-address']/div/fieldset/div/p[contains(text(), '?')]";
	String dateOfMembershipLocator = "//fieldset[@id='edit-membership-date']/div/fieldset/div/time[contains(text(), '?')]";
	String primaryContactNameLocator = "//fieldset[@id='edit-member-primary-contact']/div/fieldset/div[contains(text(), '?')]";
	String primaryContactWorkLocator = "//fieldset[@id='edit-member-primary-contact']/div/fieldset/div[contains(text(), '?')]";
	String primaryContactMobileLocator = "//fieldset[@id='edit-member-primary-contact']/div/fieldset/div[contains(text(), '?')]";
	String primaryContactEmailLocator = "//fieldset[@id='edit-member-primary-contact']/div/fieldset/div/a[contains(text(), '?')]";
	String legalEntitiesLocator = "//fieldset[@id='edit-legal-entities']/div/fieldset/div[contains(text(), '?')]";
	String tradingNameLocator = "//fieldset[@id='edit-trading-names']/div/fieldset/div[contains(text(), '?')]";
	String coveredByInspectionPlanLocator = "//fieldset[@id='edit-covered-by-inspection']/div/fieldset/div[contains(text(), '?')]";
	
	public MemberOrganisationSummaryPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public AddOrganisationNamePage selectEditOrganisationName() {
		editOrganisationNameLink.click();
		return PageFactory.initElements(driver, AddOrganisationNamePage.class);
	}
	
	public AddAddressPage selectEditAddress() {
		editAddressLink.click();
		return PageFactory.initElements(driver, AddAddressPage.class);
	}
	
	public EnterTheDatePage selectEditMembershipStartDate() {
		editMembershipStartDateLink.click();
		return PageFactory.initElements(driver, EnterTheDatePage.class);
	}
	
	public PersonContactDetailsPage selectEditPerson() {
		editPersonLink.click();
		return PageFactory.initElements(driver, PersonContactDetailsPage.class);
	}
	
	public LegalEntityTypePage selectAddAnotherLegalEntity() {
		addAnotherLegalEntityLink.click();
		return PageFactory.initElements(driver, LegalEntityTypePage.class);
	}
	
	public TradingPage selectEditTradingName() {
		editTradingNameLink.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
	
	public InspectionPlanCoveragePage selectEditCoveredByInspectionPlan() {
		editCoveredByInspectionPlanLink.click();
		return PageFactory.initElements(driver, InspectionPlanCoveragePage.class);
	}
	
	public Boolean checkMemberDetails() {
		WebElement organisationName = driver.findElement(By.xpath(organisationNameLocator.replace("?", DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME))));
		WebElement organisationAddress = driver.findElement(By.xpath(organisationAddressLocator.replace("?", getOrganisationAddress())));
		WebElement membershipStartDate = driver.findElement(By.xpath(dateOfMembershipLocator.replace("?", DataStore.getSavedValue(UsableValues.MEMBERSHIP_START_DATE))));
		WebElement contactsName = driver.findElement(By.xpath(primaryContactNameLocator.replace("?", getContactsName())));
		WebElement contactsWorkNumber = driver.findElement(By.xpath(primaryContactWorkLocator.replace("?", DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER))));
		WebElement contactsMobileNumber = driver.findElement(By.xpath(primaryContactMobileLocator.replace("?", DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER))));
		WebElement contactsEmailAddress = driver.findElement(By.xpath(primaryContactEmailLocator.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL).toLowerCase())));
		WebElement legalEntity = driver.findElement(By.xpath(legalEntitiesLocator.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));
		WebElement tradingName = driver.findElement(By.xpath(tradingNameLocator.replace("?", DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME))));
		WebElement coveredByInspection = driver.findElement(By.xpath(coveredByInspectionPlanLocator.replace("?", DataStore.getSavedValue(UsableValues.COVERED_BY_INSPECTION_PLAN))));
		
		return organisationName.isDisplayed() && organisationAddress.isDisplayed() && membershipStartDate.isDisplayed() && contactsName.isDisplayed() && contactsWorkNumber.isDisplayed() 
				&& contactsMobileNumber.isDisplayed() && contactsEmailAddress.isDisplayed() && legalEntity.isDisplayed() && tradingName.isDisplayed() && coveredByInspection.isDisplayed();
	}
	
	public MemberOrganisationAddedConfirmationPage selectSave() {
		saveBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationAddedConfirmationPage.class);
	}
	
	public MemberListPage selectDone() {
		doneBtn.click();
		return PageFactory.initElements(driver, MemberListPage.class);
	}
	
	private String getContactsName() {
		return DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);
	}
	
	private String getOrganisationAddress() {
		return DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1) + ", " + DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2) + ", " + DataStore.getSavedValue(UsableValues.BUSINESS_TOWN) 
			+ ", " + DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE) + ", " + DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY);
	}
}
