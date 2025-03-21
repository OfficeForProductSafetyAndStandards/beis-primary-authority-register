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

public class EnforcementActionPage extends BasePageObject {
	
	@FindBy(id = "edit-par-component-enforcement-action-0-title")
	private WebElement title;
	
	@FindBy(id = "edit-par-component-enforcement-action-0-details")
	private WebElement descriptionBox;

	@FindBy(id = "edit-par-component-enforcement-action-0-files-upload")
	private WebElement chooseFile;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	private String regulatoryFunctionsLocator = "//label[contains(text(),'?')]";
	
	public EnforcementActionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void setEnforcementActionDetails(DataTable details) {
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {
			
			DataStore.saveValue(UsableValues.ENFORCEMENT_TITLE, data.get("Title"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_DESCRIPTION, data.get("Description"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_REGFUNC, data.get("Regulatory Function"));
			DataStore.saveValue(UsableValues.ENFORCEMENT_FILENAME, data.get("Attachment"));
		}
	}
	
	public void enterTitle(String value) {
		title.clear();
		title.sendKeys(value);
	}
	
	public void selectRegulatoryFunctions(String func) {
		driver.findElement(By.xpath(regulatoryFunctionsLocator.replace("?", func))).click();
	}
	
	public void enterEnforcementDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}
	
	public void chooseFile(String filename) {
		uploadDocument(chooseFile, filename);
		
		DataStore.saveValue(UsableValues.ENFORCEMENT_FILENAME, filename.replace(".txt", ""));
	}
	
	public void clearAllFields() {
		title.clear();
		descriptionBox.clear();
		chooseFile.clear();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
