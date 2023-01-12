package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class EnforcementDetailsPage extends BasePageObject {

	public EnforcementDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;
	
	private String locator = "//label[contains(text(),'?')]";

	public EnforcementDetailsPage selectEnforcementType(String type) {
		driver.findElement(By.xpath(locator.replace("?", type))).click();;
		return PageFactory.initElements(driver, EnforcementDetailsPage.class);
	}
	
	public EnforcementDetailsPage enterEnforcementDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, EnforcementDetailsPage.class);
	}
	public EnforcementActionPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementActionPage.class);
	}
}
