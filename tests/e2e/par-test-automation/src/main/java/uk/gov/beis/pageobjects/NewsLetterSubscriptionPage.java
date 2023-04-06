package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

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
	private WebElement subscriptionListEmailElement;
	
	@FindBy(xpath = "//td[@class='views-field views-field-code']")
	private WebElement subscriptionListCodeElement;
	
	@FindBy(xpath = "//td[@class='priority-medium views-field views-field-verified']")
	private WebElement subscriptionListVerifyElement;
	
	@FindBy(linkText = "back to dashboard")
	private WebElement dashboardBtn;
	
	public NewsLetterManageSubscriptionListPage selectManageSubsciptions() {
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
		return driver.findElements(By.xpath("//td[@class='views-field views-field-email']")).size() == 1;
	}
	
	public Boolean verifyTableElementIsNull() {
		return driver.findElements(By.xpath("//td[@class='views-field views-field-email']")).size() == 0;
	}
	
	public DashboardPage selectBackToDashboard() {
		dashboardBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
