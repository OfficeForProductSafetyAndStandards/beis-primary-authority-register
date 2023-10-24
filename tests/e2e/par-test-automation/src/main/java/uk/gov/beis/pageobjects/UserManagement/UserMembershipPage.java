package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.util.List;
import java.util.Map;
import java.util.Random;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.Select;

import cucumber.api.DataTable;
import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class UserMembershipPage extends BasePageObject {
	
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
	@FindBy(xpath = "//div[@id='edit_par_data_organisation_id_chosen']/ul/li/input")
	private WebElement organisationChoiceInput;
	
	@FindBy(xpath = "//div[@id='edit_par_data_authority_id_chosen']/ul/li/input")
	private WebElement authorityChoiceInput;
	
	@FindBy(id = "edit-par-data-authority-id")
	private WebElement authorityDropDown;		// Temp Web Element
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public UserMembershipPage() throws ClassNotFoundException, IOException {
		super();
	}
	
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
		mainOrganisationSelectionMethod(details);
		
		//tempOrganisationSelectionMethod();
	}
	
	public void selectAuthority(DataTable details) {
		//mainAuthoritySelectionMethod(details);
		
		tempAuthoritySelectionMethod();
	}
	
	public UserRoleTypePage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, UserRoleTypePage.class);
	}
	
	// These methods are what the Web Elements should be doing which the Accessibility branch should have.
	private void mainOrganisationSelectionMethod(DataTable details) {
		organisationChoiceInput.click();
		
		for (Map<String, String> data : details.asMaps(String.class, String.class)) {

			DataStore.saveValue(UsableValues.CHOSEN_ORGANISATION, data.get("Organisation"));
		}
		
		organisationChoiceInput.sendKeys(DataStore.getSavedValue(UsableValues.CHOSEN_ORGANISATION));
		
		WebElement organisation = driver.findElement(By.cssSelector("div.chosen-drop ul.chosen-results li.active-result"));
		organisation.click();
	}
	
	private void mainAuthoritySelectionMethod(DataTable details) {
		authorityChoiceInput.click();

		for (Map<String, String> data : details.asMaps(String.class, String.class)) {

			DataStore.saveValue(UsableValues.CHOSEN_AUTHORITY, data.get("Authority"));
		}
		
		authorityChoiceInput.sendKeys(DataStore.getSavedValue(UsableValues.CHOSEN_AUTHORITY));
		
		WebElement authority = driver.findElement(By.cssSelector("div.chosen-drop ul.chosen-results li.active-result"));
		authority.click();
	}
	
	
	// These are here due to the on going issue with Input fields and Select Drop-down menu Web Elements changing with each new build.
	private void tempOrganisationSelectionMethod() {
		organisationChoiceInput.click();
		
		WebElement randomOrganisation = chooseRandomOrganisation();
		
		String organisation = randomOrganisation.getText();
		randomOrganisation.click();
		
		DataStore.saveValue(UsableValues.CHOSEN_ORGANISATION, organisation);
	}
	
	private void tempAuthoritySelectionMethod() {
		Select selectObject = new Select(authorityDropDown);
		
		String randomAuthority = chooseRandomAuthority(selectObject).getText();
		selectObject.selectByVisibleText(randomAuthority);
		
		DataStore.saveValue(UsableValues.CHOSEN_AUTHORITY, randomAuthority);
	}
	
	private WebElement chooseRandomOrganisation() {
		Random random = new Random();
		List<WebElement> organisations = driver.findElements(By.cssSelector("div.chosen-drop ul.chosen-results li.active-result"));
		
		return organisations.get(random.nextInt(organisations.size()));
	}
	
	private WebElement chooseRandomAuthority(Select selectList) {
		Random random = new Random();
		List<WebElement> authorities = selectList.getOptions();
		
		return authorities.get(random.nextInt(authorities.size()));
	}
}
