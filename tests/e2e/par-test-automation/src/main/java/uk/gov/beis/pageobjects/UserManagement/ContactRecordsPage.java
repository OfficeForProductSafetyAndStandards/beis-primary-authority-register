package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ContactRecordsPage extends BasePageObject {
	
	@FindBy(name = "user_person")
	private List<WebElement> userContacts;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public ContactRecordsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectContactToUpdate() {
		if(userContacts.size() > 0) {
			userContacts.get(0).click();
		}
	}
	
	public ContactDetailsPage selectContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, ContactDetailsPage.class);
	}
}
