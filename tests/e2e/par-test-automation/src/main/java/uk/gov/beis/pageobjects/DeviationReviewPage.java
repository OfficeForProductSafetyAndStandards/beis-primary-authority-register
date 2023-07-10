package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class DeviationReviewPage extends BasePageObject {

	public DeviationReviewPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;
	
	@FindBy(linkText = "Submit a response")
	WebElement submitResponse;

	public DeviationCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, DeviationCompletionPage.class);
	}
	
	public ReplyDeviationRequestPage submitResponse() {
		submitResponse.click();
		return PageFactory.initElements(driver, ReplyDeviationRequestPage.class);
	}

	String desc = "//div/p[contains(text(),'?')]";
	String status = "//fieldset/p[contains(text(),'?')]";
	String response = "//div/p[contains(text(),'?')]";


	public boolean checkDeviationCreation() {
		WebElement desc1 = driver.findElement(By
				.xpath(desc.replace("?", DataStore.getSavedValue(UsableValues.DEVIATION_DESCRIPTION))));

		return (desc1.isDisplayed());
	}
	
	public boolean checkDeviationResponse() {
		WebElement response1 = driver.findElement(By
				.xpath(response.replace("?", DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1))));

		return (response1.isDisplayed());
	}
	
	public boolean checkDeviationStatus() {
		WebElement status1 = driver.findElement(By
				.xpath(status.replace("?", "Approved")));

		return (status1.isDisplayed());
	}
}
