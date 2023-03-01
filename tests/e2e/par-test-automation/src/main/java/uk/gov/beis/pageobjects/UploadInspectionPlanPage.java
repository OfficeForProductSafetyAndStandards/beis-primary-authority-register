package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class UploadInspectionPlanPage extends BasePageObject {

	public UploadInspectionPlanPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[@id='edit-inspection-plan-files-upload']")
	WebElement chooseFile1;

	public InspectionPlanDetailsPage uploadFile() {
		driver.findElement(By.id("edit-upload")).click();
		return PageFactory.initElements(driver, InspectionPlanDetailsPage.class);
	}

	public UploadInspectionPlanPage chooseFile(String filename) {
		chooseFile1.sendKeys(System.getProperty("user.dir") + "/" + filename);
		return PageFactory.initElements(driver, UploadInspectionPlanPage.class);
	}

}
