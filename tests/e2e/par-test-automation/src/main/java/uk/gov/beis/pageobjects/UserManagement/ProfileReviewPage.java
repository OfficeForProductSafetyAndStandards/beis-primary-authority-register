package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ProfileReviewPage extends BasePageObject {
	
	@FindBy(id = "edit-name")
	private WebElement profileName;
	
	@FindBy(id = "edit-email")
	private WebElement emailAddress;
	
	@FindBy(id = "edit-work-phone")
	private WebElement workPhoneNumber;
	
	@FindBy(id = "edit-mobile-phone")
	private WebElement mobilePhoneNumber;
	
	@FindBy(id = "edit-communication-noes")
	private WebElement communicationNotes;
	
	@FindBy(id = "edit-confirm-account")
	private WebElement userAccountCheckbox;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public ProfileReviewPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void confirmUserAccountEmail() {
		if(!userAccountCheckbox.isSelected()) {
			userAccountCheckbox.click();
		}
	}
	
	public boolean checkContactDetails() {
		return profileName.isDisplayed() && emailAddress.isDisplayed() && workPhoneNumber.isDisplayed() && mobilePhoneNumber.isDisplayed() && communicationNotes.isDisplayed();
	}
	
	public void clickSaveButton() {
		saveBtn.click();
	}
}
