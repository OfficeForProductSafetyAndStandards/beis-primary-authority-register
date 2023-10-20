package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class UploadInspectionPlanPage extends BasePageObject {

	@FindBy(id = "edit-inspection-plan-files-upload")
	private WebElement chooseFile;
	
	@FindBy(id = "edit-upload")
	private WebElement uploadBtn;
	
	public UploadInspectionPlanPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void chooseFile(String filename) {
		uploadDocument(chooseFile, filename);
	}
	
	public InspectionPlanDetailsPage uploadFile() {
		uploadBtn.click();
		return PageFactory.initElements(driver, InspectionPlanDetailsPage.class);
	}

}
