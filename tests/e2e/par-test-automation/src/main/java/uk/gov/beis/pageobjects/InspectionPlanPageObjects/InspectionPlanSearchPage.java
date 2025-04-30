package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class InspectionPlanSearchPage extends BasePageObject {

	@FindBy(linkText = "Upload inspection plan")
	private WebElement uploadBtn;

	@FindBy(linkText = "Edit inspection plan")
	private WebElement editBtn;

	@FindBy(linkText = "Revoke inspection plan")
	private WebElement revokeBtn;

	@FindBy(linkText = "Remove inspection plan")
	private WebElement removeBtn;

	String planstatus = "//td/a[contains(text(),'?')]/parent::td/following-sibling::td[1]";

	public InspectionPlanSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectUploadLink() {
		uploadBtn.click();
	}

	public void selectInspectionPlan() {
		driver.findElement(By.linkText(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE))).click();
	}

	public void selectEditLink() {
		editBtn.click();
	}

	public void selectRevokeLink() {
		revokeBtn.click();
	}

	public void selectRemoveLink() {
		removeBtn.click();
	}

	public String getPlanStatus() {
		try {
            waitForElementToBeVisible(By.xpath(planstatus.replace("?", DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE))), 2000);
			return driver.findElement(By.xpath(planstatus.replace("?", DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE)))).getText();
		} catch (Exception e) {
			return ("No results returned");
		}
	}
}
