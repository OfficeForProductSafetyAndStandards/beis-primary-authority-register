package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipTermsPage extends BasePageObject {

	@FindBy(id = "edit-confirm")
	private WebElement confirmCheckbox;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public PartnershipTermsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void deselectTerms() {
		
		if (confirmCheckbox.isSelected())
		{
			confirmCheckbox.click();
		}
	}
	
	public void acceptTerms() {
		
		if (!confirmCheckbox.isSelected())
		{
			confirmCheckbox.click();
		}
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
