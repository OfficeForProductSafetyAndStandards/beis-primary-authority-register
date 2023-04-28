package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.List;
import java.util.Random;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.Select;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class PersonMembershipPage extends BasePageObject {
	public PersonMembershipPage() throws ClassNotFoundException, IOException {
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
	@FindBy(xpath = "//*[@id=\"edit_par_data_organisation_id_chosen\"]/ul/li/input")
	private WebElement organisationChoiceInput;
	
	@FindBy(className = "chosen-drop")
	private WebElement organisationChoiceList;
	
	@FindBy(id = "edit_par_data_authority_id_chosen")
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
	public void selectOrganisation() {
		organisationChoiceInput.click();
		
		WebElement randomOrganisation = chooseRandomOrganisation();
		
		String organisation = randomOrganisation.getText();
		randomOrganisation.click();
		
		DataStore.saveValue(UsableValues.CHOSEN_ORGANISATION, organisation);
	}
	
	public void selectAuthority() {
		Select selectObject = new Select(authorityDropDown);
		
		String randomAuthority = chooseRandomAuthority(selectObject).getText();
		selectObject.selectByVisibleText(randomAuthority);
		
		DataStore.saveValue(UsableValues.CHOSEN_AUTHORITY, randomAuthority);
	}
	
	public PersonUserRoleTypeSelectionPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, PersonUserRoleTypeSelectionPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
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
