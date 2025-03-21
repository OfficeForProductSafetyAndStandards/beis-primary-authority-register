package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class LegalEntityReviewPage extends BasePageObject {
	
	@FindBy(id = "edit-par-component-legal-entity-actions-add-another")
	private WebElement addAnotherLink;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public LegalEntityReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectAddAnotherLink() {
		addAnotherLink.click();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}