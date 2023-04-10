package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AuthorityUserTypeSelectionPage extends BasePageObject {
	public AuthorityUserTypeSelectionPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(id = "edit-role-par-organisation")
	private WebElement organisationMemberRadioBtn;
	
	@FindBy(id = "edit-role-par-authority")
	private WebElement authorityMemberRadioBtn;
	
	@FindBy(id = "edit-role-par-enforcement")
	private WebElement enforcementMemberRadioBtn;
	
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
	
	public void selectEnforcementMember() {
		enforcementMemberRadioBtn.click();
	}
	
	public ProfileReviewPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, ProfileReviewPage.class);
	}
	
	public DashboardPage clickCancelButton() {
		cancelBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
}
