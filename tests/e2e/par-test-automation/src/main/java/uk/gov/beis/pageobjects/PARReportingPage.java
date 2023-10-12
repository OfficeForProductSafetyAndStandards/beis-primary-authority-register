package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

public class PARReportingPage extends BasePageObject {
	
	@FindBy(xpath = "//div[@id = 'block-par-theme-page-title']/h1[contains(text(), 'PAR Reporting')]")
	private WebElement pageHeader;
	
	public PARReportingPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderIsDisplayed() {
		return pageHeader.isDisplayed();
	}
}
