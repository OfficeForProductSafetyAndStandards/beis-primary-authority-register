package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.util.List;
import java.util.Random;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.Select;

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
	
	public PersonUserRoleTypePage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, PersonUserRoleTypePage.class);
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
