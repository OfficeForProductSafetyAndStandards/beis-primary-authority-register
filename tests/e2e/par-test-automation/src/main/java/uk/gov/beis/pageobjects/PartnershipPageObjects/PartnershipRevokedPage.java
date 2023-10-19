package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipRevokedPage extends BasePageObject {

	public PartnershipRevokedPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-done")
	WebElement doneBtn;

	public PartnershipAdvancedSearchPage completeApplication() {
		if (doneBtn.isDisplayed()) {
			doneBtn.click();
		}
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}
}
