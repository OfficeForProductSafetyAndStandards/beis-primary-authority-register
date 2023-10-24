package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class ProfileReviewPage extends BasePageObject {

	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public ProfileReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public ProfileCompletionPage goToProfileCompletionPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, ProfileCompletionPage.class);
	}
	
	public PartnershipConfirmationPage clickSaveButton() {
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
