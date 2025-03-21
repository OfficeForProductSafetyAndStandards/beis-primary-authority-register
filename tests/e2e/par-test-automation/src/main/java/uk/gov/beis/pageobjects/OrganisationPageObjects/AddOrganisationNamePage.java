package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

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
	
	public void enterMemberName(String name) {
		organisationName.clear();
		organisationName.sendKeys(name);
	}
	
	public void clearOrganisationNameField() {
		organisationName.clear();
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
}
