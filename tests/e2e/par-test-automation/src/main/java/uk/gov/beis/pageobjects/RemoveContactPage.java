package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class RemoveContactPage extends BasePageObject {
	
	@FindBy(id = "edit-save")
	private WebElement removeBtn;
	
	public RemoveContactPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipConfirmationPage clickRemoveButton() {
		removeBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
