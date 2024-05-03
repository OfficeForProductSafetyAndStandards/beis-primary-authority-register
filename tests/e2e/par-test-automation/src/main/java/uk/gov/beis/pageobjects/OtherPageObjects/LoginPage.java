package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.BasePageObject;
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
	
	public void navigateToUrl() {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("par_url") + "/user/login%3Fcurrent");
		checkLoginPage();
	}
	
	public void navigateToInviteLink() throws InterruptedException {
		ScenarioContext.lastDriver.get(DataStore.getSavedValue(UsableValues.INVITE_LINK));
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
	
	public void selectSignIn() {
		loginBtn.click();
	}
	
	public void checkLoginPage() {
		try {
			driver.findElement(By.xpath(login.replace("?", "Sign in")));
			LOG.info("user is signed out");
		} catch (NoSuchElementException e) {
			driver.findElement(By.linkText("Sign out")).click();
			driver.findElement(By.linkText("Sign in")).click();
		}
	}
}
