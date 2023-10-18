package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.RegulatoryFunctionPage;

public class DeclarationPage extends BasePageObject {

	@FindBy(id = "edit-confirm-authorisation-select")
	private WebElement authorisedCheckbox;
	
	@FindBy(id = "edit-confirm")
	private WebElement confirmationCheckbox;
	
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
	
	public void selectConfirmCheckbox() {
		if(!confirmationCheckbox.isSelected()) {
			confirmationCheckbox.click();
		}
	}

	public RegulatoryFunctionPage goToRegulatoryFunctionsPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, RegulatoryFunctionPage.class);
	}
	
	public BusinessDetailsPage goToBusinessDetailsPage() {	
		continueBtn.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
}
