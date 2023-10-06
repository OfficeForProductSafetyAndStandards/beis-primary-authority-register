package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class MemberOrganisationAddedConfirmationPage extends BasePageObject {
	
	@FindBy(xpath = "//a[contains(text(), 'Done')]")
	private WebElement doneBtn;
	
	public MemberOrganisationAddedConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public MemberListPage selectDone() {
		doneBtn.click();
		return PageFactory.initElements(driver, MemberListPage.class);
	}
}
