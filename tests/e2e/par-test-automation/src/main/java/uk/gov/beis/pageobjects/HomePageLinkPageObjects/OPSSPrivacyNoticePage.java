package uk.gov.beis.pageobjects.HomePageLinkPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class OPSSPrivacyNoticePage extends BasePageObject {

	@FindBy(xpath = "//h1[contains(text(),'OPSS: privacy notice')]")
	private WebElement opssPrivacyNoticeHeader;
	
	public OPSSPrivacyNoticePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderDisplayed() {
		return opssPrivacyNoticeHeader.isDisplayed();
	}
}
