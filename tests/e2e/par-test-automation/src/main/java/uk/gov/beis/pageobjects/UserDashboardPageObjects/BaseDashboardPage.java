package uk.gov.beis.pageobjects.UserDashboardPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class BaseDashboardPage extends BasePageObject{
	
	@FindBy(id = "block-cookiebanner")
	private WebElement cookieBanner;
	
	@FindBy(id = "block-par-theme-page-title")
	private WebElement dashBoardHeader;
	
	@FindBy(linkText = "Manage your profile details")
	private WebElement manageYourProfileDetailsBtn;
	
	private String userAccountLocator = "//a[contains(normalize-space(), '?')]";
	
	public BaseDashboardPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void acceptCookies() {
		driver.manage().deleteAllCookies();
		
		WebElement acceptBtn = cookieBanner.findElement(By.xpath("//button[contains(text(),'Accept')]"));
		acceptBtn.click();
	}
	
	public void hideCookieBanner() {
		cookieBanner.findElement(By.xpath("//button[contains(text(),'Hide this message')]")).click();
	}
	
	public Boolean checkCookiesAccepted() {
		WebElement cookiesAccepted = cookieBanner.findElement(By.id("govuk-cookies-accepted"));
		
		return cookiesAccepted.getText().contains("You’ve accepted additional cookies.");
	}
	
	public Boolean checkCookieBannerExists() {
		return driver.findElements(By.id("block-cookiebanner")).isEmpty();
	}
	
	public Boolean checkPage() {
		return dashBoardHeader.getText().contains("Dashboard");
	}
	
	public Boolean checkUserAccountEmailAddress() {
		return driver.findElement(By.xpath(userAccountLocator.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL)))).isDisplayed();
	}
	
	public void selectManageProfileDetails() {
		manageYourProfileDetailsBtn.click();
	}
}
