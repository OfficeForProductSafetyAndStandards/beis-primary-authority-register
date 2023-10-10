package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class MemberOrganisationSummaryPage extends BasePageObject {
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public MemberOrganisationSummaryPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public MemberOrganisationAddedConfirmationPage selectSave() {
		saveBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationAddedConfirmationPage.class);
	}
}
