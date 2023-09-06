package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class AuthorityDashboardPage extends BasePageObject {
	
	public AuthorityDashboardPage() throws ClassNotFoundException, IOException {
		super();
		
		LOG.info("Authority Dashboard Page is Loaded!");
	}

	@FindBy(linkText = "Add an authority")
	private WebElement addAuthorityBtn;
	
	@FindBy(id = "edit-name-search")
	private WebElement searchInput;

	@FindBy(xpath = "//input[contains(@value,'Search')]")
	private WebElement searchBtn;
	
	@FindBy(linkText = "Manage authority")
	private WebElement authorityLink;

	public AuthorityDashboardPage searchAuthority() {
		WebDriverWait wait = new WebDriverWait(driver, 60);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("edit-name-search")));
		
		if(searchInput.isDisplayed()) {
			searchInput.sendKeys(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
			searchBtn.click();
		}
		
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}
	
	public AuthorityConfirmationPage selectAuthority() {
		authorityLink.click();
		return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
	}
	
	public AuthorityNamePage selectAddAuthority() {
		addAuthorityBtn.click();
		return PageFactory.initElements(driver, AuthorityNamePage.class);
	}
}
