package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.EnterTheDatePage;

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

	public void enterInspectionDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}

	public EnterTheDatePage clickSave() {
		saveBtn.click();
		return PageFactory.initElements(driver, EnterTheDatePage.class);
	}
}
