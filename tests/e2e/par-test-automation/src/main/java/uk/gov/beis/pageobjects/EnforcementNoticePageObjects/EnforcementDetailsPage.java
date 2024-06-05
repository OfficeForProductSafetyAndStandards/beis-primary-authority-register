package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;
import java.util.Map;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import cucumber.api.DataTable;
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
		continueBtn.click();
	}
	
	public EnforcementActionPage goToEnforcementActionPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementActionPage.class);
	}
}
