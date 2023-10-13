package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DeviationRequestPageObjects.RequestDeviationPage;

public class EnforcementContactDetailsPage extends BasePageObject {
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public EnforcementContactDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}

	public EnforcementLegalEntityPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementLegalEntityPage.class);
	}
	
	public RequestDeviationPage save() {
		continueBtn.click();
		return PageFactory.initElements(driver, RequestDeviationPage.class);
	}
}
