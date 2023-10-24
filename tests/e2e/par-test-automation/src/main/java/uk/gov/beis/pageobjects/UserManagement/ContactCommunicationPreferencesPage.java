package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ContactCommunicationPreferencesPage extends BasePageObject {
	
	@FindBy(id = "edit-preferred-contact-communication-email")
	private WebElement emailCommunicationCheckbox;
	
	@FindBy(id = "edit-preferred-contact-communication-phone")
	private WebElement workPhoneCommunicationCheckbox;
	
	@FindBy(id = "edit-preferred-contact-communication-mobile")
	private WebElement mobilePhoneCommunicationCheckbox;
	
	@FindBy(id = "edit-notes")
	private WebElement contactNoteTextArea;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public ContactCommunicationPreferencesPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectEmailCommunicationPreference() {
		emailCommunicationCheckbox.click();
	}
	
	public void selectWorkPhoneCommunicationPreference() {
		workPhoneCommunicationCheckbox.click();
	}
	
	public void selectMobilePhoneCommunicationPreference() {
		mobilePhoneCommunicationCheckbox.click();
	}
	
	public void enterContactNote(String note) {
		contactNoteTextArea.sendKeys(note);
	}
	
	public ContactUpdateSubscriptionPage selectContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, ContactUpdateSubscriptionPage.class);
	}
}
