package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class EnforcementReviewPage extends BasePageObject {
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	private String legalEntityNameLocator = "//fieldset/p[contains(text(),'?')]";
	private String enforcementTitleLocator = "//div/h3[contains(text(),'?')]";
	private String enforcementTypeLocator = "//fieldset/p[contains(text(),'?')]";
	private String enforcementDescriptionLocator = "//div/p[contains(text(),'?')]";
	private String enforcementFileLocator = "//span/a[contains(text(),'?')]";
	
	public EnforcementReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkEnforcementCreation() {
		WebElement legalEntity = driver.findElement(By.xpath(legalEntityNameLocator.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));
		WebElement enforcementTitle = driver.findElement(By.xpath(enforcementTitleLocator.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE))));
		WebElement enforcementType = driver.findElement(By.xpath(enforcementTypeLocator.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_TYPE).toLowerCase())));
		WebElement enforcementDescription = driver.findElement(By.xpath(enforcementDescriptionLocator.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION).toLowerCase())));
		WebElement enforcementFile = driver.findElement(By.xpath(enforcementFileLocator.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_FILENAME))));
		
		return legalEntity.isDisplayed() && enforcementType.isDisplayed() && enforcementTitle.isDisplayed() && enforcementDescription.isDisplayed() && enforcementFile.isDisplayed();
	}

	public EnforcementCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, EnforcementCompletionPage.class);
	}
}
