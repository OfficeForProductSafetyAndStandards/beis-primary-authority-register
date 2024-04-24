package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class LegalEntityTypePage extends BasePageObject {
	
	@FindBy(id = "edit-par-component-legal-entity-0-registry-companies-house")
	private WebElement registeredOrganisationRadial;
	
	@FindBy(id = "edit-par-component-legal-entity-0-registry-charity-commission")
	private WebElement charityRadial;
	
	@FindBy(id = "edit-par-component-legal-entity-0-registry-internal")
	private WebElement unregisteredEntityRadial;
	
	@FindBy(id = "edit-par-component-legal-entity-0-registered-legal-entity-number")
	private WebElement registrationNumberTextbox;
	
	@FindBy(id = "edit-par-component-legal-entity-0-unregistered-legal-entity-name")
	private WebElement legalEntityNameTextbox;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String legalEntTypeRadio = "//label[contains(normalize-space(),'?')]/preceding-sibling::input";
	
	public LegalEntityTypePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectLegalEntityType(String entityType) {
		driver.findElement(By.xpath(legalEntTypeRadio.replace("?", entityType))).click();
	}
	
	public void selectRegisteredOrganisation(String registrationNumber) {
		registeredOrganisationRadial.click();
		
		if(registeredOrganisationRadial.isSelected()) {
			registrationNumberTextbox.clear();
			registrationNumberTextbox.sendKeys(registrationNumber);
		}
	}
	
	public void selectCharity(String registrationNumber) {
		charityRadial.click();
		
		if(charityRadial.isSelected()) {
			registrationNumberTextbox.clear();
			registrationNumberTextbox.sendKeys(registrationNumber);
		}
	}
	
	public void selectUnregisteredEntity(String entityType, String entityName) {
		unregisteredEntityRadial.click();
		
		if(unregisteredEntityRadial.isSelected()) {
			if(entityType != "" || entityName != "") {
				enterUnregisteredEntityDetails(entityType, entityName);
			}
		}
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
	
	public LegalEntityReviewPage goToLegalEntityReviewPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, LegalEntityReviewPage.class);
	}
	
	private void enterUnregisteredEntityDetails(String entityType, String entityName) {
		driver.findElement(By.xpath(legalEntTypeRadio.replace("?", entityType))).click();
		
		legalEntityNameTextbox.clear();
		legalEntityNameTextbox.sendKeys(entityName);
	}
}
