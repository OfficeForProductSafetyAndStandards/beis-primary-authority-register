package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PersonUserRoleTypeSelectionPage extends BasePageObject {
	public PersonUserRoleTypeSelectionPage() throws ClassNotFoundException, IOException {
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
	}
	
	public void selectAuthorityMember() {
		authorityMemberRadioBtn.click();
	}
	
	public void selectAuthorityManager() {
		authorityManagerRadioBtn.click();
	}
	
	public void selectEnforcementOfficer() {
		enforcementOfficerRadioBtn.click();
	}
	
	public void selectProcessingTeamMember() {
		processingTeamMemberRadioBtn.click();
	}
	
	public void selectSeniorAdministrationOfficer() {
		seniorAdministrationOfficerRadioBtn.click();
	}
	
	// Check these Steps
	public InvitePersonToCreateAccountPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, InvitePersonToCreateAccountPage.class);
	}
	
	public ProfileReviewPage clickProfileReviewContinueButton() {
		  continueBtn.click(); 
		  return PageFactory.initElements(driver,ProfileReviewPage.class); 
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}