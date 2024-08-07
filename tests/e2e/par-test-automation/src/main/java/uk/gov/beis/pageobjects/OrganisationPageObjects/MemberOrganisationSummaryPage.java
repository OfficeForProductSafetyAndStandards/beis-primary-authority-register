package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
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
	
	String organisationNameLocator = "//div[@id='edit-organisation-name']/div/div[contains(normalize-space(), '?')]";
	String organisationAddressLocator = "//div[@id='edit-member-registered-address']/div/div/p[contains(normalize-space(), '?')]";
	String dateOfMembershipLocator = "//div[@id='edit-membership-date']/div/div/time[contains(normalize-space(), '?')]";
	String primaryContactNameLocator = "//div[@id='edit-member-primary-contact']/div/div[contains(normalize-space(), '?')]";
	String primaryContactWorkLocator = "//div[@id='edit-member-primary-contact']/div/div[contains(normalize-space(), '?')]";
	String primaryContactMobileLocator = "//div[@id='edit-member-primary-contact']/div/div[contains(normalize-space(), '?')]";
	String primaryContactEmailLocator = "//div[@id='edit-member-primary-contact']/div/div/a[contains(normalize-space(), '?')]";
	String legalEntitiesLocator = "//div[@id='edit-legal-entities']/div/div[contains(normalize-space(), '?')]";
	String tradingNameLocator = "//div[@id='edit-trading-names']/div/div[contains(normalize-space(), '?')]";
	String coveredByInspectionPlanLocator = "//div[@id='edit-covered-by-inspection']/div/div[contains(normalize-space(), '?')]";
	
	public MemberOrganisationSummaryPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectEditOrganisationName() {
		editOrganisationNameLink.click();
	}
	
	public void selectEditAddress() {
		editAddressLink.click();
	}
	
	public void selectEditMembershipStartDate() {
		editMembershipStartDateLink.click();
	}
	
	public void selectEditPerson() {
		editPersonLink.click();
	}
	
	public void selectAddAnotherLegalEntity() {
		addAnotherLegalEntityLink.click();
	}
	
	public void selectEditTradingName() {
		editTradingNameLink.click();
	}
	
	public void selectEditCoveredByInspectionPlan() {
		editCoveredByInspectionPlanLink.click();
	}
	
	public Boolean checkMemberOrganisationSummaryPage() {
		WebElement organisationName = driver.findElement(By.xpath(organisationNameLocator.replace("?", DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME))));
		return organisationName.isDisplayed();
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
	
	public void clickSaveButton() {
		saveBtn.click();
	}
	
	public void clickDoneButton() {
		doneBtn.click();
	}
	
	private String getContactsName() {
		return DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);
	}
	
	private String getOrganisationAddress() {
		return DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1) + ", " + DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2) + ", " + DataStore.getSavedValue(UsableValues.BUSINESS_TOWN) 
			+ ", " + DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE) + ", " + DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY);
	}
}
