package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.OtherPageObjects.AccountInvitePage;

public class UserRoleTypePage extends BasePageObject {
	
	@FindBy(id = "edit-general-secretary-state")
	private WebElement secretaryOfStateBtn;
	
	@FindBy(id = "edit-general-senior-administration-officer")
	private WebElement seniorAdministratorBtn;
	
	@FindBy(id = "edit-general-par-helpdesk")
	private WebElement processingTeamMemberBtn;
	
	@FindBy(id = "edit-general-national-regulator")
	private WebElement nationalRegulatorBtn;
	
	@FindBy(id = "edit-par-data-authority-par-authority-manager")
	private WebElement authorityManagerBtn;
	
	@FindBy(id = "edit-par-data-authority-par-authority")
	private WebElement authorityMemberBtn;
	
	@FindBy(id = "edit-par-data-authority-par-enforcement")
	private WebElement enforcementOfficerBtn;
	
	@FindBy(id = "edit-par-data-organisation-par-organisation-manager")
	private WebElement organisationManagerBtn;
	
	@FindBy(id = "edit-par-data-organisation-par-organisation")
	private WebElement organisationMemberBtn;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public UserRoleTypePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void deselectAllMemberships() {
		List<WebElement> checkboxes = driver.findElements(By.xpath("//input[@class='form-checkbox form-control govuk-input govuk-checkboxes__input']"));
		
		for(WebElement checkbox : checkboxes) {
			if(checkbox.isSelected()) {
				checkbox.click();
			}
		}
	}
	
	public void chooseMembershipRole(String roleType) {
		switch(roleType) {
		case "Secretary of State":
			selectSecretaryOfState();
			break;
		case "Senior Administration Officer":
			selectSeniorAdministrator();
			break;
		case "Processing Team Member":
			selectProcessingTeamMember();
			break;
		case "National Regulator":
			selectNationalRegulator();
			break;
		case "Authority Manager":
			selectAuthorityManager();
			break;
		case "Authority Member":
			selectAuthorityMember();
			break;
		case "Enforcement Officer":
			selectEnforcementOfficer();
			break;
		case "Organisation Manager":
			selectOrganisationManager();
			break;
		case "Organisation Member":
			selectOrganisationMember();
			break;
		}
	}
	
	public void selectSecretaryOfState() {
		if(!secretaryOfStateBtn.isSelected()) {
			secretaryOfStateBtn.click();
		}
	}
	public void selectSeniorAdministrator() {
		if(!seniorAdministratorBtn.isSelected()) {
			seniorAdministratorBtn.click();
		}
	}
	
	public void selectProcessingTeamMember() {
		if(!processingTeamMemberBtn.isSelected()) {
			processingTeamMemberBtn.click();
		}
	}
	
	public void selectNationalRegulator() {
		if(!nationalRegulatorBtn.isSelected()) {
			nationalRegulatorBtn.click();
		}
	}
	
	public void selectAuthorityManager() {
		if(!authorityManagerBtn.isSelected()) {
			authorityManagerBtn.click();
		}
	}
	
	public void selectAuthorityMember() {
		if(!authorityMemberBtn.isSelected()) {
			authorityMemberBtn.click();
		}
	}
	
	public void selectEnforcementOfficer() {
		if(!enforcementOfficerBtn.isSelected()) {
			enforcementOfficerBtn.click();
		}
	}
	
	public void selectOrganisationManager() {
		if(!organisationManagerBtn.isSelected()) {
			organisationManagerBtn.click();
		}
	}
	
	public void selectOrganisationMember() {
		if(!organisationMemberBtn.isSelected()) {
			organisationMemberBtn.click();
		}
	}
	
	public UserProfilePage goToUserProfilePage() {
		continueBtn.click();
		return PageFactory.initElements(driver, UserProfilePage.class);
	}
	
	public AccountInvitePage goToUserAccountInvitePage() {
		continueBtn.click();
		return PageFactory.initElements(driver, AccountInvitePage.class);
	}
}
