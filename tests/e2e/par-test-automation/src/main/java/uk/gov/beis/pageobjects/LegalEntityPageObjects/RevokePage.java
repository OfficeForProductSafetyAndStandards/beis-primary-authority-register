package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class RevokePage extends BasePageObject {
	
	@FindBy(id = "edit-revocation-reason")
	private WebElement reasonTextArea;

	@FindBy(id = "edit-save")
	private WebElement revokeBtn;
	
	public RevokePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterReasonForRevocation(String reason) {
		reasonTextArea.clear();
		reasonTextArea.sendKeys(reason);
	}
	
	public PartnershipConfirmationPage goToPartnershipDetailsPage() {
		revokeBtn.click();
		
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
