package uk.gov.beis.pageobjects.HomePageLinkPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class PrimaryAuthorityDocumentsPage  extends BasePageObject {

	@FindBy(xpath = "//h1[contains(text(),'Primary Authority documents')]")
	private WebElement primaryAuthorityDocumentsHeader;
	
	public PrimaryAuthorityDocumentsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderDisplayed() {
		return primaryAuthorityDocumentsHeader.isDisplayed();
	}
}
