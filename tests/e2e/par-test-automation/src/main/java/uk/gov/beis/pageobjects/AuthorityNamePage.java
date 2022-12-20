package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AuthorityNamePage extends BasePageObject {

	public AuthorityNamePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-name")
	WebElement authorityName;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public BasePageObject enterAuthorityName(String name) {
		authorityName.clear();
		authorityName.sendKeys(name);
		try {
			driver.findElement(By.id("edit-next")).click();
			return PageFactory.initElements(driver, AuthorityTypePage.class);
		} catch (Exception e) {
			driver.findElement(By.id("edit-save")).click();
			return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
		}
	}

}
