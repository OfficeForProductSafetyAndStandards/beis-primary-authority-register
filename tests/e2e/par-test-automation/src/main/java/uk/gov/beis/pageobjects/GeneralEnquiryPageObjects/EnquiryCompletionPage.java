package uk.gov.beis.pageobjects.GeneralEnquiryPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class EnquiryCompletionPage extends BasePageObject {

	@FindBy(xpath = "//a[contains(@class,'button')]")
	private WebElement doneBtn;
	
	public EnquiryCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void clickDoneButton() {
		doneBtn.click();
	}
}