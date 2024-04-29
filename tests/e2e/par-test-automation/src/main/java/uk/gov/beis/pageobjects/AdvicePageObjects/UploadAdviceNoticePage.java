package uk.gov.beis.pageobjects.AdvicePageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class UploadAdviceNoticePage extends BasePageObject{
	
	@FindBy(id = "edit-files-upload")
	private WebElement chooseFile;
	
	@FindBy(id = "edit-upload")
	private WebElement uploadBtn;
	
	public UploadAdviceNoticePage() throws ClassNotFoundException, IOException {
		super();
	}

	public void chooseFile(String filename) {
		uploadDocument(chooseFile, filename);
	}
	
	public void selectUploadButton() {
		uploadBtn.click();
	}

	public AdviceNoticeDetailsPage uploadFile() {
		uploadBtn.click();
		return PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
	}
}