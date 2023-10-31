package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanCoveragePage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.CheckPartnershipInformationPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipInformationPage;

public class LegalEntityReviewPage extends BasePageObject {
	
	@FindBy(id = "edit-par-component-legal-entity-actions-add-another")
	private WebElement addAnotherLink;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public LegalEntityReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public LegalEntityTypePage selectAddAnotherLink() {
		addAnotherLink.click();
		return PageFactory.initElements(driver, LegalEntityTypePage.class);
	}
	
	public PartnershipInformationPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipInformationPage.class);
	}
	
	public CheckPartnershipInformationPage goToCheckPartnershipInformationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, CheckPartnershipInformationPage.class);
	}
	
	public InspectionPlanCoveragePage clickContinueForMember() {
		continueBtn.click();
		return PageFactory.initElements(driver, InspectionPlanCoveragePage.class);
	}
	
	public ConfirmThisAmendmentPage goToConfirmThisAmendmentPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, ConfirmThisAmendmentPage.class);
	}
	
	
}