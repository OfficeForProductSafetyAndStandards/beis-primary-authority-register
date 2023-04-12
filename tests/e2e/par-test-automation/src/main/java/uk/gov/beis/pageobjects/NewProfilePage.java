package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class NewProfilePage extends BasePageObject {
	public NewProfilePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(linkText = "Done")
	private WebElement doneBtn;
	
	public ManageColleaguesPage clickDoneButton() {
		doneBtn.click();
		return PageFactory.initElements(driver, ManageColleaguesPage.class);
	}
}
