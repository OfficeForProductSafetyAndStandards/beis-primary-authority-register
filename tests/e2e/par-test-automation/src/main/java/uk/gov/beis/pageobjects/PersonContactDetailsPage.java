package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.Map;

import org.apache.commons.lang3.RandomStringUtils;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import cucumber.api.DataTable;
import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class PersonContactDetailsPage extends BasePageObject {
	public PersonContactDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

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
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public void enterContactDetails(DataTable person) {
		clearTextfields();
		
		String firstName = RandomStringUtils.randomAlphabetic(8);
		String lastName = RandomStringUtils.randomAlphabetic(8);
		String emailAddress = firstName + "@" + lastName + ".com";

		for (Map<String, String> data : person.asMaps(String.class, String.class)) {
			titleField.sendKeys(data.get("Title"));
			firstnameField.sendKeys(firstName);
			lastnameField.sendKeys(lastName);
			workPhoneField.sendKeys(data.get("WorkNumber"));
			mobilePhoneField.sendKeys(data.get("MobileNumber"));
			emailAddressField.sendKeys(emailAddress);
			
			DataStore.saveValue(UsableValues.PERSON_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.BUSINESS_FIRSTNAME, firstName); 
			DataStore.saveValue(UsableValues.BUSINESS_LASTNAME, lastName);
			DataStore.saveValue(UsableValues.PERSON_WORK_NUMBER, data.get("WorkNumber"));
			DataStore.saveValue(UsableValues.PERSON_MOBILE_NUMBER, data.get("MobileNumber"));
			DataStore.saveValue(UsableValues.BUSINESS_EMAIL, emailAddress);
		}
	}
	
	public PersonAccountPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, PersonAccountPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
	
	private void clearTextfields() {
		titleField.clear();
		firstnameField.clear();
		lastnameField.clear();
		workPhoneField.clear();
		mobilePhoneField.clear();
		emailAddressField.clear();
	}
}
