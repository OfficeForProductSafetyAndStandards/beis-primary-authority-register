package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class GivePersonAccountPage extends BasePageObject {
	public GivePersonAccountPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-account-new")
	private WebElement inviteUserRadioBtn;
	
	//private String existingAccountRadio = "edit-account-?"; // Check that ? does not equal the word "none"
	
	@FindBy(xpath = "//input[contains(@id,'edit-account-')]") // ID of the current Authority Signed In.
	private List<WebElement> radioButtons;
	
//	@FindBy(id = "edit-account-19278") // ID of the current Authority Signed In.
//	private WebElement existingAccountRadioBtn;
	
	@FindBy(id = "edit-account-none")
	private WebElement createUserAccountRadioBtn;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public void selectInviteUserToCreateAccount() {
		inviteUserRadioBtn.click();
	}
	
	public void selectExistingAccount() {
		// Idea 04: Get the Account ID either from the URL or find it on the Dash-board.
		// Idea 03: Possibly create a function similar to the EnquirySearchPage.
		
		// Idea 02:
		//List<WebElement> radioButtons = driver.findElements(By.xpath("//input[contains(@id,'edit-account-')]"));
		
		for(WebElement radio : radioButtons) {
			if(radio != createUserAccountRadioBtn) {
				radio.click();
			}
		}
		
		// Idea 01:
		//WebElement radioBtn = driver.findElement(By.xpath(existingAccountRadio.replace("?", !"none")));
		//radioBtn.click();
		
		// Original:
		//existingAccountRadioBtn.click();
	}
	
	public void selectCreateAccount() {		// Check as Authority User
		createUserAccountRadioBtn.click();
	}
	
	// Check these steps
	public ChoosePersonMembershipPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, ChoosePersonMembershipPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
