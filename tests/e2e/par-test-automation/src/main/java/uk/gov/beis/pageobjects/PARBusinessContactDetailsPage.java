package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARBusinessContactDetailsPage extends BasePageObject {
	public PARBusinessContactDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[@name='first_name']")
	WebElement firstName;

	@FindBy(xpath = "//input[@name='last_name']")
	WebElement lastName;

	@FindBy(xpath = "//input[@name='work_phone']")
	WebElement phone;

	@FindBy(xpath = "//input[@name='email']")
	WebElement email;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PARBusinessAddressDetailsPage enterContactDetails(String firstname,String lastname, String phone1, String email1) {
		if (firstName.isDisplayed())
			firstName.clear();
		firstName.sendKeys(firstname);
		lastName.clear();
		lastName.sendKeys(lastname);
		phone.clear();
		phone.sendKeys(phone1);
		email.clear();
		email.sendKeys(email1);

		if (continueBtn.isDisplayed())
			continueBtn.click();

		return PageFactory.initElements(driver, PARBusinessAddressDetailsPage.class);
	}
}
