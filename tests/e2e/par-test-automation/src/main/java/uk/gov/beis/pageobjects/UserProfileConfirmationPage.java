package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class UserProfileConfirmationPage extends BasePageObject {

	public UserProfileConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;
	
	String businessFName = "//fieldset[contains(text(),'?')]";
	String businessLName = "//fieldset[contains(text(),'?')]";
	String businessEmailid = "//fieldset[contains(text(),'?')]";
	
	public boolean checkUserCreation() {
		WebElement businessFirstName = driver.findElement(
				By.xpath(businessFName.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME)).toLowerCase()));
		WebElement businessLastName = driver.findElement(
				By.xpath(businessLName.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME)).toLowerCase()));
		WebElement businessEmail = driver.findElement(
				By.xpath(businessEmailid.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL)).toLowerCase()));

		return (businessFirstName.isDisplayed() && businessLastName.isDisplayed()
				&& businessEmail.isDisplayed());
	}
	
	public UserProfileCompletionPage saveChanges() {
		if (saveBtn.isDisplayed())
			saveBtn.click();
		return PageFactory.initElements(driver, UserProfileCompletionPage.class);
	}
}
