package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipRestoredPage extends BasePageObject {
	
	@FindBy(id = "edit-done")
	private WebElement doneBtn;
	
	public PartnershipRestoredPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipAdvancedSearchPage goToAdvancedPartnershipSearchPage() {
			doneBtn.click();
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}
}
