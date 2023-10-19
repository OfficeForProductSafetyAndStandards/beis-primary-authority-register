package uk.gov.beis.pageobjects.HomePageLinkPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class LocalRegulationPrimaryAuthorityPage extends BasePageObject {

	@FindBy(xpath = "//h1[contains(text(),'Local regulation: Primary Authority')]")
	private WebElement localRegulationPageHeader;
	
	public LocalRegulationPrimaryAuthorityPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderDisplayed() {
		return localRegulationPageHeader.isDisplayed();
	}
}
