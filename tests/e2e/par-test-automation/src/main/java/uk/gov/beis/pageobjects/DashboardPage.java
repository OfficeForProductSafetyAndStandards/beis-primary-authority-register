package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class DashboardPage extends BasePageObject {

	public DashboardPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//div[@id='block-par-theme-page-title']")
	private WebElement dashBoard;

	@FindBy(linkText = "Apply for a new partnership")
	WebElement applyPartnershipBtn;
	
	@FindBy(linkText = "See your enforcement notices")
	WebElement seeEnforcementNoticesBtn;
	
	@FindBy(linkText = "Manage enforcement notices")
	WebElement manageEnforcementNoticesBtn;

	@FindBy(linkText = "See your partnerships")
	WebElement viewPartnershipBtn;

	@FindBy(linkText = "Search partnerships")
	WebElement searchPartnershipBtn;
	
	@FindBy(linkText = "Search for a partnership")
	WebElement searchPartnershipBtn1;

	@FindBy(linkText = "Manage authorities")
	WebElement mangeAuthoritiesBtn;
	
	@FindBy(linkText = "Manage organisations")
	WebElement mangeOrganisationsBtn;
	
	public OrganisationDashboardPage selectManageOrganisations() {
		mangeOrganisationsBtn.click();
		return PageFactory.initElements(driver, OrganisationDashboardPage.class);
	}
	
	public AuthorityPage selectApplyForNewPartnership() {
		applyPartnershipBtn.click();
		return PageFactory.initElements(driver, AuthorityPage.class);
	}
	
	public EnforcementSearchPage selectSeeEnforcementNotices() {
		seeEnforcementNoticesBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
	
	public EnforcementSearchPage selectManageEnforcementNotices() {
		manageEnforcementNoticesBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}

	public PartnershipSearchPage selectSeePartnerships() {
		viewPartnershipBtn.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}
	
	public AuthorityDashboardPage selectManageAuthorities() {
		mangeAuthoritiesBtn.click();
		return PageFactory.initElements(driver, AuthorityDashboardPage.class);
	}

	public PartnershipAdvancedSearchPage selectSearchPartnerships() {
		searchPartnershipBtn.click();
		return PageFactory.initElements(driver, PartnershipAdvancedSearchPage.class);
	}
	
	public PartnershipSearchPage selectSearchforPartnership() {
		searchPartnershipBtn1.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}

	public String checkPage() {
		return dashBoard.getText();
	}

	public DashboardPage checkAndAcceptCookies() {
		try {
			driver.findElement(By.xpath("//button[contains(text(),'Accept')]")).click();
		} catch (NoSuchElementException e) {
			// do nothing
		}
		return PageFactory.initElements(driver, DashboardPage.class);
	}

}
