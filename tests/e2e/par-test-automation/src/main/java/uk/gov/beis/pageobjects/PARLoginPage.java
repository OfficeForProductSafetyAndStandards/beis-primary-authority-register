package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.jar.Attributes.Name;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

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
	
	public PARLoginPage enterLoginDetails(String user, String pass) {
		username.sendKeys(user);
		password.sendKeys(pass);
		return PageFactory.initElements(driver, PARLoginPage.class);
	}
	
	public PARDashboardPage selectLogin() {
		loginBtn.click();
		return PageFactory.initElements(driver, PARDashboardPage.class);
	}

}
