package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class PersonUserRoleTypePage extends BasePageObject {
	public PersonUserRoleTypePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-role-par-authority")
	private WebElement authorityMemberRadioBtn;
	
	@FindBy(id = "edit-role-par-enforcement")
	private WebElement enforcementOfficerRadioBtn;
	
	// Addition Role Selections for Help Desk User
	@FindBy(id = "edit-role-par-organisation")
	private WebElement organisationMemberRadioBtn;
	
	@FindBy(id = "edit-role-par-authority-manager")
	private WebElement authorityManagerRadioBtn;
	
	@FindBy(id = "edit-role-par-helpdesk")
	private WebElement processingTeamMemberRadioBtn;
	
	@FindBy(id = "edit-role-senior-administration-officer")
	private WebElement seniorAdministrationOfficerRadioBtn;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public void selectOrganisationMember() { // Disappeared from the page but was there when I first created this Class??
		organisationMemberRadioBtn.click();
		getRoleName();
	}
	
	public void selectAuthorityMember() {
		authorityMemberRadioBtn.click();
		getRoleName();
	}
	
	public void selectAuthorityManager() {
		authorityManagerRadioBtn.click();
		getRoleName();
	}
	
	public void selectEnforcementOfficer() {
		enforcementOfficerRadioBtn.click();
		getRoleName();
	}
	
	public void selectProcessingTeamMember() {
		processingTeamMemberRadioBtn.click();
		getRoleName();
	}
	
	public void selectSeniorAdministrationOfficer() {
		seniorAdministrationOfficerRadioBtn.click();
		getRoleName();
	}
	
	public PersonCreateAccountPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, PersonCreateAccountPage.class);
	}
	
	public UserProfileConfirmationPage clickProfileReviewContinueButton() {
		  continueBtn.click(); 
		  return PageFactory.initElements(driver,UserProfileConfirmationPage.class); 
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
	
	private void getRoleName() {
		for(WebElement div : driver.findElements(By.className("govuk-radios__item"))) {
			if(div.findElement(By.tagName("input")).isSelected()) {
				DataStore.saveValue(UsableValues.ACCOUNT_TYPE, div.findElement(By.tagName("label")).getText());
			}
		}
	}
}
