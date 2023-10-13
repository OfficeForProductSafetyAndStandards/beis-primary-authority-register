package uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ReplyInspectionFeedbackPage extends BasePageObject {
	public ReplyInspectionFeedbackPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	private WebElement descriptionBox;

	@FindBy(id = "edit-files-upload")
	private WebElement chooseFile1;
	
	@FindBy(xpath = "//input[contains(@value,'Save')]")
	private WebElement saveBtn;

	public InspectionFeedbackDetailsPage enterFeedbackDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
	}

	public InspectionFeedbackDetailsPage chooseFile(String filename) {
		uploadDocument(chooseFile1, filename);
		return PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
	}

	public InspectionFeedbackConfirmationPage proceed() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
	}
}
