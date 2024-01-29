package uk.gov.beis.pageobjects;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.helper.ScenarioContext;

public class WebsiteManager {
	
	public WebDriver driver;
	
	// Home page
	public HomePage homePage;
	
	// Login
	public LoginPage loginPage;
	
	public WebsiteManager() {
		
		driver = ScenarioContext.lastDriver;
		
		// PAR Home Page
		homePage = PageFactory.initElements(driver, HomePage.class);
		
		// Login
		loginPage = PageFactory.initElements(driver, LoginPage.class);
	}
}
