package uk.gov.beis.pageobjects.UserDashboardPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class BaseDashboardPage extends BasePageObject{
	
	@FindBy(id = "block-par-theme-page-title")
	private WebElement dashBoardHeader;
	
	@FindBy(linkText = "Manage your profile details")
	private WebElement manageYourProfileDetailsBtn;
	
	private String userAccountLocator = "//a[contains(normalize-space(), '?')]";
	
	public BaseDashboardPage() throws ClassNotFoundException, IOException {
		super();
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
