package uk.gov.beis.pageobjects.NewsLetterSubscriptionPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

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
		generateNewEmailList();
	}
	
	private void generateNewEmailList() {
		emailsTextArea.clear();
		String number = "";
		int id = 0;
		
		// Get the number from the Last created Email
		String lastEmail = DataStore.getSavedValue(UsableValues.LAST_PAR_NEWS_EMAIL);
		
		char[] chars = lastEmail.toCharArray(); 
		for(char c : chars) {
			if(Character.isDigit(c)) {
				number += c;
			}
		}
		
		id = Integer.parseInt(number);
		
		// Create the new emails
		for(int i = 0; i < 4; i++) {
			emailsTextArea.sendKeys("user@newsletter" + (id + 1) + ".com" + "\n");
			id++;
		}
	}
	
	public void AddNewEmail(String email) {
		emailsTextArea.clear();
		emailsTextArea.sendKeys(email);
	}
	
	public void RemoveEmail(String email) {
		emailsTextArea.sendKeys(email);
	}
	
	public NewsLetterSubscriptionReviewPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, NewsLetterSubscriptionReviewPage.class);
	}
	
	public NewsLetterSubscriptionPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, NewsLetterSubscriptionPage.class);
	}
}
