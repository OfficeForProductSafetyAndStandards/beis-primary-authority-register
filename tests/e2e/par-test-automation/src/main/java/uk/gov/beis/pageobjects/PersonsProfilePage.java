package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PersonsProfilePage extends BasePageObject {
	public PersonsProfilePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(partialLinkText = "Update")
	private WebElement updateUserBtn;
	
	@FindBy(linkText = "Done")
	private WebElement doneBtn;
	
	public UpdateUserContactDetailsPage clickUpdateUserButton() {
		updateUserBtn.click();
		return PageFactory.initElements(driver, UpdateUserContactDetailsPage.class);
	}
	
	public ManagePeoplePage clickDoneButton() {
		doneBtn.click();
		return PageFactory.initElements(driver, ManagePeoplePage.class);
	}
}
