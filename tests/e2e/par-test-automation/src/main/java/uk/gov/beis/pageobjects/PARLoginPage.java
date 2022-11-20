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

public class PARLoginPage extends BasePageObject {

	public PARLoginPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(name = "name")
	private WebElement username;

	@FindBy(name = "pass")
	private WebElement password;

	@FindBy(name = "op")
	private WebElement loginBtn;

	public PARLoginPage navigateToUrl() {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("par_url") + "/user/login%3Fcurrent");
		checkLoginPage();
		return PageFactory.initElements(driver, PARLoginPage.class);
	}

	public PARLoginPage enterLoginDetails(String user, String pass) {
		username.sendKeys(user);
		password.sendKeys(pass);
		return PageFactory.initElements(driver, PARLoginPage.class);
	}

	public PARDashboardPage selectLogin() {
		loginBtn.click();
		return PageFactory.initElements(driver, PARDashboardPage.class);
	}

	private String login = "//a[contains(text(),'?')]";

	public PARLoginPage checkLoginPage() {
		try {
		driver.findElement(By.xpath(login.replace("?", "Sign in")));
//		if (!link.isDisplayed()) {
			driver.findElement(By.linkText("Sign out")).click();
			driver.findElement(By.linkText("Sign in")).click();
		} catch (NoSuchElementException e) {
			driver.findElement(By.linkText("Sign out")).click();
			driver.findElement(By.linkText("Sign in")).click();
		}
		return PageFactory.initElements(driver, PARLoginPage.class);
	}
	
	@FindBy(xpath = "//button[contains(text(),'Accept')]")
	private WebElement cookies;
	
	public PARLoginPage checkAndAcceptCookies() {
		driver.manage().deleteAllCookies();
		try {
//		if (cookies.isDisplayed()) {
//			System.exit(0);
			driver.findElement(By.xpath("//button[contains(text(),'Accept')]")).click();
//			cookies.click();
		} catch (NoSuchElementException e) {
			// do nothing
			System.out.println("Doing nothing");
		}
		return PageFactory.initElements(driver, PARLoginPage.class);
	}

}
