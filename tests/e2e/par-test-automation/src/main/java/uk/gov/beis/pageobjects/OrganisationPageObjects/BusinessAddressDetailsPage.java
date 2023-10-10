package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class BusinessAddressDetailsPage extends BasePageObject {

	public BusinessAddressDetailsPage() throws ClassNotFoundException, IOException {
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

	public BusinessContactDetailsPage enterAddressDetails(String address1, String town1, String postcode1) {
		driver.findElement(By.xpath("//input[@name='address_line1']")).clear();
		driver.findElement(By.xpath("//input[@name='address_line1']")).sendKeys(address1);
		town.clear();
		town.sendKeys(town1);
		postcode.clear();
		postcode.sendKeys(postcode1);
		continueBtn.click();

		return PageFactory.initElements(driver, BusinessContactDetailsPage.class);
	}

	public BusinessContactDetailsPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, BusinessContactDetailsPage.class);
	}
}
