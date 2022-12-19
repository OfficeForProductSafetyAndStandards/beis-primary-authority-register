package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class AuthorityDashboardPage extends BasePageObject {
	
	public AuthorityDashboardPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(linkText = "Add an authority")
	WebElement addAuthorityBtn;
	
	@FindBy(id = "edit-name-search")
	WebElement searchInput;

	@FindBy(xpath = "//input[contains(@value,'Search')]")
	WebElement searchBtn;
	
	@FindBy(linkText = "Manage authority")
	WebElement authorityLink;

	public AuthorityDashboardPage searchAuthority() {
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.AUTHORITY_NAME));
		searchBtn.click();
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
