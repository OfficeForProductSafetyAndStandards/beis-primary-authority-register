package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.SharedPageObjects.DeclarationPage;
import uk.gov.beis.utility.DataStore;

public class PartnershipSearchPage extends BasePageObject {
	
	@FindBy(id = "edit-keywords")
	private WebElement searchInput;
	
	@FindBy(xpath = "//input[@value='Search']")
	private WebElement searchPartnershipsBtn;
	
	private String authority = "//td/a[contains(text(),'?')]";
	
	public PartnershipSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void searchPartnerships() {
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		searchPartnershipsBtn.click();
	}

	public DeclarationPage selectBusinessNameLink() {
		driver.findElement(By.linkText(DataStore.getSavedValue(UsableValues.BUSINESS_NAME))).click();
		return PageFactory.initElements(driver, DeclarationPage.class);
	}
	
	public PartnershipInformationPage selectBusinessNameLinkFromPartnership() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]")).click();
		return PageFactory.initElements(driver, PartnershipInformationPage.class);
	}
	
	public PartnershipInformationPage selectAuthority(String auth) {
		WebElement link = driver.findElement(By.xpath(authority.replace("?", auth)));
		link.click();
		return PageFactory.initElements(driver, PartnershipInformationPage.class);
	}
	
	public PartnershipInformationPage selectPartnershipLink(String businessName) {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + businessName + "')]")).click();
		return PageFactory.initElements(driver, PartnershipInformationPage.class);
	}
}
