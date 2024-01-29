package uk.gov.beis.stepdefs;

import static org.junit.Assert.assertTrue;

import java.io.IOException;

import cucumber.api.java.en.Given;
import cucumber.api.java.en.Then;
import cucumber.api.java.en.When;
import uk.gov.beis.helper.LOG;

import uk.gov.beis.pageobjects.WebsiteManager;

public class SadPathStepDefinitions {
	
	private WebsiteManager websiteManager;
	
	public SadPathStepDefinitions() throws ClassNotFoundException, IOException {
		websiteManager = new WebsiteManager();
	}
	
	@Given("^the user is on the home page$")
	public void the_user_is_on_the_home_page() throws Throwable {
		LOG.info("Navigating to PAR Home page.");
		websiteManager.homePage.navigateToUrl();
	}

	@Given("^the user is on the sign in page$")
	public void the_user_is_on_the_sign_in_page() throws Throwable {
		LOG.info("Navigating to PAR login page - logging out user first if already logged in");
		websiteManager.loginPage.navigateToUrl();
	}
	
	@When("^the user enters the following  and  credentials$")
	public void the_user_enters_the_following_and_credentials() throws Throwable {
		LOG.info("Leaving the email and password fields empty.");
		
		websiteManager.loginPage.enterEmailAddress("");
		websiteManager.loginPage.enterPassword("");
		websiteManager.loginPage.selectSignIn();
	}

	@Then("^the user is shown an error message The Enter your e-mail address is required\\. The Enter your password is required\\. successfully$")
	public void the_user_is_shown_an_error_message_The_Enter_your_e_mail_address_is_required_The_Enter_your_password_is_required_successfully() throws Throwable {
		LOG.info("Validating the error messages.");
		
		assertTrue(websiteManager.loginPage.checkErrorSummary("The Enter your e-mail address is required."));
		assertTrue(websiteManager.loginPage.checkErrorSummary("The Enter your password is required."));
	}

	@When("^the user enters the following  and TestPassword credentials$")
	public void the_user_enters_the_following_and_TestPassword_credentials() throws Throwable {
		LOG.info("Entering a valid password and leaving the email field empty.");
		
		websiteManager.loginPage.enterEmailAddress("");
		websiteManager.loginPage.enterPassword("TestPassword");
		websiteManager.loginPage.selectSignIn();
	}

	@Then("^the user is shown an error message The Enter your e-mail address is required\\.  successfully$")
	public void the_user_is_shown_an_error_message_The_Enter_your_e_mail_address_is_required_successfully() throws Throwable {
		LOG.info("Validating the error message.");
		
		assertTrue(websiteManager.loginPage.checkErrorSummary("The Enter your e-mail address is required."));
	}

	@When("^the user enters the following par_coordinator@example\\.com and  credentials$")
	public void the_user_enters_the_following_par_coordinator_example_com_and_credentials() throws Throwable {
		LOG.info("Entering a valid email and leaving the password field empty.");
		
		websiteManager.loginPage.enterEmailAddress("par_coordinator@example.com");
		websiteManager.loginPage.enterPassword("");
		websiteManager.loginPage.selectSignIn();
	}

	@Then("^the user is shown an error message The Enter your password is required\\. Unrecognized username or password\\. Forgot your password\\? successfully$")
	public void the_user_is_shown_an_error_message_The_Enter_your_password_is_required_Unrecognized_username_or_password_Forgot_your_password_successfully() throws Throwable {
		LOG.info("Validating the error messages.");
		
		assertTrue(websiteManager.loginPage.checkErrorSummary("The Enter your password is required."));
		assertTrue(websiteManager.loginPage.checkErrorSummary("Unrecognized username or password. Forgot your password?"));
	}
	
	@When("^the user enters the following par_coordinator@example\\.com and Invalid credentials$")
	public void the_user_enters_the_following_par_coordinator_example_com_and_Invalid_credentials() throws Throwable {
		LOG.info("Entering a valid email and an invalid password.");
		
		websiteManager.loginPage.enterEmailAddress("par_coordinator@example.com");
		websiteManager.loginPage.enterPassword("Invalid");
		websiteManager.loginPage.selectSignIn();
	}
	
	@When("^the user enters the following Invalid and TestPassword credentials$")
	public void the_user_enters_the_following_Invalid_and_TestPassword_credentials() throws Throwable {
		LOG.info("Entering an invalid email and a valid password.");
		
		websiteManager.loginPage.enterEmailAddress("Invalid");
		websiteManager.loginPage.enterPassword("TestPassword");
		websiteManager.loginPage.selectSignIn();
	}

	@When("^the user enters the following Invalid and Invalid credentials$")
	public void the_user_enters_the_following_Invalid_and_Invalid_credentials() throws Throwable {
		LOG.info("Entering an invalid email and password.");
		
		websiteManager.loginPage.enterEmailAddress("Invalid");
		websiteManager.loginPage.enterPassword("Invalid");
		websiteManager.loginPage.selectSignIn();
	}

	@Then("^the user is shown an error message Unrecognized username or password\\. Forgot your password\\?  successfully$")
	public void the_user_is_shown_an_error_message_Unrecognized_username_or_password_Forgot_your_password_successfully() throws Throwable {
		LOG.info("Validating the error message.");
		
		assertTrue(websiteManager.loginPage.checkErrorSummary("Unrecognized username or password. Forgot your password?"));
	}

	
}
