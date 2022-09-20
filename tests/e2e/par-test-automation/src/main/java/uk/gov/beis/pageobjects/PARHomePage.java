package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.Keys;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;

public class PARHomePage extends BasePageObject {

	public PARHomePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(linkText = "Sign in")
	private WebElement signinButton;
	
	public PARHomePage navigateToUrl() {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("par_url"));
		return PageFactory.initElements(driver, PARHomePage.class);
	}
	
	public PARLoginPage selectLogin() {
		signinButton.click();
		return PageFactory.initElements(driver, PARLoginPage.class);
	}


}
