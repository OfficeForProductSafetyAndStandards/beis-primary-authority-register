package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AuthoritiesSearchPage extends BasePageObject {
	
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
	
	public AuthoritiesSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void goToHelpDeskDashboard() {
		helpDeskDashboardBtn.click();
	}
	
	public void searchAuthority(String authorityName) {
		
		if(searchInput.isDisplayed()) {
			searchInput.sendKeys(authorityName);
			searchBtn.click();
		}
	}
	
	public void selectAddAuthority() {
		addAuthorityBtn.click();
	}
	
	public void selectManageAuthority() {
		manageAuthorityLink.click();
	}
	
	public void selectTransferPartnerships() {
		transferPartnershipsLink.click();
	}
	

}
