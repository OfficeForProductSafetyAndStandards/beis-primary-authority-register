package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.UserManagement.ContactDetailsPage;

public class AuthorityAddressDetailsPage extends BasePageObject {

	@FindBy(id = "edit-address-line1")
	private WebElement addressLine1;
	
	@FindBy(id = "edit-address-line2")
	private WebElement addressLine2;

	@FindBy(id = "edit-town-city")
	private WebElement townOrCity;
	
	@FindBy(id = "edit-county")
	private WebElement county;

	@FindBy(id = "edit-postcode")
	private WebElement postcode;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public AuthorityAddressDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterMemberAddressDetails(String address1, String address2, String townCity, String countyName, String postalcode) {
		addressLine1.clear();
		addressLine2.clear();
		townOrCity.clear();
		county.clear();
		postcode.clear();
		
		addressLine1.sendKeys(address1);
		addressLine2.sendKeys(address2);
		townOrCity.sendKeys(townCity);
		county.sendKeys(countyName);
		postcode.sendKeys(postalcode);
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clearMemberAddressTextFields() {
		addressLine1.clear();
		addressLine2.clear();
		townOrCity.clear();
		county.clear();
		postcode.clear();
	}
	
	public ONSCodePage enterAddressDetails(String address1, String address2, String townCity, String countyName, String postalcode) {
		addressLine1.clear();
		addressLine2.clear();
		townOrCity.clear();
		county.clear();
		postcode.clear();
		
		addressLine1.sendKeys(address1);
		addressLine2.sendKeys(address2);
		townOrCity.sendKeys(townCity);
		county.sendKeys(countyName);
		postcode.sendKeys(postalcode);
		
		continueBtn.click();

		return PageFactory.initElements(driver, ONSCodePage.class);
	}
	
	public ContactDetailsPage enterMemberOrganisationAddressDetails(String address1, String address2, String townCity, String countyName, String postalcode) {
		addressLine1.clear();
		addressLine2.clear();
		townOrCity.clear();
		county.clear();
		postcode.clear();
		
		addressLine1.sendKeys(address1);
		addressLine2.sendKeys(address2);
		townOrCity.sendKeys(townCity);
		county.sendKeys(countyName);
		postcode.sendKeys(postalcode);
		
		continueBtn.click();

		return PageFactory.initElements(driver, ContactDetailsPage.class);
	}
}
