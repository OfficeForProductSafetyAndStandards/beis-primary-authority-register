package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class UploadInspectionPlanPage extends BasePageObject {

	public UploadInspectionPlanPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[@id='edit-inspection-plan-files-upload']")
	private WebElement chooseFile1;

	public InspectionPlanDetailsPage uploadFile() {
		driver.findElement(By.id("edit-upload")).click();
		return PageFactory.initElements(driver, InspectionPlanDetailsPage.class);
	}

	public UploadInspectionPlanPage chooseFile(String filename) {
		uploadDocument(chooseFile1, filename);
		return PageFactory.initElements(driver, UploadInspectionPlanPage.class);
	}

}