package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DeviationCompletionPage;
import uk.gov.beis.utility.DataStore;

public class InspectionPlanReviewPage extends BasePageObject {

	public InspectionPlanReviewPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	public DeviationCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, DeviationCompletionPage.class);
	}

	String desc = "//div/p[contains(text(),'?')]";

	public boolean checkInspectionPlan() {
		boolean title1 = driver.findElement(By.xpath("//div/h1")).isDisplayed();
		WebElement desc1 = driver.findElement(
				By.xpath(desc.replace("?", DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION))));

		return (title1 && desc1.isDisplayed());
	}

}
