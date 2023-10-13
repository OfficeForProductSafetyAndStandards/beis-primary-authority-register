package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DashboardPage;

public class UpdateUserContactDetailsPage extends BasePageObject {
	public UpdateUserContactDetailsPage() throws ClassNotFoundException, IOException {
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
	
	public void enterTitle(String title) {
		titleField.sendKeys(title);
	}
	
	public void enterFirstname(String firstname) {
		firstnameField.sendKeys(firstname);
	}
	
	public void enterLastname(String lastname) {
		lastnameField.sendKeys(lastname);
	}
	
	public void enterWorkPhoneNumber(String number) {
		workPhoneField.sendKeys(number);
	}
	
	public void enterMobilePhoneNumber(String number) {
		mobilePhoneField.sendKeys(number);
	}
	
	public void enterEmailAddress(String email) {
		emailAddressField.sendKeys(email);
	}
	
	public UpdateUserCommunicationPreferencesPage selectContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, UpdateUserCommunicationPreferencesPage.class);
	}
	
	public DashboardPage selectCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
