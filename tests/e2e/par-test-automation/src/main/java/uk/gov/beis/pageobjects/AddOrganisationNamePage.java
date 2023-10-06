package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AddOrganisationNamePage extends BasePageObject {

	@FindBy(id = "edit-name")
	private WebElement organisationName;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public AddOrganisationNamePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public AuthorityAddressDetailsPage enterMemberOrganisationName(String name) {
		organisationName.clear();
		organisationName.sendKeys(name);
		
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityAddressDetailsPage.class);
	}
}
