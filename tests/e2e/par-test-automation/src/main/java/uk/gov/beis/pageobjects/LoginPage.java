package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.UserDashboardPageObjects.*;
import uk.gov.beis.pageobjects.UserManagement.PasswordPage;
import uk.gov.beis.utility.DataStore;

public class LoginPage extends BasePageObject {
	
	@FindBy(id = "edit-name")
	private WebElement emailTextfield;
	
	@FindBy(id = "edit-pass")
	private WebElement passwordTextfield;
	
	@FindBy(id = "edit-submit")
	private WebElement loginBtn;
	
	private String login = "//a[contains(text(),'?')]";
	
	public LoginPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public LoginPage navigateToUrl() throws InterruptedException {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("par_url") + "/user/login%3Fcurrent");
		checkLoginPage();
		return PageFactory.initElements(driver, LoginPage.class);
	}
	
	public void enterEmailAddress(String email) {
		emailTextfield.clear();
		emailTextfield.sendKeys(email);
	}
	
	public void enterPassword(String password) {
		passwordTextfield.clear();
		passwordTextfield.sendKeys(password);
	}
	
	public void enterLoginDetails(String user, String pass) {
		emailTextfield.sendKeys(user);
		passwordTextfield.sendKeys(pass);
	}
	
	public LoginPage selectSignIn() {
		loginBtn.click();
		return PageFactory.initElements(driver, LoginPage.class);
	}

	public BaseDashboardPage clickSignIn() {
		loginBtn.click();
		
		switch(DataStore.getSavedValue(UsableValues.LOGIN_USER)) {
			case ("par_helpdesk@example.com"):
				LOG.info("Help Desk Dashboard.");
				return PageFactory.initElements(driver, HelpDeskDashboardPage.class);
			default:
				return PageFactory.initElements(driver, DashboardPage.class);
		}
	}

	public PasswordPage navigateToInviteLink() throws InterruptedException {
		ScenarioContext.lastDriver.get(DataStore.getSavedValue(UsableValues.INVITE_LINK));
		return PageFactory.initElements(driver, PasswordPage.class);
	}
	
	public HomePage selectLogout() {
		driver.findElement(By.linkText("Sign out")).click();
		return PageFactory.initElements(driver, HomePage.class);
	}
	
	public LoginPage checkLoginPage() {
		try {
			driver.findElement(By.xpath(login.replace("?", "Sign in")));
			LOG.info("user is signed out");
		} catch (NoSuchElementException e) {
			driver.findElement(By.linkText("Sign out")).click();
			driver.findElement(By.linkText("Sign in")).click();
		}
		return PageFactory.initElements(driver, LoginPage.class);
	}
}
