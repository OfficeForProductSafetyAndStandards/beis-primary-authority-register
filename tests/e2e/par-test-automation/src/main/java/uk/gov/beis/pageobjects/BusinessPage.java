package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class BusinessPage extends BasePageObject {

	public BusinessPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[@type='text']")
	WebElement businessName;

	public BasePageObject enterBusinessName(String name) {
		businessName.clear();
		businessName.sendKeys(name);
		try {
			driver.findElement(By.id("edit-next")).click();
			return PageFactory.initElements(driver, BusinessAddressDetailsPage.class);
		} catch (Exception e) {
			driver.findElement(By.id("edit-save")).click();
			return PageFactory.initElements(driver, BusinessConfirmationPage.class);
		}
	}
}
