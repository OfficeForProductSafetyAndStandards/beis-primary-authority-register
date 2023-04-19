package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class EnquiryReviewPage extends BasePageObject {

	public EnquiryReviewPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	@FindBy(linkText = "Submit a response")
	WebElement submitResponse;

	public EnquiryCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, EnquiryCompletionPage.class);
	}

	public ReplyEnquiryPage submitResponse() {
		submitResponse.click();
		return PageFactory.initElements(driver, ReplyEnquiryPage.class);
	}

	String desc = "//div/p[contains(text(),'?')]";
	String reply = "//div/p[contains(text(),'?')]";
	String status; // This variable is seems to be missing.

	public boolean checkEnquiryCreation() {
		WebElement desc1 = driver
				.findElement(By.xpath(desc.replace("?", DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION))));

		return (desc1.isDisplayed());
	}

	public boolean checkEnquiryReply() {
		WebElement reply1 = driver
				.findElement(By.xpath(reply.replace("?", DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY))));

		return (reply1.isDisplayed());
	}

}
