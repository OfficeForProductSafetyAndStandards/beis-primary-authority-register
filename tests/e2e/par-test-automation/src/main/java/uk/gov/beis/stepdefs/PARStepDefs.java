package uk.gov.beis.stepdefs;

import java.io.IOException;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.support.PageFactory;

import cucumber.api.PendingException;
import cucumber.api.java.en.Given;
import cucumber.api.java.en.Then;
import uk.gov.beis.helper.LOG;
import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.PARDashboardPage;
import uk.gov.beis.pageobjects.PARHomePage;
import uk.gov.beis.pageobjects.PARLoginPage;

public class PARStepDefs {
	
	public static WebDriver driver;
	private PARHomePage parHomePage;
	private PARLoginPage parLoginPage;
	private PARDashboardPage parDashboardPage;


	public PARStepDefs() throws ClassNotFoundException, IOException {
		driver = ScenarioContext.lastDriver;
		parHomePage = PageFactory.initElements(driver, PARHomePage.class);
		parLoginPage = PageFactory.initElements(driver, PARLoginPage.class);
		parDashboardPage = PageFactory.initElements(driver, PARDashboardPage.class);

	}
	
	@Given("^the user is on the PAR home page$")
	public void the_user_is_on_the_PAR_home_page() throws Throwable {
		LOG.info("Navigating to PAR Home page");
		parHomePage.navigateToUrl();
	}

	@Given("^the user wants to login$")
	public void the_user_wants_to_login() throws Throwable {
		parHomePage.selectLogin();
	}
	
	@Given("^the user logs in with the \"([^\"]*)\" user credentials$")
	public void the_user_logs_in_with_the_user_credentials(String user) throws Throwable {
		String pass = PropertiesUtil.getConfigPropertyValue(user);
		parLoginPage.enterLoginDetails(user, pass);
		parLoginPage.selectLogin();
	}

	@Then("^the user is on the dashboard page$")
	public void the_user_is_on_the_dashboard_page() throws Throwable {
	    
	}

}
