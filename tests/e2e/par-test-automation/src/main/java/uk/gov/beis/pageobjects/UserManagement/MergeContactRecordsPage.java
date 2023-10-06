package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class MergeContactRecordsPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	private String contactLabelLocator = "//label/div[contains(text(), '?')]";
	
	public MergeContactRecordsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void mergeContacts() {
		
		List<WebElement> checkboxes = driver.findElements(By.xpath("//input[@type='checkbox']"));
		
		for(WebElement box : checkboxes) {
			box.click();
		}
		
		List<WebElement> contacts = driver.findElements(By.xpath(contactLabelLocator.replace("?", getContactName())));
		
		for(WebElement record : contacts) {
			record.click();
		}
	}
	
	public MergeContactRecordsConfirmationPage clickContinue() {
		continueBtn.click();
		return PageFactory.initElements(driver, MergeContactRecordsConfirmationPage.class);
	}
	
	private String getContactName() {
		return DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);
	}
}
