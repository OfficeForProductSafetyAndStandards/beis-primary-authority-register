package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class UserProfilePage extends BasePageObject {
	
	@FindBy(tagName = "h1")
	private WebElement profileHeader;
	
	@FindBy(xpath = "//div/a[contains(normalize-space(), 'Invite the user to create an account')]")
	private WebElement accountInvitationLink;

	@FindBy(xpath = "//div[@class='govuk-grid-row govuk-form-group']/p[3]")
	private WebElement userAccountType;

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
	
	public UserProfilePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public Boolean checkHeaderForName() {
		return profileHeader.getText().contains(getExpectedPersonsName());
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

	// Merge Contact Records
	public Boolean checkContactRecordAdded() {
		String contactsNameLocator = "//p[contains(text(), '?')]";
		
		return driver.findElements(By.xpath(contactsNameLocator.replace("?", getPersonsName()))).size() > 1;
	}
	
	public Boolean checkContactRecord() {
		String contactsNameLocator = "//p[contains(text(), '?')]";
		
		return driver.findElements(By.xpath(contactsNameLocator.replace("?", getPersonsName()))).size() == 1;
	}
	
	public ContactDetailsPage clickUpdateUserButton() {
		updateUserBtn.click();
		return PageFactory.initElements(driver, ContactDetailsPage.class);
	}

	public MergeContactRecordsPage clickMergeContactRecords() {
		mergeContactRecordsLink.click();
		return PageFactory.initElements(driver, MergeContactRecordsPage.class);
	}
	
	public ManagePeoplePage clickDoneButton() {
		doneBtn.click();
		return PageFactory.initElements(driver, ManagePeoplePage.class);
	}
	
	private String getExpectedPersonsName() {
		return DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + DataStore.getSavedValue(UsableValues.PERSON_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.PERSON_LASTNAME);
	}
	
	private String getPersonsName() { // Temp?
		return DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " " + DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);
	}
}
