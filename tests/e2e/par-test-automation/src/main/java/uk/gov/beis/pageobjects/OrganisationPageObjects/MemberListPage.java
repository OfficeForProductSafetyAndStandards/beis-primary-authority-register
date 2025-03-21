package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
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

	public void selectAddAMemberLink() {
		addAMemberLink.click();
	}
	
	public void selectUploadMembersListLink() {
		uploadMembersListLink.click();
	}
	
	public void searchForAMember(String memberName) {
		memberSearchbar.clear();
		memberSearchbar.sendKeys(memberName);
		
		applyBtn.click();
	}
	
	public void selectMembersName() {
		driver.findElement(By.xpath(memberNameLocator.replace("?", DataStore.getSavedValue(UsableValues.MEMBER_ORGANISATION_NAME)))).click();
	}
	
	public void selectCeaseMembership() {
		driver.findElement(By.xpath(ceaseMemberLocator)).click();
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
	
	public void selectMemberSize(String size) {
		driver.findElement(By.xpath(memberSize.replace("?", size))).click();
		continueBtn.click();
	}
}
