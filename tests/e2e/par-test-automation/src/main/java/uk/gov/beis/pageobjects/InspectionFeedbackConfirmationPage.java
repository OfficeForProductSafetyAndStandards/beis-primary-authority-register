package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class InspectionFeedbackConfirmationPage extends BasePageObject {

	public InspectionFeedbackConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	public InspectionFeedbackCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackCompletionPage.class);
	}

	String legEnt = "//fieldset/p[contains(text(),'?')]";
	String enfType = "//fieldset/p[contains(text(),'?')]";
	String enfTitle = "//fieldset/h3[contains(text(),'?')]";
	String desc = "//div/p[contains(text(),'?')]";
	String enfFile = "//span/a[contains(text(),'?')]";

	public boolean checkInspectionFeedback() {
		WebElement legEnt1 = driver
				.findElement(By.xpath(legEnt.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));
		WebElement enfType1 = driver.findElement(
				By.xpath(enfType.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_TYPE).toLowerCase())));

		WebElement enfTitle1 = driver
				.findElement(By.xpath(enfTitle.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE))));
		WebElement desc1 = driver.findElement(By
				.xpath(desc.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION).toLowerCase())));

		return (legEnt1.isDisplayed() && enfType1.isDisplayed() && enfTitle1.isDisplayed() && desc1.isDisplayed());
	}

}
