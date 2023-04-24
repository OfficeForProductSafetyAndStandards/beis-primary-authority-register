package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class ReplyInspectionFeedbackPage extends BasePageObject {
	public ReplyInspectionFeedbackPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	public InspectionFeedbackDetailsPage enterFeedbackDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
	}

	@FindBy(id = "edit-files-upload")
	WebElement chooseFile1;

	public InspectionFeedbackDetailsPage chooseFile(String filename) {
		chooseFile1.sendKeys(System.getProperty("user.dir") + "/" + filename);
		return PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
	}

	public InspectionFeedbackConfirmationPage proceed() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
	}
}
