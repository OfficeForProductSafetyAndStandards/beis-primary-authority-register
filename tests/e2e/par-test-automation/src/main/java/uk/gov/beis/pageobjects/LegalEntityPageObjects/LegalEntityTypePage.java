package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.helper.ScenarioContext;
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
	private WebElement legalEnityNameTextbox;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	String legalEntType = "//label[contains(text(),'?')]/preceding-sibling::input";
	
	public LegalEntityTypePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public LegalEntityReviewPage selectEntityType(String name, String type, String reg) {
		if (!type.equalsIgnoreCase("unregistered")) {
			ScenarioContext.registered = true;
			driver.findElement(By.xpath(legalEntType.replace("?", type))).click();
			driver.findElement(By.id("edit-par-component-legal-entity-0-registered-legal-entity-number")).sendKeys(reg);
		} else {
			ScenarioContext.registered = false;
			driver.findElement(By.xpath(legalEntType.replace("?", type))).click();
			driver.findElement(By.xpath("//label[contains(text(),'Other')]")).click();
			driver.findElement(By.xpath("//input[@class='form-group form-text form-control govuk-input']")).sendKeys(name);
		}
		
		continueBtn.click();
		
		return PageFactory.initElements(driver, LegalEntityReviewPage.class);
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
	
	public LegalEntityReviewPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, LegalEntityReviewPage.class);
	}
	
}
