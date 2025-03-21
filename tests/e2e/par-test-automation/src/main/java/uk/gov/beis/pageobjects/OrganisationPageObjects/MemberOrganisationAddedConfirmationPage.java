package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class MemberOrganisationAddedConfirmationPage extends BasePageObject {
	
	@FindBy(xpath = "//a[contains(text(), 'Done')]")
	private WebElement doneBtn;
	
	public MemberOrganisationAddedConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void clickDoneButton() {
		doneBtn.click();
	}
}
