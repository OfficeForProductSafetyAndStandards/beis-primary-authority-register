package uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

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
		WebElement response = driver.findElement(By.xpath(responseLocator.replace("?", DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_RESPONSE1))));
		WebElement fileUpload = driver.findElement(By.xpath(fileLocator.replace("?", "link")));
		return response.isDisplayed() && fileUpload.isDisplayed();
	}
	
	public ReplyInspectionFeedbackPage submitResponse() {
		submitResponse.click();
		return PageFactory.initElements(driver, ReplyInspectionFeedbackPage.class);
	}
	
	public InspectionFeedbackCompletionPage goToInspectionFeedbackCompletionPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackCompletionPage.class);
	}
}
