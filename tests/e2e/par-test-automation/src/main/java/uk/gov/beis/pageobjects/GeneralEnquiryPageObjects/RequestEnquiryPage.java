package uk.gov.beis.pageobjects.GeneralEnquiryPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class RequestEnquiryPage extends BasePageObject {

	@FindBy(id = "edit-notes")
	private WebElement descriptionBox;

	@FindBy(id = "edit-files-upload")
	private WebElement chooseFile;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public RequestEnquiryPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterDescription(String description) throws Throwable {
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
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
