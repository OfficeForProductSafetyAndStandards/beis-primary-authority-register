package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class UpdateUserCommunicationPreferencesPage extends BasePageObject {
	public UpdateUserCommunicationPreferencesPage() throws ClassNotFoundException, IOException {
		super();
	}
	
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
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
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
	
	public UpdateUserSubscriptionsPage selectContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, UpdateUserSubscriptionsPage.class);
	}
	
	public DashboardPage selectCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
