package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DashboardPage;

public class PartnershipCompletionPage extends BasePageObject {

	public PartnershipCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//a[contains(@class,'button')]")
	WebElement doneBtn;

	public DashboardPage completeApplication() {
			doneBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
