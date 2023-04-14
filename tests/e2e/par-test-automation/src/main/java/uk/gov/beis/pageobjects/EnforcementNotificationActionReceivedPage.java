package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class EnforcementNotificationActionReceivedPage extends BasePageObject {
	public EnforcementNotificationActionReceivedPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(xpath = "//*[@id=\"block-par-theme-content\"]/div[1]/div/div[1]/fieldset/div[1]/fieldset")
	private WebElement enforcementOfficerDetails;
	
	@FindBy(xpath = "//*[@id=\"block-par-theme-content\"]/div[1]/div/div[1]/fieldset/div[2]/fieldset/p")
	private WebElement enforcingAuthorityName;
	
	@FindBy(xpath = "//*[@id=\"block-par-theme-content\"]/div[1]/div/div[2]/fieldset/div[1]/fieldset/p")
	private WebElement enforcedOrganisationName;
	
	@FindBy(xpath = "//*[@id=\"block-par-theme-content\"]/div[1]/div/div[2]/fieldset/div[2]/fieldset/p[1]")
	private WebElement primaryAuthorityName;
	
	@FindBy(linkText = "Done")
	private WebElement doneBtn;
	
	public String getEnforcementOfficerDetails() {
		return enforcementOfficerDetails.getText();
	}
	
	public String getEnforcingAuthorityName() {
		return enforcingAuthorityName.getText();
	}
	
	public String getEnforcedOrganisationName() {
		return enforcedOrganisationName.getText();
	}
	
	public String getPrimaryAuthorityName() {
		return primaryAuthorityName.getText();
	}
	
	public EnforcementSearchPage clickDoneButton() {
		doneBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
}
