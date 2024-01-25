package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipInformationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipRestoredPage;
import uk.gov.beis.pageobjects.UserManagement.UserProfilePage;

public class ReinstatePage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement reinstateBtn;
	
	public ReinstatePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipRestoredPage goToPartnershipRestoredPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipRestoredPage.class);
	}
	
	public PartnershipInformationPage goToPartnershipDetailsPage() {
		reinstateBtn.click();
		return PageFactory.initElements(driver, PartnershipInformationPage.class);
	}
	
	public UserProfilePage goToUserProfilePage() {
		continueBtn.click();
		return PageFactory.initElements(driver, UserProfilePage.class);
	}
}
