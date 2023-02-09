package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class OrganisationDashboardPage extends BasePageObject {

	public OrganisationDashboardPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(id = "edit-name-search")
	WebElement searchInput;
	
	@FindBy(xpath = "//input[contains(@value,'Search')]")
	WebElement searchBtn;
	
	@FindBy(linkText = "Manage organisation")
	WebElement organisationLink;

	public AuthorityDashboardPage searchOrganisation() {
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		searchBtn.click();
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}
	
	public BusinessConfirmationPage selectOrganisation() {
		organisationLink.click();
		return PageFactory.initElements(driver, BusinessConfirmationPage.class);
	}
}
