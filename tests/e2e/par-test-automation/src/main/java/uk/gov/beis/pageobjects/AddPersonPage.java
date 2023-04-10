package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AddPersonPage extends BasePageObject {
	public AddPersonPage() throws ClassNotFoundException, IOException {
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
	
	public void enterWorkPhoneNumber(String phoneNumber) {
		workPhoneField.sendKeys(phoneNumber);
	}
	
	public void enterMobilePhoneNumber(String phoneNumber) {
		mobilePhoneField.sendKeys(phoneNumber);
	}
	
	public void enterEmailAddress(String email) {
		emailAddressField.sendKeys(email);
	}
	
	public AuthorityGiveUserAccountPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityGiveUserAccountPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
