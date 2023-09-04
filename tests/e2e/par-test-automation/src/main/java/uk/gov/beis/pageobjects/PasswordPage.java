package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PasswordPage extends BasePageObject {
	public PasswordPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-pass-pass1")
	private WebElement password1;

	@FindBy(id = "edit-pass-pass2")
	private WebElement password2;
	
	@FindBy(name = "op")
	private WebElement register;

	public PasswordPage enterPassword(String pass1, String pass2) {
		password1.sendKeys(pass1);
		password2.sendKeys(pass2);
		return PageFactory.initElements(driver, PasswordPage.class);
	}

	public UserTermsPage selectRegister() {
		register.click();
		return PageFactory.initElements(driver, UserTermsPage.class);
	}

}
