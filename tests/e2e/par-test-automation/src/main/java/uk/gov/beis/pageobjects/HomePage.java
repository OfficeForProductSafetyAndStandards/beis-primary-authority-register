package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.Keys;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;

public class HomePage extends BasePageObject {

	public HomePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(linkText = "Sign in")
	private WebElement signinButton;

	@FindBy(xpath = "//button[contains(text(),'Accept')]")
	private WebElement cookies;
	
	@FindBy(xpath = "//a[contains(text(),'public list')]")
	private WebElement searchPartnershipsLink;
	
	public HomePage navigateToUrl() {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("par_url"));
		return PageFactory.initElements(driver, HomePage.class);
	}

	public HomePage checkAndAcceptCookies() {
		driver.manage().deleteAllCookies();
		try {
			if (cookies.isDisplayed()) {
				cookies.click();
			}
		} catch (Exception e) {
			// do nothing
		}
		return PageFactory.initElements(driver, HomePage.class);
	}

	public LoginPage selectLogin() {
		signinButton.click();
		return PageFactory.initElements(driver, LoginPage.class);
	}
	
	public PartnershipSearchPage selectPartnershipSearchLink() {
		searchPartnershipsLink.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}
}
