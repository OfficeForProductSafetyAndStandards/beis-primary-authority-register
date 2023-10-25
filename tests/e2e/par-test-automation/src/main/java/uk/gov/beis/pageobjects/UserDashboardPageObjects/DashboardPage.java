package uk.gov.beis.pageobjects.UserDashboardPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.DeviationRequestPageObjects.DeviationSearchPage;
import uk.gov.beis.pageobjects.EnforcementNoticePageObjects.EnforcementSearchPage;
import uk.gov.beis.pageobjects.GeneralEnquiryPageObjects.EnquiriesSearchPage;
import uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects.InspectionFeedbackSearchPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.AuthorityPage;
import uk.gov.beis.pageobjects.PartnershipPageObjects.PartnershipSearchPage;
import uk.gov.beis.pageobjects.UserManagement.ManagePeoplePage;

public class DashboardPage extends BaseDashboardPage {
	
	@FindBy(xpath = "//div[@id='block-par-theme-page-title']")
	private WebElement dashBoard;
	
	@FindBy(linkText = "See your partnerships")
	private WebElement viewPartnershipBtn;
	
	@FindBy(linkText = "Search for a partnership")
	private WebElement searchPartnershipBtn;
	
	@FindBy(linkText = "Apply for a new partnership")
	private WebElement applyPartnershipBtn;
	
	@FindBy(linkText = "See your enforcement notices")
	private WebElement seeEnforcementNoticesBtn;
	
	@FindBy(linkText = "See your deviation requests")
	private WebElement deviationReqBtn;
	
	@FindBy(linkText = "See your inspection feedback")
	private WebElement inspectionFeedbackNoticeBtn;
	
	@FindBy(partialLinkText = "general enquiries")
	private WebElement generalEnquiriesBtn;
	
	@FindBy(linkText = "Manage your colleagues")
	private WebElement manageYourColleaguesBtn;
	
	public DashboardPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipSearchPage selectSeePartnerships() {
		viewPartnershipBtn.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}
	
	public PartnershipSearchPage selectSearchforPartnership() {
		searchPartnershipBtn.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}
	
	public AuthorityPage selectApplyForNewPartnership() {
		applyPartnershipBtn.click();
		return PageFactory.initElements(driver, AuthorityPage.class);
	}
	
	public EnforcementSearchPage selectSeeEnforcementNotices() {
		seeEnforcementNoticesBtn.click();
		return PageFactory.initElements(driver, EnforcementSearchPage.class);
	}
	
	public DeviationSearchPage selectSeeDeviationRequests() {
		deviationReqBtn.click();
		return PageFactory.initElements(driver, DeviationSearchPage.class);
	}
	
	public InspectionFeedbackSearchPage selectSeeInspectionFeedbackNotices() {
		inspectionFeedbackNoticeBtn.click();
		return PageFactory.initElements(driver, InspectionFeedbackSearchPage.class);
	}
	
	public EnquiriesSearchPage selectGeneralEnquiries() {
		generalEnquiriesBtn.click();
		return PageFactory.initElements(driver, EnquiriesSearchPage.class);
	}
	
	public ManagePeoplePage selectManageColleagues() {
		manageYourColleaguesBtn.click();
		return PageFactory.initElements(driver, ManagePeoplePage.class);
	}
	
	public String checkPage() {
		return dashBoard.getText();
	}

	public DashboardPage checkAndAcceptCookies() {
		driver.manage().deleteAllCookies();
		
		if(!driver.findElements(By.id("block-cookiebanner")).isEmpty()) {
			driver.findElement(By.xpath("//button[contains(text(),'Accept')]")).click();
		}
		
		//try {
		//	driver.findElement(By.xpath("//button[contains(text(),'Accept')]")).click();
		//} catch (NoSuchElementException e) {
			// do nothing
		//}
		return PageFactory.initElements(driver, DashboardPage.class);
	}

}
