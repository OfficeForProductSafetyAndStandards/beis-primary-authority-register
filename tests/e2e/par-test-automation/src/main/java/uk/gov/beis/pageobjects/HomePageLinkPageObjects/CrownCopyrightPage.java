package uk.gov.beis.pageobjects.HomePageLinkPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class CrownCopyrightPage extends BasePageObject {

	@FindBy(xpath = "//h1[contains(text(),'Crown copyright')]")
	private WebElement crownCopyrightHeader;
	
	public CrownCopyrightPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderDisplayed() {
		return crownCopyrightHeader.isDisplayed();
	}
}
