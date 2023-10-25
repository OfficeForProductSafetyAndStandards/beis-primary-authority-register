package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class OrganisationsSearchPage extends BasePageObject {
	
	@FindBy(id = "edit-name-search")
	private WebElement searchInput;
	
	@FindBy(id = "edit-submit-helpdesk-organisations")
	private WebElement searchBtn;
	
	@FindBy(linkText = "Manage organisation")
	private WebElement organisationLink;
	
	public OrganisationsSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void searchOrganisation() {
		searchInput.sendKeys(DataStore.getSavedValue(UsableValues.BUSINESS_NAME));
		searchBtn.click();
	}
	
	public BusinessDetailsPage selectOrganisation() {
		organisationLink.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
}
