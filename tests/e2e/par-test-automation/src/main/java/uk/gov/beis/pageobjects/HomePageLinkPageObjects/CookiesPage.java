package uk.gov.beis.pageobjects.HomePageLinkPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class CookiesPage extends BasePageObject {

	@FindBy(xpath = "//span[contains(text(),'Primary Authority Register')]")
	private WebElement primaryAuthorityRegisterHeader;
	
	@FindBy(xpath = "//h1/span[contains(text(),'Cookies')]")
	private WebElement cookiesPageHeader;
	
	public CookiesPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderDisplayed() {
		return cookiesPageHeader.isDisplayed();
	}
	
	public void selectPARHeader() {
		primaryAuthorityRegisterHeader.click();
	}
}
