package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class ReplyDeviationRequestPage extends BasePageObject {

	public ReplyDeviationRequestPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	public ReplyDeviationRequestPage enterFeedbackDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, ReplyDeviationRequestPage.class);
	}

	@FindBy(id = "edit-files-upload")
	WebElement chooseFile1;

	public ReplyDeviationRequestPage chooseFile(String filename) {
		chooseFile1.sendKeys(System.getProperty("user.dir") + "/" + filename);
		return PageFactory.initElements(driver, ReplyDeviationRequestPage.class);
	}

	public DeviationReviewPage proceed() {
		saveBtn.click();
		return PageFactory.initElements(driver, DeviationReviewPage.class);
	}
}
