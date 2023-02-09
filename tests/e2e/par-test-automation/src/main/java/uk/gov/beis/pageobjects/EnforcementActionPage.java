package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class EnforcementActionPage extends BasePageObject {

	public EnforcementActionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	@FindBy(xpath = "//input[@id='edit-par-component-enforcement-action-0-files-upload']")
	WebElement chooseFile1;

	@FindBy(id = "edit-par-component-enforcement-action-0-title")
	WebElement title;

	private String locator = "//label[contains(text(),'?')]";

	public EnforcementDetailsPage selectRegFunc(String func) {
		driver.findElement(By.xpath(locator.replace("?", func))).click();
		return PageFactory.initElements(driver, EnforcementDetailsPage.class);
	}

	public EnforcementActionPage enterTitle(String val) {
		title.sendKeys(val);
		return PageFactory.initElements(driver, EnforcementActionPage.class);
	}

	public EnforcementActionPage chooseFile(String filename) {
		chooseFile1.sendKeys(System.getProperty("user.dir") + "/" + filename);
		return PageFactory.initElements(driver, EnforcementActionPage.class);
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	public EnforcementActionPage enterEnforcementDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, EnforcementActionPage.class);
	}

	public EnforcementReviewPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementReviewPage.class);
	}
}
