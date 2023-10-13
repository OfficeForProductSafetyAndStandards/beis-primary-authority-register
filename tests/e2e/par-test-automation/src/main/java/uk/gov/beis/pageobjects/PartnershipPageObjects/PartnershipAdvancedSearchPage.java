package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DeletePage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.DeclarationPage;
import uk.gov.beis.utility.DataStore;

public class PartnershipAdvancedSearchPage extends BasePageObject {
	
	@FindBy(id = "edit-keywords")
	private WebElement searchInput;

	@FindBy(xpath = "//input[contains(@value,'Search')]")
	private WebElement searchBtn;
	
	public PartnershipAdvancedSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipAdvancedSearchPage searchPartnerships() {
		searchInput.clear();
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		searchBtn.click();
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}

	public PartnershipConfirmationPage selectPartnershipLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td[1]/a[1]")).click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public DeclarationPage selectApproveBusinessNameLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td/a[contains(text(),'Approve')]")).click();
		return PageFactory.initElements(driver, DeclarationPage.class);
	}
	
	public DeletePage selectDeletePartnershipLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td/a[contains(text(),'Delete')]")).click();
		return PageFactory.initElements(driver, DeletePage.class);
	}
	
	public RevokePartnershipConfirmationPage selectRevokeBusinessNameLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td/a[contains(text(),'Revoke')]")).click();
		return PageFactory.initElements(driver, RevokePartnershipConfirmationPage.class);
	}
	
	public RestorePartnershipConfirmationPage selectRestoreBusinessNameLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td/a[contains(text(),'Restore')]")).click();
		return PageFactory.initElements(driver, RestorePartnershipConfirmationPage.class);
	}
	
	public boolean checkPartnershipDetails(String status, String action) {
		WebElement status1 = driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td[3][contains(text(),'" + status + "')]"));
		WebElement action1 = driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td[7]/a[contains(text(),'" + action + "')]"));
		
		return (status1.isDisplayed() && action1.isDisplayed());
	}
	
	public boolean checkPartnershipExists() {
		return driver.findElements(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]")).isEmpty();
	}
	
	public PartnershipAdvancedSearchPage searchPartnershipsPrimaryAuthority() {
		searchInput.clear();
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		searchBtn.click();
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}
	
	public PartnershipConfirmationPage selectPrimaryAuthorityLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.AUTHORITY_NAME) + "')]/parent::td/parent::tr/td[1]/a[1]")).click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public PartnershipConfirmationPage selectOrganisationLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td[1]/a[2]")).click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
