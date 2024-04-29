package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipInformationPage;
import uk.gov.beis.pageobjects.UserDashboardPageObjects.DashboardPage;

public class AmendmentCompletedPage extends BasePageObject {
	
	@FindBy(xpath = "//a[contains(normalize-space(), 'Done')]")
	private WebElement doneBtn;
	
	public AmendmentCompletedPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipInformationPage goToPartnershipDetailsPage() {
		doneBtn.click();
		
		return PageFactory.initElements(driver, PartnershipInformationPage.class);
	}
	
	public DashboardPage goToDashBoardPage() {
		doneBtn.click();
		
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
