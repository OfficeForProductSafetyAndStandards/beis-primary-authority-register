package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class RevokeReasonInspectionPlanPage extends BasePageObject {

	public RevokeReasonInspectionPlanPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	public InspectionPlanSearchPage enterRevokeDescription() throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys("Revoking");
		saveBtn.click();

		return PageFactory.initElements(driver, InspectionPlanSearchPage.class);
	}

}
