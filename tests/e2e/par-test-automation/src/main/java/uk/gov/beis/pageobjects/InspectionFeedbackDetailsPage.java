package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class InspectionFeedbackDetailsPage extends BasePageObject{
	
	public InspectionFeedbackDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn; 

	public InspectionFeedbackDetailsPage enterFeedbackDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
	}
	
	@FindBy(xpath = "//input[@id='edit-files-upload']")
	WebElement chooseFile1;

	public InspectionFeedbackDetailsPage chooseFile(String filename) {
		chooseFile1.sendKeys(System.getProperty("user.dir") + "/" + filename);
		return PageFactory.initElements(driver, InspectionFeedbackDetailsPage.class);
	}

	public InspectionFeedbackConfirmationPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
	}

}
