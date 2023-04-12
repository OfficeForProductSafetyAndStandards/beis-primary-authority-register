package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AuthorityChooseMembershipPage extends BasePageObject {
	public AuthorityChooseMembershipPage() throws ClassNotFoundException, IOException {
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
	
	public AuthorityUserTypeSelectionPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityUserTypeSelectionPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
