package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.DashboardPage;
import uk.gov.beis.pageobjects.TransferPartnerships.AuthorityTransferSelectionPage;

public class AuthorityDashboardPage extends BasePageObject {
	
	@FindBy(xpath = "//a[contains(text(), 'Helpdesk')]")
	private WebElement helpDeskDashboardBtn;
	
	@FindBy(linkText = "Add an authority")
	private WebElement addAuthorityBtn;
	
	@FindBy(id = "edit-name-search")
	private WebElement searchInput;

	@FindBy(id = "edit-submit-helpdesk-authorities")
	private WebElement searchBtn;
	
	@FindBy(linkText = "Manage authority")
	private WebElement manageAuthorityLink;
	
	@FindBy(linkText = "Transfer partnerships")
	private WebElement transferPartnershipsLink;
	
	public AuthorityDashboardPage() throws ClassNotFoundException, IOException {
		super();
		
		LOG.info("Authority Dashboard Page is Loaded!");
	}
	
	public DashboardPage goToHelpDeskDashboard() {
		helpDeskDashboardBtn.click();
		return PageFactory.initElements(driver, DashboardPage.class);
	}
	
	public AuthorityDashboardPage searchAuthority(String authorityName) {
		
		if(searchInput.isDisplayed()) {
			searchInput.sendKeys(authorityName);
			searchBtn.click();
		}
		
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}
	
	public AuthorityNamePage selectAddAuthority() {
		addAuthorityBtn.click();
		return PageFactory.initElements(driver, AuthorityNamePage.class);
	}
	
	public AuthorityConfirmationPage selectManageAuthority() {
		manageAuthorityLink.click();
		return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
	}
	
	public AuthorityTransferSelectionPage selectTransferPartnerships() {
		transferPartnershipsLink.click();
		return PageFactory.initElements(driver, AuthorityTransferSelectionPage.class);
	}
	

}
