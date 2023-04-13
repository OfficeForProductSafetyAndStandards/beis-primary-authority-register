package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.Select;

public class ChoosePersonMembershipPage extends BasePageObject {
	public ChoosePersonMembershipPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-par-data-organisation-id-68")
	private WebElement testBusinessCheckbox;
	
	@FindBy(id = "edit-par-data-organisation-id-50")
	private WebElement abcdMartCheckbox;
	
	@FindBy(id = "edit-par-data-organisation-id-51")
	private WebElement demolitionExpertsCheckbox;
	
	@FindBy(id = "edit-par-data-organisation-id-52")
	private WebElement partnershipConfirmedByAuthorityCheckbox;
	
	@FindBy(id = "edit-par-data-authority-id-7")
	private WebElement cityEnforcementSquadCheckbox;
	
	@FindBy(id = "edit-par-data-authority-id-9")
	private WebElement upperWestSideBoroughCouncilCheckbox;
	
	@FindBy(id = "edit-par-data-authority-id-8")
	private WebElement lowerEastSideBoroughCouncilCheckbox;
	
	// Help Desk Membership Selection Page Elements
	@FindBy(id = "edit_par_data_organisation_id_chosen")
	private WebElement organisationChoiceDiv;
	
	@FindBy(id = "edit_par_data_authority_id_chosen") // From these IDs, find the text field to enter text?
	private WebElement authorityChoiceDiv;
	
	@FindBy(id = "edit-par-data-organisation-id")
	private WebElement organisationDropDown;
	
	@FindBy(id = "edit-par-data-authority-id")
	private WebElement authorityDropDown;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public void selectTestBusiness() {
		testBusinessCheckbox.click();
	}
	
	public void selectABCDMart() {
		abcdMartCheckbox.click();
	}
	
	public void selectDemolitionExperts() {
		demolitionExpertsCheckbox.click();
	}
	
	public void selectPartnershipConfirmedByAuthority() {
		partnershipConfirmedByAuthorityCheckbox.click();
	}
	
	public void selectCityEnforcementSquad() {
		cityEnforcementSquadCheckbox.click();
	}
	
	public void selectUpperWestSideBoroughCouncil() {
		upperWestSideBoroughCouncilCheckbox.click();
	}
	
	public void selectLowerEstSideBoroughCouncil() {
		lowerEastSideBoroughCouncilCheckbox.click();
	}
	
	// Help Desk Membership Selection Page Methods
	public void selectOrganisation(String option) {
		organisationChoiceDiv.click();
		
		Select selectObject = new Select(organisationDropDown);
		selectObject.selectByValue(option);
	}
	
	public void selectAuthority(String option) {
		authorityChoiceDiv.click();
		
		Select selectObject = new Select(authorityDropDown);
		selectObject.selectByValue(option);
	}
	
	public PersonUserRoleTypeSelectionPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, PersonUserRoleTypeSelectionPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
