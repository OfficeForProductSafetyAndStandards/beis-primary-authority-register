package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class InspectionPlanDetailsPage extends BasePageObject {

	public InspectionPlanDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn; 
	
	@FindBy(id = "edit-title")
	WebElement title;

	private String locator = "//label[contains(text(),'?')]";

	public InspectionPlanDetailsPage enterTitle(String value) {
		title.clear();
		title.sendKeys(value);
		return PageFactory.initElements(driver, InspectionPlanDetailsPage.class);
	}

	public InspectionPlanDetailsPage enterInspectionDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, InspectionPlanDetailsPage.class);
	}

	public InspectionPlanExpirationPage save() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionPlanExpirationPage.class);
	}

}
