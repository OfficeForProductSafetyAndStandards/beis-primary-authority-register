package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ConfirmMemberUploadPage extends BasePageObject {
	
	@FindBy(id = "edit-save")
	private WebElement uploadBtn;
	
	public ConfirmMemberUploadPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public MemberListUploadedPage selectUpload() {
		uploadBtn.click();
		return PageFactory.initElements(driver, MemberListUploadedPage.class);
	}
}
