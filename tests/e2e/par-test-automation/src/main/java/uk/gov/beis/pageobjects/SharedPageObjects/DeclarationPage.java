package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.EnforcementNoticePageObjects.EnforcementSearchPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.AboutTheOrganisationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.RegulatoryFunctionPage;
import uk.gov.beis.pageobjects.UserManagement.ContactDetailsPage;

public class DeclarationPage extends BasePageObject {

	@FindBy(id = "edit-confirm-authorisation-select")
	private WebElement authorisedCheckbox;
	
	@FindBy(id = "edit-confirm")
	private WebElement confirmationCheckbox;
	
	@FindBy(id = "edit-data-policy")
	private WebElement dataPolcyCheckbox;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	private boolean advancedsearch = false;
	
	public DeclarationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void setAdvancedSearch(boolean value) {
		this.advancedsearch = value;
	}

	public boolean getAdvancedSearch() {
		return this.advancedsearch;
	}
	
	public void selectAuthorisedCheckbox() {
		if(!authorisedCheckbox.isSelected()) {
			authorisedCheckbox.click();
		}
	}
	
	public void deselectConfirmCheckbox() {
		if(confirmationCheckbox.isSelected()) {
			confirmationCheckbox.click();
		}
	}
	
	public void selectConfirmCheckbox() {
		if(!confirmationCheckbox.isSelected()) {
			confirmationCheckbox.click();
		}
	}
	
	public void selectDataPolicyCheckbox() {
		if(!dataPolcyCheckbox.isSelected()) {
			dataPolcyCheckbox.click();
		}
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public ContactDetailsPage goToContactDetailsPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, ContactDetailsPage.class);
	}
	
	public RegulatoryFunctionPage goToRegulatoryFunctionsPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, RegulatoryFunctionPage.class);
	}
	
	public AboutTheOrganisationPage goToBusinessDetailsPage() {	
		continueBtn.click();
		return PageFactory.initElements(driver, AboutTheOrganisationPage.class);
	}
	
	public EnforcementSearchPage goToEnforcementSearchPage() {
		WebElement element = driver.findElement(By.xpath("//input[@value='Remove']"));
		
		executeJavaScript("arguments[0].click();", element);
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
}
