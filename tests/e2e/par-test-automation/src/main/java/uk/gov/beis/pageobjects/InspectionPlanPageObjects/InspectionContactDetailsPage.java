package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.InspectionFeedbackDetailsPage;

public class InspectionContactDetailsPage extends BasePageObject {

	public InspectionContactDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public InspectionFeedbackDetailsPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
	}

}
