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

	@FindBy(xpath = "//legend/span[contains(text(),'Enforcement officer')]")
	private WebElement officer;
	
	@FindBy(xpath = "//legend/span[contains(text(),'Enforcing authority')]")
	private WebElement enforceingAuthority;
	
	@FindBy(xpath = "//legend/span[contains(text(),'Enforced organisation')]")
	private WebElement enforcedOrganisation;
	
	@FindBy(xpath = "//legend/span[contains(text(),'Primary authority')]")
	private WebElement primaryAuthority;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	String legEnt = "//fieldset/p[contains(text(),'?')]";
	String enfType = "//fieldset/p[contains(text(),'?')]";
	String enfTitle = "//div/h3[contains(text(),'?')]";
	String desc = "//div/p[contains(text(),'?')]";
	String enfFile = "//span/a[contains(text(),'?')]";
	
	public EnforcementReviewPage() throws ClassNotFoundException, IOException {
		super();
	}

	public EnforcementCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, EnforcementCompletionPage.class);
	}

	public boolean checkEnforcementCreation() {
		WebElement legEnt1 = driver.findElement(By.xpath(legEnt.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));
		WebElement enfType1 = driver.findElement(By.xpath(enfType.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_TYPE).toLowerCase())));
		WebElement enfTitle1 = driver.findElement(By.xpath(enfTitle.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE))));
		WebElement desc1 = driver.findElement(By.xpath(desc.replace("?", DataStore.getSavedValue(UsableValues.ENFORCEMENT_DESCRIPTION).toLowerCase())));

		return (legEnt1.isDisplayed() && enfType1.isDisplayed() && enfTitle1.isDisplayed() && desc1.isDisplayed());
	}
	
	public boolean checkOfficerDetails() {
		return (officer.isDisplayed() && enforceingAuthority.isDisplayed() && enforcedOrganisation.isDisplayed() && primaryAuthority.isDisplayed());
	}
}