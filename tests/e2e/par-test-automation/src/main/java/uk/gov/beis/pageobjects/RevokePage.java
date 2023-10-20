package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanSearchPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipRevokedPage;

public class RevokePage extends BasePageObject {
	
	@FindBy(id = "edit-revocation-reason")
	private WebElement reasonTextArea;
	
	@FindBy(id = "edit-next")
	private WebElement revokeNextBtn;
	
	@FindBy(id = "edit-save")
	private WebElement revokeSaveBtn;
	
	public RevokePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterReasonForRevocation(String reason) {
		reasonTextArea.clear();
		reasonTextArea.sendKeys(reason);
	}
	
	public PartnershipRevokedPage goToPartnershipRevokedPage() {
		revokeNextBtn.click();
		return PageFactory.initElements(driver, PartnershipRevokedPage.class);
	}
	
	public PartnershipConfirmationPage goToPartnershipDetailsPage() {
		revokeSaveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public InspectionPlanSearchPage goToInspectionPlanSearchPage() throws Throwable {
		revokeSaveBtn.click();
		return PageFactory.initElements(driver, InspectionPlanSearchPage.class);
	}
}
