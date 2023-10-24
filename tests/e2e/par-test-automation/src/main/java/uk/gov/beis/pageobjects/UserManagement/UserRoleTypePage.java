package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.AccountInvitePage;
import uk.gov.beis.utility.DataStore;

public class UserRoleTypePage extends BasePageObject {
	
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
	
	public UserRoleTypePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectOrganisationMember() {
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
	
	public AccountInvitePage goToAccountInvitePage() {
		continueBtn.click();
		return PageFactory.initElements(driver, AccountInvitePage.class);
	}
	
	public ProfileReviewPage goToProfileReviewPage() {
		  continueBtn.click(); 
		  return PageFactory.initElements(driver,ProfileReviewPage.class); 
	}
	
	private void getRoleName() {
		for(WebElement div : driver.findElements(By.className("govuk-radios__item"))) {
			if(div.findElement(By.tagName("input")).isSelected()) {
				DataStore.saveValue(UsableValues.ACCOUNT_TYPE, div.findElement(By.tagName("label")).getText());
			}
		}
	}
}
