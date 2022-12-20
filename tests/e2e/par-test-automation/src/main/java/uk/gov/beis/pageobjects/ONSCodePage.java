package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class ONSCodePage extends BasePageObject {

	public ONSCodePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-ons-code")
	WebElement onsCode;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public BasePageObject enterONSCode(String name) {
		onsCode.clear();
		onsCode.sendKeys(name);
		try {
			driver.findElement(By.id("edit-next")).click();
			return PageFactory.initElements(driver, RegulatoryFunctionPage.class);
		} catch (Exception e) {
			driver.findElement(By.id("edit-save")).click();
			return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
		}
	}
}
