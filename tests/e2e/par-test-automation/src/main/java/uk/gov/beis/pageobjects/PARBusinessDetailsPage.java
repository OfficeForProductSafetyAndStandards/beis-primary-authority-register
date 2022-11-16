package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARBusinessDetailsPage extends BasePageObject {

	public PARBusinessDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PARBusinessAddressDetailsPage enterBusinessDescription(String desc) throws Throwable {
		if (descriptionBox.isDisplayed()) {
			descriptionBox.clear();
			descriptionBox.sendKeys(desc);
		}
		if (continueBtn.isDisplayed())
			continueBtn.click();

		return PageFactory.initElements(driver, PARBusinessAddressDetailsPage.class);
	}

	public PARBusinessDetailsPage proceed() {
		if (continueBtn.isDisplayed())
			continueBtn.click();
		return PageFactory.initElements(driver, PARBusinessDetailsPage.class);
	}
}
