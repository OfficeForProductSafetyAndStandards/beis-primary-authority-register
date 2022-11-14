package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARPartnershipDescriptionPage extends BasePageObject {

	public PARPartnershipDescriptionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PARBusinessPage enterPartnershipDescription(String description) throws Throwable {
		if (descriptionBox.isDisplayed()) {
			descriptionBox.clear();
			descriptionBox.sendKeys(description);
		}
		if (continueBtn.isDisplayed())
			continueBtn.click();

		return PageFactory.initElements(driver, PARBusinessPage.class);
	}

}
