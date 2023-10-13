package uk.gov.beis.pageobjects.GeneralEnquiryPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class EnquiryCompletionPage extends BasePageObject {

	@FindBy(xpath = "//a[contains(@class,'button')]")
	private WebElement doneBtn;
	
	public EnquiryCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public PartnershipConfirmationPage complete() {
		doneBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}