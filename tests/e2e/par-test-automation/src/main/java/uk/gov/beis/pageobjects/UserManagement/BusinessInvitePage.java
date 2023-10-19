package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class BusinessInvitePage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public BusinessInvitePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public PartnershipConfirmationPage sendInvite() {
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
