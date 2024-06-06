package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.SharedPageObjects.EnterTheDatePage;
import uk.gov.beis.utility.DataStore;

public class MemberListPage extends BasePageObject {
	
	@FindBy(linkText = "Add a member")
	private WebElement addAMemberLink;
	
	@FindBy(linkText = "Upload a Member List (CSV)")
	private WebElement uploadMembersListLink;
	
	@FindBy(id = "edit-organisation-name")
	private WebElement memberSearchbar;
	
	@FindBy(id = "edit-submit-members-list")
	private WebElement applyBtn;
	
	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	private WebElement continueBtn;
	
	String memberNameLocator = "//td/a[contains(normalize-space(),'?')]";
	String ceaseMemberLocator = "//td/a[contains(normalize-space(),'Cease membership')]";
	
	String memberSize = "//select/option[contains(normalize-space(),'?')]";
	
	public MemberListPage() throws ClassNotFoundException, IOException {
		super();
	}

	public AddOrganisationNamePage selectAddAMemberLink() {
		addAMemberLink.click();
		return PageFactory.initElements(driver, AddOrganisationNamePage.class);
	}
	
	public UploadListOfMembersPage selectUploadMembersListLink() {
		uploadMembersListLink.click();
		return PageFactory.initElements(driver, UploadListOfMembersPage.class);
	}
	
	public void searchForAMember(String memberName) {
		memberSearchbar.clear();
		memberSearchbar.sendKeys(memberName);
		
		applyBtn.click();
	}
	
	public MemberOrganisationSummaryPage selectMembersName() {
		driver.findElement(By.xpath(memberNameLocator.replace("?", DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME)))).click();
		
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
	
	public EnterTheDatePage selectCeaseMembership() {
		driver.findElement(By.xpath(ceaseMemberLocator)).click();
		
		return PageFactory.initElements(driver, EnterTheDatePage.class);
	}
	
	public boolean checkMemberCreated() {
		
		return driver.findElement(By.xpath(memberNameLocator.replace("?", DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME)))).isDisplayed();
	}
	
	public boolean checkMembershipActionButtons() {
		return driver.findElements(By.xpath("(//td[@class='views-field views-field-par-flow-link-1'])[1]/a")).isEmpty();
	}
	
	public String getMembershipCeasedDate() {
		return driver.findElement(By.xpath("//td[@class = 'views-field views-field-date-membership-ceased']/time")).getText();
	}
	
	public boolean checkMembersListUploaded() {
		
		return driver.findElement(By.xpath(memberNameLocator.replace("?", DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME)))).isDisplayed();
	}
	
	public TradingPage selectMemberSize(String size) {
		driver.findElement(By.xpath(memberSize.replace("?", size))).click();
		continueBtn.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
}
