package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class ReplyEnquiryPage extends BasePageObject {
	
	public ReplyEnquiryPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement continueBtn;

	@FindBy(xpath = "//input[@id='edit-files-upload']")
	WebElement chooseFile1;

	public ReplyEnquiryPage chooseFile(String filename) {
		chooseFile1.sendKeys(System.getProperty("user.dir") + "/" + filename);
		return PageFactory.initElements(driver, ReplyEnquiryPage.class);
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	public ReplyEnquiryPage enterDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, ReplyEnquiryPage.class);
	}

	public EnquiryReviewPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnquiryReviewPage.class);
	}
}