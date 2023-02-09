package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class BusinessDetailsPage extends BasePageObject {

	public BusinessDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	public BasePageObject enterBusinessDescription(String desc) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(desc);
		try {
			driver.findElement(By.id("edit-next")).click();
			return PageFactory.initElements(driver, BusinessAddressDetailsPage.class);
		} catch (Exception e) {
			driver.findElement(By.id("edit-save")).click();
			return PageFactory.initElements(driver, BusinessConfirmationPage.class);
		}
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public BusinessDetailsPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
}
