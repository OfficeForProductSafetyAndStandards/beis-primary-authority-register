package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class UpdateLegalEntityPage extends BasePageObject {
	
	@FindBy(id = "edit-registry-companies-house")
	private WebElement registeredOrganisationRadial;
	
	@FindBy(id = "edit-registry-charity-commission")
	private WebElement charityRadial;
	
	@FindBy(id = "edit-registry-internal")
	private WebElement unregisteredEntityRadial;
	
	@FindBy(id = "edit-legal-entity-number")
	private WebElement registrationNumberTextbox;

	@FindBy(id = "edit-legal-entity-name")
	private WebElement legalEnityNameTextbox;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	String legalEntType = "//label[contains(text(),'?')]";
	
	public UpdateLegalEntityPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectRegisteredOrganisation(String registrationNumber) {
		registeredOrganisationRadial.click();
		
		if(registeredOrganisationRadial.isSelected()) {
			registrationNumberTextbox.sendKeys(registrationNumber);
		}
	}
	
	public void selectCharity(String registrationNumber) {
		charityRadial.click();
		
		if(charityRadial.isSelected()) {
			registrationNumberTextbox.sendKeys(registrationNumber);
		}
	}
	
	public void selectUnregisteredEntity(String entityType, String legalEntityName) {
		unregisteredEntityRadial.click();
		
		if(unregisteredEntityRadial.isSelected()) {
			
			driver.findElement(By.xpath(legalEntType.replace("?", entityType))).click();
			
			legalEnityNameTextbox.sendKeys(legalEntityName);
		}
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
}
