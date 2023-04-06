package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class DeviationCompletionPage extends BasePageObject{
	
	public DeviationCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//a[contains(@class,'button')]")
	WebElement doneBtn;

	public PartnershipConfirmationPage complete() {
			doneBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}