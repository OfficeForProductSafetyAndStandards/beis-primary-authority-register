package uk.gov.beis.pageobjects.NewsLetterSubscriptionPageObjects;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DashboardPage;
import uk.gov.beis.utility.DataStore;

public class NewsLetterSubscriptionPage extends BasePageObject {
	public NewsLetterSubscriptionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(partialLinkText = "PAR News")
	private WebElement manageSubscriptionsBtn;
	
	@FindBy(id = "edit-verified")
	private WebElement verifiedDropDownBox;
	
	@FindBy(id = "edit-email")
	private WebElement emailTextField;
	
	@FindBy(id = "edit-submit-subscriptions")
	private WebElement searchBtn;
	
	@FindBy(xpath = "//td[@class='views-field views-field-email']")
	private List<WebElement> emailListElements;
	
	@FindBy(xpath = "//td[@class='views-field views-field-email']")
	private WebElement subscriptionListEmailElement;
	
	@FindBy(xpath = "//td[@class='views-field views-field-code']")
	private WebElement subscriptionListCodeElement;
	
	@FindBy(xpath = "//td[@class='priority-medium views-field views-field-verified']")
	private WebElement subscriptionListVerifyElement;
	
	@FindBy(linkText = "back to dashboard")
	private WebElement dashboardBtn;
	
	public NewsLetterManageSubscriptionListPage selectManageSubsciptions() {
		getLastEmailFromList();
		manageSubscriptionsBtn.click();
		return PageFactory.initElements(driver, NewsLetterManageSubscriptionListPage.class);
	}
	
	public void EnterEmail(String email) {
		emailTextField.sendKeys(email);
	}
	
	public void ClickSearchButton() {
		searchBtn.click();
	}
	
	public String GetEmailAddress() {
		return subscriptionListEmailElement.getText();
	}
	
	public String GetEmailCode() {
		return subscriptionListCodeElement.getText();
	}
	
	public String GetEmailVerified() {
		return subscriptionListVerifyElement.getText();
	}
	
	public Boolean verifyTableElementExists() {
		return driver.findElement(By.xpath("//td[@class='views-field views-field-email']")).isDisplayed();
	}
	
	public Boolean verifyTableElementIsNull() {
		// Cannot use the .isDisplayed() method as the table element is null and cannot be found.
		return driver.findElements(By.xpath("//td[@class='views-field views-field-email']")).size() == 0;
	}
	
	private void getLastEmailFromList() {
		int currentNumber = 0;
		int highestNumber = 0;
		String emailAddress = "";
		
		// Scrub through the New Letter Subscription List to find the correct email format, example: uxxxxxxr@newsletter5.com
		for(WebElement element : emailListElements) {
			if(element.getText().contains("@newsletter")) {
				// Find the Highest number
				String number = "";
				char[] chars = element.getText().toCharArray(); 
				
				for(char c : chars) {
					if(Character.isDigit(c)) {
						number += c;
					}
				}
				
				currentNumber = Integer.parseInt(number);
				
				// Check for the highest number
				if(currentNumber > highestNumber) {
					highestNumber = currentNumber;
					
					// Final stage and include the Empty check
					emailAddress = element.getText();
				}
			}
		}
		
		// First Time Run: If no email is found with the @newsletter format.
		if(highestNumber == 0) {
			emailAddress = "user@newsletter0.com";
		}
		
		DataStore.saveValue(UsableValues.LAST_PAR_NEWS_EMAIL, emailAddress);
	}
	
	public DashboardPage selectBackToDashboard() {
		dashboardBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
