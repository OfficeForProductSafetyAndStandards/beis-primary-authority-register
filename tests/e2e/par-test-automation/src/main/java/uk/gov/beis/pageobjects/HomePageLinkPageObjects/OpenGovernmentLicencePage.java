package uk.gov.beis.pageobjects.HomePageLinkPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class OpenGovernmentLicencePage extends BasePageObject {

	@FindBy(id = "open-licence-logo")
	private WebElement openGovernmentLicenceLogo;
	
	public OpenGovernmentLicencePage () throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderDisplayed() {
		return openGovernmentLicenceLogo.isDisplayed();
	}
}
