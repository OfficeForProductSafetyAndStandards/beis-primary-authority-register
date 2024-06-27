package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class PasswordPage extends BasePageObject {
	
	//@FindBy(id = "edit-pass-pass1")
	@FindBy(xpath = "//input[@name='pass[pass1]']")
	private WebElement passwordField;

	//@FindBy(id = "edit-pass-pass2")
	@FindBy(xpath = "//input[@name='pass[pass2]']")
	private WebElement confirmPasswordField;
	
	@FindBy(id = "edit-next")
	private WebElement register;
	
	@FindBy(xpath = "//button[@class='hide-button']")
	private WebElement drupalToolBarHideButton;
	
	public PasswordPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterPassword(String password, String confirm) {
		passwordField.sendKeys(password);
		confirmPasswordField.sendKeys(confirm);
	}

	public void clickRegisterButton() {
		register.click();
	}
	
	public void clickDrupalHideButton() {
		drupalToolBarHideButton.click();
	}
}
