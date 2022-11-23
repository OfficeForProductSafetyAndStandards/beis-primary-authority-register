package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.jar.Attributes.Name;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;

public class LoginPage extends BasePageObject {

	public LoginPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(name = "name")
	private WebElement username;

	@FindBy(name = "pass")
	private WebElement password;

	@FindBy(name = "op")
	private WebElement loginBtn;

	public LoginPage navigateToUrl() {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("par_url") + "/user/login%3Fcurrent");
		checkLoginPage();
		return PageFactory.initElements(driver, LoginPage.class);
	}

	public LoginPage enterLoginDetails(String user, String pass) {
		username.sendKeys(user);
		password.sendKeys(pass);
		return PageFactory.initElements(driver, LoginPage.class);
	}

	public DashboardPage selectLogin() {
		loginBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}

	private String login = "//a[contains(text(),'?')]";

	public LoginPage checkLoginPage() {
		try {
			driver.findElement(By.xpath(login.replace("?", "Sign in")));
			driver.findElement(By.linkText("Sign out")).click();
			driver.findElement(By.linkText("Sign in")).click();
		} catch (NoSuchElementException e) {
			driver.findElement(By.linkText("Sign out")).click();
			driver.findElement(By.linkText("Sign in")).click();
		}
		return PageFactory.initElements(driver, LoginPage.class);
	}

	@FindBy(xpath = "//button[contains(text(),'Accept')]")
	private WebElement cookies;

	public LoginPage checkAndAcceptCookies() {
		driver.manage().deleteAllCookies();
		try {
			driver.findElement(By.xpath("//button[contains(text(),'Accept')]")).click();
		} catch (NoSuchElementException e) {
			// do nothing
		}
		return PageFactory.initElements(driver, LoginPage.class);
	}

}
