package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class NewsLetterManageSubscriptionListPage extends BasePageObject {
	public NewsLetterManageSubscriptionListPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(id = "edit-method-insert")
	private WebElement addEmailRadioBtn;
	
	@FindBy(id = "edit-method-remove")
	private WebElement removeEmailRadioBtn;
	
	@FindBy(id = "edit-method-replace")
	private WebElement replaceSubscriptionListRadioBtn;
	
	@FindBy(id = "edit-emails")
	private WebElement emailsTextArea;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public void selectInsertNewEmailRadioButton() {
		addEmailRadioBtn.click();
	}
	
	public void selectRemoveEmailRadioButton() {
		removeEmailRadioBtn.click();
	}
	
	public void selectReplaceSubscriptionListRadioButton() {
		replaceSubscriptionListRadioBtn.click();
	}
	
	public void AddNewEmail(String email) {
		emailsTextArea.sendKeys(email);
	}
	
	public void RemoveEmail(String email) {
		emailsTextArea.sendKeys(email);
	}
	
	public void ReplaceSubscriptionList(String email) {
		emailsTextArea.sendKeys(email + "\n");
	}
	
	public NewsLetterSubscriptionReviewChangesPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, NewsLetterSubscriptionReviewChangesPage.class);
	}
	
	public NewsLetterSubscriptionPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
	}
}
