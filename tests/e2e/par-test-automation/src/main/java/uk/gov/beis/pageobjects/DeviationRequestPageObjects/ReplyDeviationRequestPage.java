package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ReplyDeviationRequestPage extends BasePageObject {
	
	@FindBy(id = "edit-message")
	private WebElement descriptionBox;

	@FindBy(id = "edit-files-upload")
	private WebElement chooseFile;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public ReplyDeviationRequestPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterFeedbackDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}

	public void chooseFile(String filename) {
		uploadDocument(chooseFile, filename);
	}
	
	public void clearAllFields() {
		descriptionBox.clear();
		chooseFile.clear();
	}

	public void clickSaveButton() {
		saveBtn.click();
	}
}
