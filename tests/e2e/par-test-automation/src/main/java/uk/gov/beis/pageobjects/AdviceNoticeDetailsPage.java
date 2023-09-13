package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AdviceNoticeDetailsPage extends BasePageObject {

	public AdviceNoticeDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	@FindBy(id = "edit-advice-title")
	WebElement title;

	private String advicetype = "//label[contains(text(),'?')]";
	private String regfunc = "//label[contains(text(),'?')]";

	public AdviceNoticeDetailsPage selectAdviceType(String type) {
		driver.findElement(By.xpath(advicetype.replace("?", type))).click();
		return PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
	}

	public AdviceNoticeDetailsPage selectRegFunc(String func) {
		driver.findElement(By.xpath(regfunc.replace("?", func))).click();
		return PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
	}

	public AdviceNoticeDetailsPage enterTitle(String value) {
		title.clear();
		title.sendKeys(value);
		return PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
	}

	public AdviceNoticeDetailsPage enterDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
	}

	public InspectionPlanExpirationPage save() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionPlanExpirationPage.class);
	}

}
