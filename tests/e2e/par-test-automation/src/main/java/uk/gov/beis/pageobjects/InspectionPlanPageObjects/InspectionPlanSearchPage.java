package uk.gov.beis.pageobjects.InspectionPlanPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.SharedPageObjects.RemovePage;
import uk.gov.beis.pageobjects.SharedPageObjects.RevokePage;
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
	
	public UploadInspectionPlanPage selectUploadLink() {
		uploadBtn.click();
		return PageFactory.initElements(driver, UploadInspectionPlanPage.class);
	}
	
	public InspectionPlanDetailsPage selectEditLink() {
		editBtn.click();
		return PageFactory.initElements(driver, InspectionPlanDetailsPage.class);
	}
	
	public RevokePage selectRevokeLink() {
		revokeBtn.click();
		return PageFactory.initElements(driver, RevokePage.class);
	}
	
	public RemovePage selectRemoveLink() {
		removeBtn.click();
		return PageFactory.initElements(driver, RemovePage.class);
	}
	
	public InspectionPlanReviewPage selectInspectionPlan() {
		driver.findElement(By.linkText(DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE))).click();
		return PageFactory.initElements(driver, InspectionPlanReviewPage.class);
	}
	
	public String getPlanStatus() {
		try {
			return driver.findElement(By.xpath(planstatus.replace("?", DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE)))).getText();
		} catch (Exception e) {
			return ("No results returned");
		}
	}
}
