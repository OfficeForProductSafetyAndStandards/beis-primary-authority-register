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

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public BusinessAddressDetailsPage enterBusinessDescription(String desc) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(desc);
		continueBtn.click();

		return PageFactory.initElements(driver, BusinessAddressDetailsPage.class);
	}

	public BusinessDetailsPage proceed() {
//		if (continueBtn.isDisplayed())
		continueBtn.click();
		return PageFactory.initElements(driver, BusinessDetailsPage.class);
	}
}
