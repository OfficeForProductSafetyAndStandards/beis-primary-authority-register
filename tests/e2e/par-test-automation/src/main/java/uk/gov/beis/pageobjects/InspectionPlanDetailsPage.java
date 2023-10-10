package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class InspectionPlanDetailsPage extends BasePageObject {

	@FindBy(id = "edit-title")
	private WebElement title;
	
	@FindBy(id = "edit-summary")
	private WebElement descriptionBox;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn; 
	
	public InspectionPlanDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterTitle(String value) {
		title.clear();
		title.sendKeys(value);
	}

	public void enterInspectionDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}

	public InspectionPlanExpirationPage save() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionPlanExpirationPage.class);
	}
}
