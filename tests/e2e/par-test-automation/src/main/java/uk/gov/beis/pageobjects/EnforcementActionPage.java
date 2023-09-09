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

	@FindBy(id = "edit-par-component-enforcement-action-0-title")
	private WebElement title;
	
	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	private WebElement descriptionBox;

	@FindBy(xpath = "//input[@id='edit-par-component-enforcement-action-0-files-upload']")
	private WebElement chooseFile1;
	
	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	private WebElement continueBtn;

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
		uploadDocument(chooseFile1, filename);
		return PageFactory.initElements(driver, EnforcementActionPage.class);
	}

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
