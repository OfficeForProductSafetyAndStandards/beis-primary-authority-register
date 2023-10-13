package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ConfirmThisAmendmentPage extends BasePageObject {
	
	@FindBy(id = "edit-confirmation")
	private WebElement confirmationCheckbox;
	
	@FindBy(id = "edit-next")
	private WebElement submitAmendmentBtn;
	
	public ConfirmThisAmendmentPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public AmendmentCompletedPage confirmLegalEntityAmendments() {
		if(!confirmationCheckbox.isSelected()) {
			confirmationCheckbox.click();
		}
		
		submitAmendmentBtn.click();
		
		return PageFactory.initElements(driver, AmendmentCompletedPage.class);
	}
}
