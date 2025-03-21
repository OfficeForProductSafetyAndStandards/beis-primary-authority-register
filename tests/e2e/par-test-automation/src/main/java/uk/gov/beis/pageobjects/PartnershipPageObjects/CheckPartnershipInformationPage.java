package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class CheckPartnershipInformationPage extends BasePageObject {
	
	@FindBy(id = "edit-terms-authority-agreed")
	private WebElement authorityConfirmCheckbox;
	
	@FindBy(id = "edit-terms-organisation-agreed")
	private WebElement organisationConfirmCheckbox;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String partnershipDetailsLocator = "//div/p[contains(text(),'?')]";
	
	private String businessnameLocator = "//div[contains(text(),'?')]";
	private String businessAddressLocator = "//div/p[contains(text(),'?')]";
	
	private String businessContactFullnameLocator = "//div[contains(text(),'?')]";
	private String businessWorkNumberLocator = "//div[contains(text(),'?')]";
	private String businessMobileNumberLocator = "//div[contains(text(),'?')]";
	private String businessEmailLocator = "//div/a[contains(text(),'?')]";
	
	private String authorityNameLocator = "//div[contains(text(),'?')]";
	
	private String aboutTheOrganisationLocator = "//div/p[contains(text(),'?')]";
	
	private String sicCodeLocator = "//div[contains(text(),'?')]";
	
	private String noEmplyeesLocator = "//div[contains(text(),'?')]";
	private String membersizeLocator = "//div[contains(text(),'?')]";
	
	private String entityNameLocator = "//div[contains(text(),'?')]";
	//private String entityTypeLocator = "//div[contains(text(),'?')]"; // Defect: Legal Entity Types, excepted 'Other' are not displayed, currently this breaks the test.
	private String tradeNameLocator = "//div[contains(text(),'?')]";
	
	public CheckPartnershipInformationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean verifyAboutThePartnership() {
		WebElement aboutPartnership = driver.findElement(By.xpath(partnershipDetailsLocator.replace("?", DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO))));
		return aboutPartnership.isDisplayed();
	}
	
	public boolean verifyOrganisationName() {
		WebElement name = driver.findElement(By.xpath(businessnameLocator.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME))));
		return name.isDisplayed();
	}
	
	public boolean verifyOrganisationAddress() {
		WebElement address = driver.findElement(By.xpath(businessAddressLocator.replace("?", getFullAddress())));
		return address.isDisplayed();
	}
	
	public boolean verifyContactAtTheOrganisation() {
		WebElement fullname = driver.findElement(By.xpath(businessContactFullnameLocator.replace("?", getFullname())));
		WebElement workPhone = driver.findElement(By.xpath(businessWorkNumberLocator.replace("?", DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER))));
		WebElement mobilePhone = driver.findElement(By.xpath(businessMobileNumberLocator.replace("?", DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER))));
		WebElement email = driver.findElement(By.xpath(businessEmailLocator.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL))));
		return fullname.isDisplayed() && workPhone.isDisplayed() && mobilePhone.isDisplayed() && email.isDisplayed();
	}
	
	public boolean verifyPrimaryAuthorityName() {
		WebElement authority = driver.findElement(By.xpath(authorityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_NAME))));
		return authority.isDisplayed();
	}
	
	public boolean verifyAboutTheOrganisation() {
		WebElement aboutOrganisation = driver.findElement(By.xpath(aboutTheOrganisationLocator.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_DESC))));
		return aboutOrganisation.isDisplayed();
	}
	
	public boolean verifyPrimarySICCode() {
		WebElement sicCode = driver.findElement(By.xpath(sicCodeLocator.replace("?", DataStore.getSavedValue(UsableValues.SIC_CODE))));
		return sicCode.isDisplayed();
	}
	
	public boolean verifyNumberOfEmployees() {
		WebElement employeeNumber = driver.findElement(By.xpath(noEmplyeesLocator.replace("?", DataStore.getSavedValue(UsableValues.NO_EMPLOYEES))));
		return employeeNumber.isDisplayed();
	}

	public boolean verifyMemberSize() {
		WebElement memberSize = driver.findElement(By.xpath(membersizeLocator.replace("?", DataStore.getSavedValue(UsableValues.MEMBERLIST_SIZE)).toLowerCase()));
		return memberSize.isDisplayed();
	}
	
	public boolean verifyLegalEntity() {
		WebElement entityName = driver.findElement(By.xpath(entityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));
		//WebElement entityType = driver.findElement(By.xpath(entityTypeLocator.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_TYPE))));
		return entityName.isDisplayed(); // && entityType.isDisplayed();
	}

	public boolean verifyTradingName() {
		WebElement tradeName = driver.findElement(By.xpath(tradeNameLocator.replace("?", DataStore.getSavedValue(UsableValues.TRADING_NAME))));
		return tradeName.isDisplayed();
	}
	
	// Buttons
	public void acceptTermsAndConditions() {
		if(!authorityConfirmCheckbox.isSelected()) {
			authorityConfirmCheckbox.click();
		}
	}
	
	public void deselectConfirmationCheckbox() {
		if(authorityConfirmCheckbox.isSelected()) {
			authorityConfirmCheckbox.click();
		}
	}
	
	public void deselectOrganisationConfirmationCheckbox() {
		if(organisationConfirmCheckbox.isSelected()) {
			organisationConfirmCheckbox.click();
		}
	}
	
	public void confirmApplication() {
		if(!organisationConfirmCheckbox.isSelected()) {
			organisationConfirmCheckbox.click();
		}
	}
	
	public void completeApplication() {
		if(!authorityConfirmCheckbox.isSelected()) {
			authorityConfirmCheckbox.click();
		}
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
	
	private String getFullAddress() {
		return DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1) + ", " + DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE2) + ", " + DataStore.getSavedValue(UsableValues.BUSINESS_TOWN)
		+ ", " + DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE) + ", " + DataStore.getSavedValue(UsableValues.BUSINESS_COUNTRY);
	}
	
	private String getFullname() {
		return DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);
	}
}
