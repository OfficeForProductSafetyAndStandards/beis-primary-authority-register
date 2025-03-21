package uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class InspectionFeedbackConfirmationPage extends BasePageObject {
	
	@FindBy(linkText = "Submit a response")
	private WebElement submitResponse;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String descriptionLocator = "//div/p[contains(text(),'?')]";
	private String responseLocator = "//div/p[contains(text(),'?')]";
	private String fileLocator = "//span/a[contains(text(),'?')]";
	
	public InspectionFeedbackConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkInspectionFeedback() {
		WebElement description = driver.findElement(By.xpath(descriptionLocator.replace("?", DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION))));

		return (description.isDisplayed());
	}
	
	public boolean checkInspectionResponse() {
		WebElement response = driver.findElement(By.xpath(responseLocator.replace("?", DataStore.getSavedValue(UsableValues.MESSAGE_RESPONSE))));
		WebElement fileUpload = driver.findElement(By.xpath(fileLocator.replace("?", "link")));
		return response.isDisplayed() && fileUpload.isDisplayed();
	}
	
	public void clickSubmitResponse() {
		submitResponse.click();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
}
