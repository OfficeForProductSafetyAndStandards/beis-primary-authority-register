package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DeviationRequestPageObjects.RequestDeviationPage;
import uk.gov.beis.pageobjects.EnforcementNoticePageObjects.EnforceLegalEntityPage;
import uk.gov.beis.pageobjects.GeneralEnquiryPageObjects.RequestEnquiryPage;
import uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects.InspectionFeedbackDetailsPage;

public class EnforcementOfficerContactDetailsPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public EnforcementOfficerContactDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	public EnforceLegalEntityPage goToEnforceLegalEntityPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforceLegalEntityPage.class);
	}
	
	public RequestDeviationPage goToDeviationRequestPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, RequestDeviationPage.class);
	}
	
	public InspectionFeedbackDetailsPage goToInspectionFeedbackDetailsPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
	}
	
	public RequestEnquiryPage goToRequestEnquiryPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, RequestEnquiryPage.class);
	}
}
