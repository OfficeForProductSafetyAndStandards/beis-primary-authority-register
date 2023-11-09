package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class DeviationReviewPage extends BasePageObject {

	@FindBy(linkText = "Submit a response")
	private WebElement submitResponse;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String descriptionLocator = "//div/p[contains(text(),'?')]";
	private String statusLocator = "//fieldset/p[contains(text(),'?')]";
	private String responseLocator = "//div/p[contains(text(),'?')]";
	
	public DeviationReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkDeviationCreation() {
		WebElement description = driver.findElement(By.xpath(descriptionLocator.replace("?", DataStore.getSavedValue(UsableValues.DEVIATION_DESCRIPTION))));
		return (description.isDisplayed());
	}
	
	public boolean checkDeviationResponse() {
		WebElement response = driver.findElement(By.xpath(responseLocator.replace("?", DataStore.getSavedValue(UsableValues.DEVIATIONFEEDBACK_RESPONSE1))));
		return (response.isDisplayed());
	}
	
	public boolean checkDeviationStatusApproved() {
		WebElement status = driver.findElement(By.xpath(statusLocator.replace("?", "Approved")));
		return (status.isDisplayed());
	}
	
	public boolean checkDeviationStatusBlocked() {
		WebElement status = driver.findElement(By.xpath(statusLocator.replace("?", "Blocked")));
		return (status.isDisplayed());
	}
	
	public ReplyDeviationRequestPage submitResponse() {
		submitResponse.click();
		return PageFactory.initElements(driver, ReplyDeviationRequestPage.class);
	}
	
	public DeviationCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, DeviationCompletionPage.class);
	}
}
