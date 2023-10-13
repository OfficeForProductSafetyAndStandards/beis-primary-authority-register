package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.AuthorityPageObjects.AuthorityAddressDetailsPage;

public class AddOrganisationNamePage extends BasePageObject {

	@FindBy(id = "edit-name")
	private WebElement organisationName;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public AddOrganisationNamePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public AuthorityAddressDetailsPage enterMemberOrganisationName(String name) {
		organisationName.clear();
		organisationName.sendKeys(name);
		
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityAddressDetailsPage.class);
	}
	
	public MemberOrganisationSummaryPage editMemberOrganisationName(String name) {
		organisationName.clear();
		organisationName.sendKeys(name);
		
		saveBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
}
