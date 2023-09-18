package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class EnquiryReviewPage extends BasePageObject {

	@FindBy(linkText = "Submit a response")
	private WebElement submitResponse;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	String desc = "//div/p[contains(text(),'?')]";
	String reply = "//div/p[contains(text(),'?')]";
	String status; // This variable is seems to be missing.
	
	public EnquiryReviewPage() throws ClassNotFoundException, IOException {
		super();
	}

	public EnquiryCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, EnquiryCompletionPage.class);
	}

	public ReplyEnquiryPage submitResponse() {
		submitResponse.click();
		return PageFactory.initElements(driver, ReplyEnquiryPage.class);
	}

	public boolean checkEnquiryCreation() {
		WebElement enquiryDescription = driver.findElement(By.xpath(desc.replace("?", DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION))));

		return (enquiryDescription.isDisplayed());
	}

	public boolean checkEnquiryReply() {
		WebElement reply1 = driver.findElement(By.xpath(reply.replace("?", DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY))));

		return (reply1.isDisplayed());
	}
	
	public boolean checkEnquiryReply1() {
		WebElement reply11 = driver.findElement(By.xpath(reply.replace("?", DataStore.getSavedValue(UsableValues.ENQUIRY_REPLY1))));

		return (reply11.isDisplayed());
	}
}
