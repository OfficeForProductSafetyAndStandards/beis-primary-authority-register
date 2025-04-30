package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;
import java.util.Map;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import io.cucumber.datatable.DataTable;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class EnforcementDetailsPage extends BasePageObject {

	@FindBy(id = "edit-summary")
	private WebElement descriptionBox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	private String enforcementTypeLocator = "//label[contains(text(),'?')]";

	public EnforcementDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void setEnforcementDetails(DataTable details) {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			DataStore.saveValue(UsableValues.ENFORCEMENT_TYPE, data.get("Enforcement Action"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_SUMMARY, data.get("Summary"));
		}
	}

	public void selectEnforcementType(String type) {
		driver.findElement(By.xpath(enforcementTypeLocator.replace("?", type))).click();
	}

	public void enterEnforcementDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}

	public void clickContinueButton() {
        waitForElementToBeClickable(By.id("edit-next"), 3000);
        continueBtn.click();
        waitForPageLoad();
	}
}
