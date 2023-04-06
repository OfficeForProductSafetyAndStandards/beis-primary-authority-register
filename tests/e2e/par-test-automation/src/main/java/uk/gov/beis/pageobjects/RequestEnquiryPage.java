package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class RequestEnquiryPage extends BasePageObject {

	public RequestEnquiryPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	@FindBy(xpath = "//input[@id='edit-files-upload']")
	WebElement chooseFile1;

	public RequestEnquiryPage chooseFile(String filename) {
		chooseFile1.sendKeys(System.getProperty("user.dir") + "/" + filename);
		return PageFactory.initElements(driver, RequestEnquiryPage.class);
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	public RequestEnquiryPage enterDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, RequestEnquiryPage.class);
	}

	public EnquiryReviewPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnquiryReviewPage.class);
	}

}
