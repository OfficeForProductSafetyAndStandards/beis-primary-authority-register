package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;
import java.util.Map;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import cucumber.api.DataTable;
import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class EnforcementOfficerContactDetailsPage extends BasePageObject {
	
	@FindBy(id = "edit-first-name")
	private WebElement firstnameTextfield;
	
	@FindBy(id = "edit-last-name")
	private WebElement lastnameTextfield;
	
	@FindBy(id = "edit-work-phone")
	private WebElement workPhoneTextfield;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public EnforcementOfficerContactDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void setContactDetails(DataTable details) {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.PERSON_FIRSTNAME, data.get("Firstname"));
			DataStore.saveValue(UsableValues.PERSON_LASTNAME, data.get("Lastname"));
			DataStore.saveValue(UsableValues.PERSON_WORK_NUMBER, data.get("Workphone"));
		}
	}
	
	public void enterFirstname(String firstname) {
		firstnameTextfield.clear();
		firstnameTextfield.sendKeys(firstname);
	}
	
	public void enterLastname(String lastname) {
		lastnameTextfield.clear();
		lastnameTextfield.sendKeys(lastname);
	}
	
	public void enterWorkPhoneNumber(String number) {
		workPhoneTextfield.clear();
		workPhoneTextfield.sendKeys(number);
	}
	
	public void clearAllFIelds() {
		firstnameTextfield.clear();
		lastnameTextfield.clear();
		workPhoneTextfield.clear();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
