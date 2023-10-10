package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanCoveragePage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class LegalEntityReviewPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public LegalEntityReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipConfirmationPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public InspectionPlanCoveragePage clickContinueForMember() {
		continueBtn.click();
		return PageFactory.initElements(driver, InspectionPlanCoveragePage.class);
	}
}