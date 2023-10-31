package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DeclarationPage;

public class PasswordPage extends BasePageObject {
	
	@FindBy(id = "edit-pass-pass1")
	private WebElement passwordField;

	@FindBy(id = "edit-pass-pass2")
	private WebElement confirmPasswordField;
	
	@FindBy(id = "edit-next")
	private WebElement register;
	
	public PasswordPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PasswordPage enterPassword(String password, String confirm) {
		passwordField.sendKeys(password);
		confirmPasswordField.sendKeys(confirm);
		return PageFactory.initElements(driver, PasswordPage.class);
	}

	public DeclarationPage selectRegister() {
		register.click();
		return PageFactory.initElements(driver, DeclarationPage.class);
	}

}
