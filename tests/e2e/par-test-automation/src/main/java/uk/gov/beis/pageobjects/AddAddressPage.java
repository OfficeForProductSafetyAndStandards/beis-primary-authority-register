package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.Select;

import uk.gov.beis.pageobjects.AuthorityPageObjects.ONSCodePage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MemberOrganisationSummaryPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;
import uk.gov.beis.pageobjects.UserManagement.ContactDetailsPage;

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
	
	public ContactDetailsPage goToAddContactDetailsPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, ContactDetailsPage.class);
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public PartnershipConfirmationPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public PartnershipConfirmationPage clickSaveButton() {
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public MemberOrganisationSummaryPage goToMemberOrganisationSummaryPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
	
	public ONSCodePage goToONSCodePage() {
		continueBtn.click();
		return PageFactory.initElements(driver, ONSCodePage.class);
	}
}
