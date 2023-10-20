package uk.gov.beis.pageobjects.DuplicateClasses;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanSearchPage;

public class RevokeReasonInspectionPlanPage extends BasePageObject {

	public RevokeReasonInspectionPlanPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	private WebElement saveBtn;

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	private WebElement descriptionBox;

	public InspectionPlanSearchPage enterRevokeDescription() throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys("Revoking");
		saveBtn.click();

		return PageFactory.initElements(driver, InspectionPlanSearchPage.class);
	}

}
