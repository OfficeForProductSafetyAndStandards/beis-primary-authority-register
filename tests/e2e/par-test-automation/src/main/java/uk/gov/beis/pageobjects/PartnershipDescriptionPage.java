package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PartnershipDescriptionPage extends BasePageObject {

	public PartnershipDescriptionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	public BusinessPage enterPartnershipDescription(String description, boolean secondJourney) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		WebElement button = secondJourney ? driver.findElement(By.id("edit-save"))
				: driver.findElement(By.id("edit-next"));
		button.click();

		return PageFactory.initElements(driver, BusinessPage.class);
	}

}
