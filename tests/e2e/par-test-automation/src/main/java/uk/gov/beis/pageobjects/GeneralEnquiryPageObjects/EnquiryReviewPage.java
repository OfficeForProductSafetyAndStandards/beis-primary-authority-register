package uk.gov.beis.pageobjects.GeneralEnquiryPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class EnquiryReviewPage extends BasePageObject {

	@FindBy(linkText = "Submit a response")
	private WebElement submitResponse;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String descriptionLocator = "//div/p[contains(text(),'?')]";
	private String responseLocator = "//div/p[contains(text(),'?')]";
	private String fileLocator = "//span/a[contains(text(),'?')]";
	
	public EnquiryReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkEnquiryDescription() {
		WebElement enquiryDescription = driver.findElement(By.xpath(descriptionLocator.replace("?", DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION))));

		return enquiryDescription.isDisplayed();
	}
	
	public boolean checkEnquiryDetails() {
		WebElement enquiryDescription = driver.findElement(By.xpath(descriptionLocator.replace("?", DataStore.getSavedValue(UsableValues.ENQUIRY_DESCRIPTION))));
		WebElement file = driver.findElement(By.xpath(fileLocator.replace("?", "link")));
		return enquiryDescription.isDisplayed() && file.isDisplayed();
	}

	public boolean checkEnquiryResponse() {
		WebElement reply = driver.findElement(By.xpath(responseLocator.replace("?", DataStore.getSavedValue(UsableValues.MESSAGE_RESPONSE))));
		WebElement file = driver.findElement(By.xpath(fileLocator.replace("?", "link")));
		return reply.isDisplayed() && file.isDisplayed();
	}
	
	public void clickSubmitResponse() {
		submitResponse.click();
	}
	
	public void clickSaveChanges() {
		saveBtn.click();
	}
}
