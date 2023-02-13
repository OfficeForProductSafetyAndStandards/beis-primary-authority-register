package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class InspectionPlanSearchPage extends BasePageObject{
	
	public InspectionPlanSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(linkText = "Upload inspection plan")
	WebElement uploadBtn;

	public UploadInspectionPlanPage selectUploadLink() {
		uploadBtn.click();
		return PageFactory.initElements(driver, UploadInspectionPlanPage.class);
	}

}
