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
	private WebElement descriptionBox;

	@FindBy(id = "edit-files-upload")
	private WebElement chooseFile1;
	
	@FindBy(xpath = "//input[contains(@value,'Save')]")
	private WebElement saveBtn;

	public ReplyDeviationRequestPage enterFeedbackDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, ReplyDeviationRequestPage.class);
	}

	public ReplyDeviationRequestPage chooseFile(String filename) {
		uploadDocument(chooseFile1, filename);
		return PageFactory.initElements(driver, ReplyDeviationRequestPage.class);
	}

	public DeviationReviewPage proceed() {
		saveBtn.click();
		return PageFactory.initElements(driver, DeviationReviewPage.class);
	}
}
