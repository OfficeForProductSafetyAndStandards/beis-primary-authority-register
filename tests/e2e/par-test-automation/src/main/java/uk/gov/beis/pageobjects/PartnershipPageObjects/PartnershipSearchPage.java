package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class PartnershipSearchPage extends BasePageObject {
	
	@FindBy(id = "edit-keywords")
	private WebElement searchInput;
	
	@FindBy(xpath = "//input[@value='Search']")
	private WebElement searchPartnershipsBtn;
	
	private String authorityLocator = "//td[contains(normalize-space(), '£')]/preceding-sibling::td/a[contains(normalize-space(), '?')]";
	
	public PartnershipSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void searchPartnerships() {
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		searchPartnershipsBtn.click();
	}

	public void selectBusinessNameLink() {
		driver.findElement(By.linkText(DataStore.getSavedValue(UsableValues.BUSINESS_NAME))).click();
	}
	
	public void selectBusinessNameLinkFromPartnership() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]")).click();
	}
	
	public void selectAuthority(String auth) {
		WebElement link = driver.findElement(By.xpath(authorityLocator.replace("?", auth).replace("£", DataStore.getSavedValue(UsableValues.BUSINESS_NAME))));
		link.click();
	}
	
	public void selectPartnershipLink(String businessName) {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + businessName + "')]")).click();
	}
}
