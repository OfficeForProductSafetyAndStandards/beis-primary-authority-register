package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class UserProfilePage extends BasePageObject {
	public UserProfilePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(className = "govuk-radios__item")
	private List<WebElement> userContacts;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	// Each new Contacts Web Element ID is incremented by one.
	// Only need to find the Radio button with the name of the new contact.
	public void selectContactToUpdate(String name) {
		for(WebElement contact : userContacts) {
			if(contact.getText().contains(name)) {
				WebElement radioButton = contact.findElement(By.name("user_person"));
				radioButton.click();
			}
		}
	}
	
	public UpdateUserContactDetailsPage selectContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, UpdateUserContactDetailsPage.class);
	}
	
	public DashboardPage selectCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
