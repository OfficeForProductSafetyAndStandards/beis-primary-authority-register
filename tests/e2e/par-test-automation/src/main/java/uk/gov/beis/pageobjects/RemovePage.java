package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanSearchPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class RemovePage extends BasePageObject {
	
	@FindBy(id = "edit-remove-reason")
	private WebElement removeReasonTextArea;
	
	@FindBy(id = "edit-next")
	private WebElement removeNextBtn;
	
	@FindBy(id = "edit-save")
	private WebElement removeSaveBtn;
	
	public RemovePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterRemoveReason(String reason) {
		removeReasonTextArea.clear();
		removeReasonTextArea.sendKeys(reason);
	}
	
	public InspectionPlanSearchPage goToInspectionPlanSearchPage() throws Throwable {
		removeNextBtn.click();
		return PageFactory.initElements(driver, InspectionPlanSearchPage.class);
	}
	
	public PartnershipConfirmationPage goToPartnershipDetailsPage() {
		removeSaveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
