package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class DeviationCompletionPage extends BasePageObject{
	
	@FindBy(xpath = "//a[contains(@class,'button')]")
	//@FindBy(xpath = "//a[@class='flow-link button']")
	//@FindBy(partialLinkText = "Done")
	private WebElement doneBtn;
	
	public DeviationCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipConfirmationPage complete() {
		doneBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}