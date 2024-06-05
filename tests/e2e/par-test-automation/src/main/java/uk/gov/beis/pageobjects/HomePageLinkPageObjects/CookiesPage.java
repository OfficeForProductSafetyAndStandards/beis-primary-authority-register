package uk.gov.beis.pageobjects.HomePageLinkPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.OtherPageObjects.HomePage;

public class CookiesPage extends BasePageObject {

	@FindBy(xpath = "//span[contains(text(),'Primary Authority Register')]")
	private WebElement primaryAuthorityRegisterHeader;
	
	@FindBy(xpath = "//h1/span[contains(text(),'Cookies')]")
	private WebElement cookiesPageHeader;
	
	@FindBy(id = "edit-analytics-allow")
	private WebElement acceptCookiesRadial;
	
	@FindBy(id = "edit-analytics-block")
	private WebElement declineCookiesRadial;
	
	@FindBy(id = "edit-save")
	private WebElement saveCookieSettingsBtn;
	
	public CookiesPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkPageHeaderDisplayed() {
		return cookiesPageHeader.isDisplayed();
	}
	
	public void acceptCookies() {
		acceptCookiesRadial.click();
	}
	
	public void declineCookies() {
		declineCookiesRadial.click();
	}
	
	public void selectSaveButton() {
		saveCookieSettingsBtn.click();
	}
	
	public HomePage selectPARHeader() {
		primaryAuthorityRegisterHeader.click();
		return PageFactory.initElements(driver, HomePage.class);
	}
}
