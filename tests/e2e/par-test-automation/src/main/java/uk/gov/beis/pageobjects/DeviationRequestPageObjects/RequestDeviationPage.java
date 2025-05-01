package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

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

	public void clearFields() {
		descriptionBox.clear();
		chooseFile.clear();
	}

	public void clickContinueButton() {
        waitForElementToBeClickable(By.id("edit-next"), 3000);
        continueBtn.click();
        waitForPageLoad();
	}
}
