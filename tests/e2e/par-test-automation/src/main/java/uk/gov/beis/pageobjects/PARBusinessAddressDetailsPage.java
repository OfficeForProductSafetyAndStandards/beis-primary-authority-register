package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARBusinessAddressDetailsPage extends BasePageObject {

	public PARBusinessAddressDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[@name='address_line1']")
	WebElement addressLine1;

	@FindBy(xpath = "//input[@name='town_city']")
	WebElement town;

	@FindBy(xpath = "//input[@name='postcode']")
	WebElement postcode;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PARBusinessContactDetailsPage enterAddressDetails(String address1, String town1, String postcode1) {
		if (addressLine1.isDisplayed())
			addressLine1.clear();
		addressLine1.sendKeys(address1);
		town.clear();
		town.sendKeys(town1);
		postcode.clear();
		postcode.sendKeys(postcode1);

		if (continueBtn.isDisplayed())
			continueBtn.click();

		return PageFactory.initElements(driver, PARBusinessContactDetailsPage.class);
	}
}
