package uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class InspectionFeedbackCompletionPage extends BasePageObject{

	public InspectionFeedbackCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//a[contains(@class,'button')]")
	WebElement doneBtn;

	public PartnershipConfirmationPage complete() {
			doneBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}