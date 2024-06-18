package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ProfileCompletionPage extends BasePageObject {

	@FindBy(xpath = "//a[contains(@class,'button')]")
	private WebElement doneBtn;
	
	public ProfileCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void clickDoneButton() {
		doneBtn.click();
	}
}