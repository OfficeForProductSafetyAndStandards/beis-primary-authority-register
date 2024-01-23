package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.BlockPage;
import uk.gov.beis.pageobjects.ReinstatePage;
import uk.gov.beis.pageobjects.RemovePage;
import uk.gov.beis.utility.DataStore;

public class UserProfilePage extends BasePageObject {
	
	@FindBy(xpath = "//div[@class='govuk-grid-row govuk-form-group']//div[1]/p")
	private WebElement userAccountEmail;
	
	@FindBy(xpath = "//div/a[contains(normalize-space(), 'Invite the user to create an account')]")
	private WebElement accountInvitationLink;

	@FindBy(xpath = "//div[@class='govuk-grid-row govuk-form-group']//div[2]/p")
	private WebElement userAccountType;
	
	@FindBy(linkText = "Manage roles")
	private WebElement managerolesLink;
	
	@FindBy(linkText = "Block user account")
	private WebElement blockUserAccountLink;
	
	@FindBy(linkText = "Re-activate user account")
	private WebElement reactivateUserAccountLink;
	
	@FindBy(linkText = "Add a membership")
	private WebElement addMembershipLink;
	
	@FindBy(xpath = "//div/p[@class='govuk-grid-column-two-thirds'][1]")
	private WebElement userContactName;

	@FindBy(xpath = "//div/p[@class='govuk-grid-column-two-thirds'][2]")
	private WebElement userContactEmail;

	@FindBy(xpath = "//div/p[@class='govuk-grid-column-one-third'][2]")
	private WebElement userContactPhoneNumbers;

	@FindBy(partialLinkText = "Update")
	private WebElement updateUserBtn;

	@FindBy(css = ".govuk-details__summary")
	private WebElement moreInformationBtn;

	@FindBy(id = "contact-detail-locations-1")
	private WebElement whereToContactDetails;
	
	@FindBy(linkText = "merge contact records")
	private WebElement mergeContactRecordsLink;
	
	@FindBy(linkText = "Done")
	private WebElement doneBtn;
	
	private String profileHeader = "//h1[contains(normalize-space(),'?')]";
	private String userMembershipLocator = "//div/p[contains(normalize-space(), '?')]";
	private String removeMembershipLocator = "//div/p[contains(normalize-space(), '?')]/a";
	
	public UserProfilePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public Boolean checkHeaderForName() {
		return driver.findElement(By.xpath(profileHeader.replace("?", getExpectedPersonsName()))).isDisplayed();
	}
	
	public String getUserAccountEmail() {
		return userAccountEmail.getText().trim();
	}

	public Boolean checkForUserAccountInvitationLink() {
		return accountInvitationLink.isDisplayed();
	}

	public Boolean checkUserAccountEmail() {
		return userContactEmail.getText().contains(DataStore.getSavedValue(UsableValues.PERSON_EMAIL_ADDRESS));
	}

	public Boolean checkUserAccountType() {
		return userAccountType.getText().contains(DataStore.getSavedValue(UsableValues.ACCOUNT_TYPE));
	}

	public Boolean checkUserMembershipDisplayed() {
		return driver.findElement(By.xpath(userMembershipLocator.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_NAME)))).isDisplayed();
	}
	
	public Boolean checkUserAccountIsNotActive() {
		return driver.findElement(By.xpath("//p/strong[contains(normalize-space(), 'The account is no longer active')]")).isDisplayed();
	}
	
	public Boolean checkLastSignInHeaderIsDisplayed() {
		return driver.findElement(By.xpath("//div/h3[contains(normalize-space(), 'Last sign in')]")).isDisplayed();
	}
	
	public Boolean checkReactivateUserAccountLinkIsDisplayed() {
		return reactivateUserAccountLink.isDisplayed();
	}
	
	public Boolean checkBlockUserAccountLinkIsDisplayed() {
		return blockUserAccountLink.isDisplayed();
	}
	
	public Boolean checkMembershipRemoved() {
		return driver.findElements(By.xpath(userMembershipLocator.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_NAME)))).isEmpty();
	}
	
	public Boolean checkContactName() {
		return userContactName.getText().contains(getExpectedPersonsName());
	}

	public Boolean checkContactEmail() {
		return userContactEmail.getText().contains(DataStore.getSavedValue(UsableValues.PERSON_EMAIL_ADDRESS).toLowerCase());
	}

	public String getContactEmail() {
		return userContactEmail.getText();
	}

	public Boolean checkContactWorkNumber() {
		return userContactPhoneNumbers.getText().contains(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER));
	}

	public Boolean checkContactPhoneNumbers() {
		Boolean numbersDisplayed = false;
		
		if (userContactPhoneNumbers.getText().contains(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER))) {
			numbersDisplayed = true;
		} else if (userContactPhoneNumbers.getText().contains(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER)) 
				&& userContactPhoneNumbers.getText().contains(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER))) {
			numbersDisplayed = true;
		} else {
			numbersDisplayed = false;
		}
		
		return numbersDisplayed;
	}
	
	public Boolean checkContactLocationsIsEmpty() {
		moreInformationBtn.click();
		
		String locations = whereToContactDetails.getText().trim();
		
		if(locations.isEmpty()) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public Boolean seeMoreContactInformation() { // Update for the new Authority and Organisation select test.
		moreInformationBtn.click();
		
		Boolean locationsDisplayed = false;
		
		if (whereToContactDetails.isDisplayed()) {
			if (whereToContactDetails.getText().contains(DataStore.getSavedValue(UsableValues.CHOSEN_AUTHORITY)) && whereToContactDetails.getText().contains(DataStore.getSavedValue(UsableValues.CHOSEN_ORGANISATION))) {
				locationsDisplayed = true;
			} else {
				locationsDisplayed = false;
			}
		}
		
		return locationsDisplayed;
	}
	
	public UserRoleTypePage clickManageRolesLink() {
		managerolesLink.click();
		return PageFactory.initElements(driver, UserRoleTypePage.class);
	}
	
	public BlockPage clickBlockUserAccountLink() {
		blockUserAccountLink.click();
		return PageFactory.initElements(driver, BlockPage.class);
	}
	
	public ReinstatePage clickReactivateUserAccountLink() {
		reactivateUserAccountLink.click();
		return PageFactory.initElements(driver, ReinstatePage.class);
	}
	
	public ChoosePersonToAddPage clickAddMembershipLink() {
		addMembershipLink.click();
		return PageFactory.initElements(driver, ChoosePersonToAddPage.class);
	}
	
	public RemovePage clickRemoveMembershipLink() {
		driver.findElement(By.xpath(removeMembershipLocator.replace("?", DataStore.getSavedValue(UsableValues.AUTHORITY_NAME)))).click();
		return PageFactory.initElements(driver, RemovePage.class);
	}
	
	public ContactDetailsPage clickUpdateUserButton() {
		updateUserBtn.click();
		return PageFactory.initElements(driver, ContactDetailsPage.class);
	}
	
	public ManagePeoplePage clickDoneButton() {
		doneBtn.click();
		return PageFactory.initElements(driver, ManagePeoplePage.class);
	}
	
	private String getExpectedPersonsName() {
		return DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.PERSON_LASTNAME);
	}
}
