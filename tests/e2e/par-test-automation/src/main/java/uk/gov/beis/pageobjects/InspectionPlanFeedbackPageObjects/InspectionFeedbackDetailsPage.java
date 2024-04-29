package uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class InspectionFeedbackDetailsPage extends BasePageObject{
	
	@FindBy(id = "edit-notes")
	private WebElement descriptionBox;

	@FindBy(id = "edit-files-upload")
	private WebElement chooseFile;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn; 
	
	public InspectionFeedbackDetailsPage() throws ClassNotFoundException, IOException {
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
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public InspectionFeedbackConfirmationPage goToFeedbackConfirmationPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
	}
}
