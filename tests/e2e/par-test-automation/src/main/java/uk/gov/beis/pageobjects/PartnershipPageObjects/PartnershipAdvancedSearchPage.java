package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class PartnershipAdvancedSearchPage extends BasePageObject {
	
	@FindBy(id = "edit-keywords")
	private WebElement searchInput;

	@FindBy(xpath = "//input[contains(@value,'Search')]")
	private WebElement searchBtn;
	
	public PartnershipAdvancedSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void searchPartnerships() {
		searchInput.clear();
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		searchBtn.click();
	}

	public void selectPartnershipLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td[1]/a[1]")).click();
	}
	
	public void selectApproveBusinessNameLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td/a[contains(text(),'Approve')]")).click();
	}
	
	public void selectDeletePartnershipLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td/a[contains(text(),'Delete')]")).click();
	}
	
	public void selectRevokeBusinessNameLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td/a[contains(text(),'Revoke')]")).click();
	}
	
	public void selectRestoreBusinessNameLink() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td/a[contains(text(),'Restore')]")).click();
	}
	
	public boolean checkPartnershipDetails(String status, String action) {
		WebElement statusElement = driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td[3][normalize-space()='"+ status +"']"));
		WebElement actionElement = driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td[7]/a[contains(text(),'" + action + "')]"));
		
		return (statusElement.isDisplayed() && actionElement.isDisplayed());
	}
	
	public boolean checkPartnershipStatus(String status) {
		WebElement statusElement = driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]/parent::td/parent::tr/td[3][normalize-space()='"+ status +"']"));
		
		return statusElement.isDisplayed();
	}
	
	public boolean checkPartnershipExists() {
		return driver.findElements(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]")).isEmpty();
	}
	
	public void searchPartnershipsPrimaryAuthority() {
		searchInput.clear();
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		searchBtn.click();
	}
	
	public void selectPrimaryAuthorityLink() {
		driver.findElement(By.xpath("//td/a[contains(normalize-space(),'" + DataStore.getSavedValue(UsableValues.AUTHORITY_NAME) + "')]")).click();
	}
	
	public void selectOrganisationLink() {
		driver.findElement(By.xpath("//td/a[contains(normalize-space(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]")).click();
	}
}
