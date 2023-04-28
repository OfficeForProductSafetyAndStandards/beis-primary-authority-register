package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class PersonsProfilePage extends BasePageObject {
	public PersonsProfilePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(tagName = "h1")
	private WebElement profileHeader;
	
	@FindBy(linkText = "Re-send the invitation")
	private WebElement reSendAccountInvitationLink;
	
	@FindBy(xpath = "//div[@class='component-user-detail']//p[2]")
	private WebElement userAccountEmail;
	
	@FindBy(xpath = "//div[@class='component-user-detail']//p[3]")
	private WebElement userAccountType;
	
	@FindBy(css = "div[class='component-contact-locations-detail'] p:nth-child(2)")
	private WebElement userContactName;
	
	@FindBy(xpath = "//div[@class='component-contact-locations-detail']//p[3]")
	private WebElement userContactEmail;
	
	@FindBy(css = "div[class='component-contact-locations-detail'] p:nth-child(5)")
	private WebElement userContactPhoneNumbers;
	
	@FindBy(partialLinkText = "Update")
	private WebElement updateUserBtn;
	
	@FindBy(css = ".summary")
	private WebElement moreInformationBtn;
	
	@FindBy(id = "contact-detail-locations-1")
	private WebElement whereToContactDetails;
	
	@FindBy(linkText = "Done")
	private WebElement doneBtn;
	
	public Boolean checkHeaderForName() {
		return profileHeader.getText().contains(getPersonsName());
	}
	
	public Boolean checkForUserAccountInvitationLink() {
		return reSendAccountInvitationLink.isDisplayed();
	}
	
	public Boolean checkUserAccountEmail() {
		return userAccountEmail.getText().contains(DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL));
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
		if(userContactPhoneNumbers.getText().contains(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER))) {
			return true;
		}
		else if(userContactPhoneNumbers.getText().contains(DataStore.getSavedValue(UsableValues.PERSON_WORK_NUMBER)) &&
				userContactPhoneNumbers.getText().contains(DataStore.getSavedValue(UsableValues.PERSON_MOBILE_NUMBER))) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public UpdateUserContactDetailsPage clickUpdateUserButton() {
		updateUserBtn.click();
		return PageFactory.initElements(driver, UpdateUserContactDetailsPage.class);
	}
	
	public Boolean seeMoreContactInformation() {
		moreInformationBtn.click();
		
		if(whereToContactDetails.isDisplayed()) {
			if(whereToContactDetails.getText().contains(DataStore.getSavedValue(UsableValues.CHOSEN_AUTHORITY)) && 
					whereToContactDetails.getText().contains(DataStore.getSavedValue(UsableValues.CHOSEN_ORGANISATION))) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
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
