package uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ReplyInspectionFeedbackPage extends BasePageObject {
	
	@FindBy(id = "edit-message")
	private WebElement descriptionBox;

	@FindBy(id = "edit-files-upload")
	private WebElement chooseFile;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public ReplyInspectionFeedbackPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterFeedbackDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}

	public void chooseFile(String filename) {
		uploadDocument(chooseFile, filename);
	}

	public InspectionFeedbackConfirmationPage clickSave() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
	}
}
