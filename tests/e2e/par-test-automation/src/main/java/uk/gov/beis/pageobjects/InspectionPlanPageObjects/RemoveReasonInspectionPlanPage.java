package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class RemoveReasonInspectionPlanPage extends BasePageObject{
	
	public RemoveReasonInspectionPlanPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Remove')]")
	WebElement saveBtn;

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	public InspectionPlanSearchPage enterRemoveDescription() throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys("Removing");
		saveBtn.click();

		return PageFactory.initElements(driver, InspectionPlanSearchPage.class);
	}

}
