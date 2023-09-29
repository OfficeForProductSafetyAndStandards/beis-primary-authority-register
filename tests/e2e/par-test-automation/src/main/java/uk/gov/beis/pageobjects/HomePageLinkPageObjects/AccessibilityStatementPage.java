package uk.gov.beis.pageobjects.HomePageLinkPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AccessibilityStatementPage extends BasePageObject {

	@FindBy(xpath = "//h1[contains(text(),'Primary Authority Register: accessibility statement')]")
	private WebElement accessibilityStatementHeader;
	
	public AccessibilityStatementPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderDisplayed() {
		return accessibilityStatementHeader.isDisplayed();
	}
}
