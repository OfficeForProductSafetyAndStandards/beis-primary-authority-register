package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipTermsPage extends BasePageObject {

	@FindBy(id = "edit-confirm")
	private WebElement confirmCheckbox;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public PartnershipTermsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipDescriptionPage acceptTerms() {
		
		if (!confirmCheckbox.isSelected())
		{
			confirmCheckbox.click();
		}
		
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipDescriptionPage.class);
	}

}
