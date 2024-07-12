package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

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
	
	private String manageContactByEmailLocator = "//td[contains(normalize-space(), '?')]/following-sibling::td[2]/a[contains(normalize-space(), 'Manage contact')]";
	private String manageContactByNameLocator = "//td[contains(normalize-space(), '?')]/following-sibling::td[3]/a[contains(normalize-space(), 'Manage contact')]";
	
	public ManagePeoplePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectAddPerson() {
		addPersonBtn.click();
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
	
	public void findManageContactByEmail(String personEmail) {
		WebElement link = driver.findElement(By.xpath(manageContactByEmailLocator.replace("?", personEmail)));
		link.click();
	}
	
	public void findManageContactByName(String personName) {
		WebElement link = driver.findElement(By.xpath(manageContactByNameLocator.replace("?", personName)));
		link.click();
	}
	
	public void clickManageContact() {
		manageContactBtn.click();
	}
}
