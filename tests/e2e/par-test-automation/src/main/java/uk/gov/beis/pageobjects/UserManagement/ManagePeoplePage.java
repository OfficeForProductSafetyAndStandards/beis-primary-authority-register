package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ManagePeoplePage extends BasePageObject {
	
	@FindBy(linkText = "Add a person")
	private WebElement addPersonBtn;
	
	@FindBy(id = "edit-name-email-search")
	private WebElement searchTextField;
	
	@FindBy(id = "edit-authority-organisation-filter")
	private WebElement authorityOrganisationTextField;
	
	@FindBy(id = "edit-submit-par-people")
	private WebElement submitBtn;
	
	@FindBy(xpath = "//td[@class='views-field views-field-last-name']")
	private WebElement personNameTableElement;
	
	@FindBy(linkText = "Manage contact")
	private WebElement manageContactBtn;
	
	public ManagePeoplePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public ContactDetailsPage selectAddPerson() {
		addPersonBtn.click();
		return PageFactory.initElements(driver, ContactDetailsPage.class);
	}
	
	public void enterNameOrEmail(String searchText) {
		searchTextField.sendKeys(searchText);
	}
	
	public void enterAuthorityOrOrganisation(String searchText) {
		authorityOrganisationTextField.sendKeys(searchText);
	}
	
	public void clickSubmit() {
		submitBtn.click();
	}
	
	public String GetPersonName() {
		return personNameTableElement.getText().trim();
	}
	
	public UserProfilePage clickManageContact() {
		manageContactBtn.click();
		return PageFactory.initElements(driver, UserProfilePage.class);
	}
}
