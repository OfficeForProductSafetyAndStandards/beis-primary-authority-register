package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.Select;

import uk.gov.beis.pageobjects.BasePageObject;

public class AddAddressPage extends BasePageObject {

	@FindBy(id = "edit-address-line1")
	private WebElement addressLine1TextBox;

	@FindBy(id = "edit-address-line2")
	private WebElement addressLine2TextBox;

	@FindBy(id = "edit-town-city")
	private WebElement townOrCityTextBox;

	@FindBy(id = "edit-county")
	private WebElement countyTextBox;

	@FindBy(id = "edit-country-code")
	private WebElement countrySelectBox;

	@FindBy(id = "edit-nation")
	private WebElement nationSelectBox;

	@FindBy(id = "edit-postcode")
	private WebElement postcodeTextBox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	public AddAddressPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterAddressDetails(String address1, String address2, String townCity, String county, String country, String nation, String postcode) {
		addressLine1TextBox.clear();
		addressLine1TextBox.sendKeys(address1);

		addressLine2TextBox.clear();
		addressLine2TextBox.sendKeys(address2);

		townOrCityTextBox.clear();
		townOrCityTextBox.sendKeys(townCity);

		countyTextBox.clear();
		countyTextBox.sendKeys(county);

		Select countrySelect = new Select(countrySelectBox);
		countrySelect.selectByVisibleText(country);

		if(nationSelectBox != null && nationSelectBox.isDisplayed()) {
			Select nationSelect = new Select(nationSelectBox);
			nationSelect.selectByVisibleText(nation);
		}

		postcodeTextBox.clear();
		postcodeTextBox.sendKeys(postcode);
	}

	public void editAddressDetails(String address1, String address2, String townCity, String county, String postcode) {
		addressLine1TextBox.clear();
		addressLine1TextBox.sendKeys(address1);

		addressLine2TextBox.clear();
		addressLine2TextBox.sendKeys(address2);

		townOrCityTextBox.clear();
		townOrCityTextBox.sendKeys(townCity);

		countyTextBox.clear();
		countyTextBox.sendKeys(county);

		postcodeTextBox.clear();
		postcodeTextBox.sendKeys(postcode);
	}

	public void clearAddressFields() {
		addressLine1TextBox.clear();
		addressLine2TextBox.clear();
		townOrCityTextBox.clear();
		countyTextBox.clear();
		postcodeTextBox.clear();
	}

	public void clickContinueButton() {
        waitForElementToBeClickable(By.id("edit-next"), 2000);
		continueBtn.click();
	}

	public void clickSaveButton() {
        waitForElementToBeClickable(By.id("edit-save"), 2000);
        saveBtn.click();
	}
}
