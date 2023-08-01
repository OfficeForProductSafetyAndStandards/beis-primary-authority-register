package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.Select;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class EditRegisteredAddressPage extends BasePageObject {
	public EditRegisteredAddressPage() throws ClassNotFoundException, IOException {
		super();
	}

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

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

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
		countrySelect.selectByValue(country);
		
		WebElement countryValue = countrySelect.getFirstSelectedOption();
		DataStore.saveValue(UsableValues.BUSINESS_COUNTRY, countryValue.getText());
		
		if(nationSelectBox != null && nationSelectBox.isDisplayed()) {
			Select nationSelect = new Select(nationSelectBox);
			nationSelect.selectByValue(nation);
			
			WebElement nationValue = nationSelect.getFirstSelectedOption();
			DataStore.saveValue(UsableValues.BUSINESS_NATION, nationValue.getText());
		}
		
		postcodeTextBox.clear();
		postcodeTextBox.sendKeys(postcode);
	}
	
	public PartnershipConfirmationPage clickSaveButton() {
		saveBtn.click();
		
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
