package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class MemberListUploadedPage extends BasePageObject{
	
	@FindBy(id = "edit-done")
	private WebElement doneBtn;
	
	public MemberListUploadedPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public MemberListPage selectDone() {
		doneBtn.click();
		return PageFactory.initElements(driver, MemberListPage.class);
	}
}
