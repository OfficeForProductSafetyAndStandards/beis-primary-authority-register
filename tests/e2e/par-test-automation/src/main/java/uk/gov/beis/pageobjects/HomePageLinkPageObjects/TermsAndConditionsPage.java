package uk.gov.beis.pageobjects.HomePageLinkPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class TermsAndConditionsPage extends BasePageObject {

	@FindBy(xpath = "//h1[contains(text(),'Primary Authority terms and conditions')]")
	private WebElement termsAndConditionsHeader;
	
	public TermsAndConditionsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderDisplayed() {
		return termsAndConditionsHeader.isDisplayed();
	}
}
