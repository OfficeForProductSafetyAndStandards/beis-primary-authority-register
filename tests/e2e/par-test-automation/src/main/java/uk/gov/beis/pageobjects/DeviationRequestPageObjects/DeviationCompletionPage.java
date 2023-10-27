package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipInformationPage;

public class DeviationCompletionPage extends BasePageObject{
	
	@FindBy(xpath = "//a[contains(@class,'button')]")
	private WebElement doneBtn;
	
	public DeviationCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipInformationPage complete() {
		doneBtn.click();
		return PageFactory.initElements(driver, PartnershipInformationPage.class);
	}
}