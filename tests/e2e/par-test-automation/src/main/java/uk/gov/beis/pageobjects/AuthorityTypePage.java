package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AuthorityTypePage extends BasePageObject {

	public AuthorityTypePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	private String locator = "//label[contains(text(),'?')]";

	public BasePageObject selectAuthorityType(String type) {
		WebElement link = driver.findElement(By.xpath(locator.replace("?", type)));
		link.click();
		try {
			driver.findElement(By.id("edit-next")).click();
			return PageFactory.initElements(driver, AuthorityAddressDetailsPage.class);
		} catch (Exception e) {
			driver.findElement(By.id("edit-save")).click();
			return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
		}
	}
}