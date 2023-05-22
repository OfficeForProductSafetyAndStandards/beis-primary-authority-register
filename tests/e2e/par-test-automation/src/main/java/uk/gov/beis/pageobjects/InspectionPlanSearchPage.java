package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class InspectionPlanSearchPage extends BasePageObject {

	public InspectionPlanSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(linkText = "Upload inspection plan")
	WebElement uploadBtn;

	@FindBy(linkText = "Revoke inspection plan")
	WebElement revokeBtn;

	public RevokeReasonInspectionPlanPage selectRevokeLink() {
		revokeBtn.click();
		return PageFactory.initElements(driver, RevokeReasonInspectionPlanPage.class);
	}

	public UploadInspectionPlanPage selectUploadLink() {
		uploadBtn.click();
		return PageFactory.initElements(driver, UploadInspectionPlanPage.class);
	}

	String planstatus = "//td/a[contains(text(),'?')]/parent::td/following-sibling::td[1]";

	public String getPlanStatus() {
		try {
			return driver
					.findElement(By
							.xpath(planstatus.replace("?", DataStore.getSavedValue(UsableValues.INSPECTIONPLAN_TITLE))))
					.getText();
		} catch (Exception e) {
			return ("No results returned");
		}
	}

}
