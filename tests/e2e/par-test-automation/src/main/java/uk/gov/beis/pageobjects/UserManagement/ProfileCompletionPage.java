package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DashboardPage;

public class ProfileCompletionPage extends BasePageObject {

	@FindBy(xpath = "//a[contains(@class,'button')]")
	private WebElement doneBtn;
	
	public ProfileCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public DashboardPage goToDashboardPage() {
		doneBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
	
	public PersonsProfilePage goToUserProfilePage() {
		doneBtn.click();
		return PageFactory.initElements(driver, PersonsProfilePage.class);
	}
}