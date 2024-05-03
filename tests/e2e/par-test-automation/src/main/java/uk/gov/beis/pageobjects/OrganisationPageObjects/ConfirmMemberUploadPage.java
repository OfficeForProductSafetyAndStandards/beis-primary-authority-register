package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ConfirmMemberUploadPage extends BasePageObject {
	
	@FindBy(id = "edit-save")
	private WebElement uploadBtn;
	
	public ConfirmMemberUploadPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectUploadButton() {
		uploadBtn.click();
	}
}
