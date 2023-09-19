package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class RequestDeviationPage extends BasePageObject{
	
	@FindBy(id = "edit-notes")
	private WebElement descriptionBox;
	
	@FindBy(id = "edit-files-upload")
	private WebElement chooseFile;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public RequestDeviationPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}
	
	public void chooseFile(String filename) {
		uploadDocument(chooseFile, filename);
	}

	public DeviationReviewPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, DeviationReviewPage.class);
	}
}