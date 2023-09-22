package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class EnterTheDatePage extends BasePageObject {
	
	@FindBy(id = "edit-day")
	private WebElement dayField;
	
	@FindBy(id = "edit-month")
	private WebElement monthField;
	
	@FindBy(id = "edit-year")
	private WebElement yearField;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public EnterTheDatePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	// date field can be used for Sad path tests or future date tests.
	
	public TradingPage clickContinueButtonForMembershipBegan() {
		continueBtn.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
}
