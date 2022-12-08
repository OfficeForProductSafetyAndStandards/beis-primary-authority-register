package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PartnershipApprovalPage extends BasePageObject {

	public PartnershipApprovalPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-done")
	WebElement doneBtn;

	public PartnershipAdvancedSearchPage completeApplication() {
		doneBtn.click();
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}
}
