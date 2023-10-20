package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipConfirmationPage;

public class EnforcementCompletionPage extends BasePageObject{

	@FindBy(xpath = "//a[contains(@class,'button')]")
	private WebElement doneBtn;
	
	public EnforcementCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipConfirmationPage goToPartnershipConfirmationPage() {
		doneBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public EnforcementSearchPage clickDone() {
		doneBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
}
