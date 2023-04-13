package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AddPersonContactDetailsPage extends BasePageObject {
	public AddPersonContactDetailsPage() throws ClassNotFoundException, IOException {
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
		titleField.clear();
		titleField.sendKeys(title);
	}
	
	public void enterFirstname(String firstname) {
		firstnameField.clear();
		firstnameField.sendKeys(firstname);
	}
	
	public void enterLastname(String lastname) {
		lastnameField.clear();
		lastnameField.sendKeys(lastname);
	}
	
	public void enterWorkPhoneNumber(String phoneNumber) {
		workPhoneField.clear();
		workPhoneField.sendKeys(phoneNumber);
	}
	
	public void enterMobilePhoneNumber(String phoneNumber) {
		mobilePhoneField.clear();
		mobilePhoneField.sendKeys(phoneNumber);
	}
	
	public void enterEmailAddress(String email) {
		emailAddressField.clear();
		emailAddressField.sendKeys(email);
	}
	
	public GivePersonAccountPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, GivePersonAccountPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
