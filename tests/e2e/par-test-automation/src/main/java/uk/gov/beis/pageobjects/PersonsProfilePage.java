package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class PersonsProfilePage extends BasePageObject {
	
	@FindBy(tagName = "h1")
	private WebElement profileHeader;
	
	@FindBy(xpath = "//p[contains(text(), 'An invitation has already been sent')]")
	private WebElement invitationSentText;

	@FindBy(xpath = "//div[@class='component-user-detail']//p[3]")
	private WebElement userAccountType;

	@FindBy(xpath = "//fieldset/p[@class='column-two-thirds'][1]")
	private WebElement userContactName;

	@FindBy(xpath = "//fieldset/p[@class='column-two-thirds'][2]")
	private WebElement userContactEmail;

	@FindBy(xpath = "//fieldset/p[@class='column-one-third'][2]")
	private WebElement userContactPhoneNumbers;

	@FindBy(partialLinkText = "Update")
	private WebElement updateUserBtn;

	@FindBy(css = ".summary")
	private WebElement moreInformationBtn;

	@FindBy(id = "contact-detail-locations-1")
	private WebElement whereToContactDetails;

	@FindBy(linkText = "Done")
	private WebElement doneBtn;
	
	public PersonsProfilePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public Boolean checkHeaderForName() {
		return profileHeader.getText().contains(getPersonsName());
	}

	public Boolean checkForUserAccountInvitationLink() {
		return invitationSentText.isDisplayed();
	}

	public Boolean checkUserAccountEmail() {
		return userContactEmail.getText().contains(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
	}

	public Boolean checkUserAccountType() {
		return userAccountType.getText().contains(DataStore.getSavedValue(UsableValues.ACCOUNT_TYPE));
	}

	public Boolean checkContactName() {
		return userContactName.getText().contains(getPersonsName());
	}

	public Boolean checkContactEmail() {
		return userContactEmail.getText().contains(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL).toLowerCase());
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

	public UpdateUserContactDetailsPage clickUpdateUserButton() {
		updateUserBtn.click();
		return PageFactory.initElements(driver, UpdateUserContactDetailsPage.class);
	}

	public Boolean seeMoreContactInformation() {
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

	public ManagePeoplePage clickDoneButton() {
		doneBtn.click();
		return PageFactory.initElements(driver, ManagePeoplePage.class);
	}

	private String getPersonsName() {
		return DataStore.getSavedValue(UsableValues.PERSON_TITLE) + " "
				+ DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME) + " "
				+ DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME);
	}
}
