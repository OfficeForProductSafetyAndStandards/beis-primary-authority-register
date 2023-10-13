package uk.gov.beis.pageobjects.GeneralEnquiryPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ReplyEnquiryPage extends BasePageObject {
	
	public ReplyEnquiryPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	private WebElement descriptionBox;
	
	@FindBy(xpath = "//input[@id='edit-files-upload']")
	private WebElement chooseFile1;
	
	@FindBy(xpath = "//input[contains(@value,'Save')]")
	private WebElement continueBtn;

	public ReplyEnquiryPage chooseFile(String filename) {
		uploadDocument(chooseFile1, filename);
		return PageFactory.initElements(driver, ReplyEnquiryPage.class);
	}

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