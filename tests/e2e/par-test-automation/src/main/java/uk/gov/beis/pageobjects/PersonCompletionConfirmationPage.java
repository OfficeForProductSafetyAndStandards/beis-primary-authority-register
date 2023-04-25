package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PersonCompletionConfirmationPage extends BasePageObject {
	public PersonCompletionConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(linkText = "Done")
	private WebElement doneBtn;
	
	public PersonsProfilePage clickDoneButton() {
		doneBtn.click();
		return PageFactory.initElements(driver, PersonsProfilePage.class);
	}
}
