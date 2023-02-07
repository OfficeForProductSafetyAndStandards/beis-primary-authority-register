package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class RemoveEnforcementPage extends BasePageObject {

	public RemoveEnforcementPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	private String locator = "//label[contains(text(),'?')]";

	public RemoveEnforcementPage selectRevokeReason(String reason) {
		driver.findElement(By.xpath(locator.replace("?", reason))).click();
		return PageFactory.initElements(driver, RemoveEnforcementPage.class);
	}
	
	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	public RemoveEnforcementConfirmationPage enterRevokeDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		continueBtn.click();

		return PageFactory.initElements(driver, RemoveEnforcementConfirmationPage.class);
	}

}
