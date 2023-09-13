package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class UploadAdviceNoticePage extends BasePageObject{
	
	public UploadAdviceNoticePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[@id='edit-files-upload']")
	private WebElement chooseFile1;

	public AdviceNoticeDetailsPage uploadFile() {
		driver.findElement(By.id("edit-upload")).click();
		return PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
	}

	public UploadInspectionPlanPage chooseFile(String filename) {
		uploadDocument(chooseFile1, filename);
		return PageFactory.initElements(driver, UploadInspectionPlanPage.class);
	}

}