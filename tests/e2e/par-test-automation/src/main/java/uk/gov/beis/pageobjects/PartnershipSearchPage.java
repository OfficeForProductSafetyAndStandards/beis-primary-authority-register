package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class PartnershipSearchPage extends BasePageObject {

	public PartnershipSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-keywords")
	WebElement searchInput;

	@FindBy(xpath = "//input[contains(@value,'Search')]")
	WebElement searchBtn;
	
	@FindBy(xpath = "//td[@class='views-field views-field-organisation-name']")
	private WebElement partnershipTableFirstElement;
	
	public AuthorityPage searchPartnerships() {
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		searchBtn.click();
		return PageFactory.initElements(driver, AuthorityPage.class);
	}

	public DeclarationPage selectBusinessNameLink() {
		driver.findElement(By.linkText(DataStore.getSavedValue(UsableValues.BUSINESS_NAME))).click();
		return PageFactory.initElements(driver, DeclarationPage.class);
	}
	public PartnershipConfirmationPage selectBusinessNameLinkFromPartnership() {
		driver.findElement(By.xpath("//td/a[contains(text(),'" + DataStore.getSavedValue(UsableValues.BUSINESS_NAME) + "')]")).click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}

	private String authority = "//td/a[contains(text(),'?')]";

	public PartnershipConfirmationPage selectAuthority(String auth) {
		WebElement link = driver.findElement(By.xpath(authority.replace("?", auth)));
		link.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public void searchForPartnership(String partnership) {
		searchInput.sendKeys(partnership);
	}
	
	public void clickSearchButton() {
		searchBtn.click();
	}
	
	public Boolean partnershipContains(String name) {
		return partnershipTableFirstElement.getText().contains(name);
	}
}
