package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.PartnershipConfirmationPage;
import uk.gov.beis.utility.DataStore;

public class UserProfileConfirmationPage extends BasePageObject {

	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	String businessFName = "//fieldset[contains(text(),'?')]";
	String businessLName = "//fieldset[contains(text(),'?')]";
	String businessEmailid = "//fieldset[contains(text(),'?')]";
	
	public UserProfileConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public boolean checkUserCreation() {
		WebElement businessFirstName = driver.findElement(
				By.xpath(businessFName.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME))));
		WebElement businessLastName = driver.findElement(
				By.xpath(businessLName.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME))));
		WebElement businessEmail = driver.findElement(
				By.xpath(businessEmailid.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL))));

		return (businessFirstName.isDisplayed() && businessLastName.isDisplayed()
				&& businessEmail.isDisplayed());
	}
	
	public UserProfileCompletionPage saveChanges() {
		saveBtn.click();
		return PageFactory.initElements(driver, UserProfileCompletionPage.class);
	}
	
	public PartnershipConfirmationPage clickSaveButton() {
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
