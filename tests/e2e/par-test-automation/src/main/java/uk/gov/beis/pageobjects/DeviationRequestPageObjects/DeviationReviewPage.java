package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class DeviationReviewPage extends BasePageObject {

	@FindBy(linkText = "Submit a response")
	private WebElement submitResponse;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	private String descriptionLocator = "//div/p[contains(text(),'?')]";
	private String statusLocator = "//div/p[contains(text(),'?')]";
	private String responseLocator = "//div/p[contains(text(),'?')]";

	public DeviationReviewPage() throws ClassNotFoundException, IOException {
		super();
	}

	public boolean checkDeviationCreation() {
		WebElement description = driver.findElement(By.xpath(descriptionLocator.replace("?", DataStore.getSavedValue(UsableValues.DEVIATION_DESCRIPTION))));
		return (description.isDisplayed());
	}

	public boolean checkDeviationResponse() {
		WebElement response = driver.findElement(By.xpath(responseLocator.replace("?", DataStore.getSavedValue(UsableValues.MESSAGE_RESPONSE))));
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

	public void clickSubmitResponse() {
        waitForElementToBeClickable(By.linkText("Submit a response"), 3000);
        submitResponse.click();
        waitForPageLoad();
	}

	public void clickSaveChanges() {
        waitForElementToBeClickable(By.id("edit-save"), 3000);
        saveBtn.click();
        waitForPageLoad();
	}
}
