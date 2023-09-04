package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class RequestDeviationPage extends BasePageObject{

	public RequestDeviationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	private WebElement continueBtn;

	@FindBy(xpath = "//input[@id='edit-files-upload']")
	private WebElement chooseFile1;


	public RequestDeviationPage chooseFile(String filename) {
		chooseFile1.sendKeys(System.getProperty("user.dir") + "/" + filename);
		return PageFactory.initElements(driver, RequestDeviationPage.class);
	}

	@FindBy(xpath = "//div[@class='govuk-form-group']/textarea")
	WebElement descriptionBox;

	public RequestDeviationPage enterDescription(String description) throws Throwable {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
		return PageFactory.initElements(driver, RequestDeviationPage.class);
	}

	public DeviationReviewPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, DeviationReviewPage.class);
	}
}