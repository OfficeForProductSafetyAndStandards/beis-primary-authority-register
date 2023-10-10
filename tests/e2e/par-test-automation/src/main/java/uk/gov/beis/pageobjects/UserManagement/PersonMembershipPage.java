package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.util.Map;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import cucumber.api.DataTable;
import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DashboardPage;
import uk.gov.beis.utility.DataStore;

public class PersonMembershipPage extends BasePageObject {
	public PersonMembershipPage() throws ClassNotFoundException, IOException {
		super();
	}

	// Organisations
	@FindBy(id = "edit-par-data-organisation-id-68")
	private WebElement testBusinessCheckbox;
	
	@FindBy(id = "edit-par-data-organisation-id-50")
	private WebElement abcdMartCheckbox;
	
	@FindBy(id = "edit-par-data-organisation-id-51")
	private WebElement demolitionExpertsCheckbox;
	
	@FindBy(id = "edit-par-data-organisation-id-52")
	private WebElement partnershipConfirmedByAuthorityCheckbox;
	
	// Authorities
	@FindBy(xpath = "//label[contains(text(), 'City Enforcement Squad')]/preceding-sibling::input")
	private WebElement cityEnforcementSquadCheckbox;
	
	@FindBy(xpath = "//label[contains(text(), 'Upper West Side Borough Council')]/preceding-sibling::input")
	private WebElement upperWestSideBoroughCouncilCheckbox;
	
	@FindBy(xpath = "//label[contains(text(), 'Lower East Side Borough Council')]/preceding-sibling::input")
	private WebElement lowerEastSideBoroughCouncilCheckbox;
	
	// Help Desk Membership Selection Page Elements
	//@FindBy(xpath = "//*[@id=\"edit_par_data_organisation_id_chosen\"]/ul/li/input")
	@FindBy(xpath = "//div[@id='edit_par_data_organisation_id_chosen']/ul/li/input")
	private WebElement organisationChoiceInput;
	
	//@FindBy(xpath = "//*[@id=\"edit_par_data_authority_id_chosen\"]/ul/li/input")
	@FindBy(xpath = "//div[@id='edit_par_data_authority_id_chosen']/ul/li/input")
	private WebElement authorityChoiceInput;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-cancel")
	private WebElement cancelBtn;
	
	public void selectTestBusiness() {
		if(testBusinessCheckbox.isSelected()) {
			testBusinessCheckbox.click();
		}
	}
	
	public void selectABCDMart() {
		if(!abcdMartCheckbox.isSelected()) {
			abcdMartCheckbox.click();
		}
	}
	
	public void selectDemolitionExperts() {
		if(!demolitionExpertsCheckbox.isSelected()) {
			demolitionExpertsCheckbox.click();
		}
	}
	
	public void selectPartnershipConfirmedByAuthority() {
		if(!partnershipConfirmedByAuthorityCheckbox.isSelected()) {
			partnershipConfirmedByAuthorityCheckbox.click();
		}
	}
	
	public void selectCityEnforcementSquad() {
		if(!cityEnforcementSquadCheckbox.isSelected()) {
			cityEnforcementSquadCheckbox.click();
		}
	}
	
	public void selectUpperWestSideBoroughCouncil() {
		if(!upperWestSideBoroughCouncilCheckbox.isSelected()) {
			upperWestSideBoroughCouncilCheckbox.click();
		}
	}
	
	public void selectLowerEstSideBoroughCouncil() {
		if(!lowerEastSideBoroughCouncilCheckbox.isSelected()) {
			lowerEastSideBoroughCouncilCheckbox.click();
		}
	}
	
	// Help Desk Membership Selection Page Methods
	public void selectOrganisation(DataTable details) {
		organisationChoiceInput.click();
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {

			DataStore.saveValue(UsableValues.CHOSEN_ORGANISATION, data.get("Organisation"));
		}
		
		organisationChoiceInput.sendKeys(DataStore.getSavedValue(UsableValues.CHOSEN_ORGANISATION));
		
		WebElement organisation = driver.findElement(By.cssSelector("div.chosen-drop ul.chosen-results li.active-result"));
		organisation.click();
	}
	
	public void selectAuthority(DataTable details) {
		authorityChoiceInput.click();

		for (Map<String, String> data : details.asMaps(String.class, String.class)) {

			DataStore.saveValue(UsableValues.CHOSEN_AUTHORITY, data.get("Authority"));
		}
		
		authorityChoiceInput.sendKeys(DataStore.getSavedValue(UsableValues.CHOSEN_AUTHORITY));
		
		WebElement authority = driver.findElement(By.cssSelector("div.chosen-drop ul.chosen-results li.active-result"));
		authority.click();
	}
	
	public PersonUserRoleTypePage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, PersonUserRoleTypePage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
