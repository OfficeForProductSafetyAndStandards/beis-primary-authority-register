package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class InspectionPlanReviewPage extends BasePageObject {
	
	private String descriptionLocator = "//div/p[contains(text(),'?')]";
	
	public InspectionPlanReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkInspectionPlan() {
		boolean title = driver.findElement(By.xpath("//div/h1")).isDisplayed();
		WebElement desc = driver.findElement(By.xpath(descriptionLocator.replace("?", DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_DESCRIPTION))));

		return (title && desc.isDisplayed());
	}
}
