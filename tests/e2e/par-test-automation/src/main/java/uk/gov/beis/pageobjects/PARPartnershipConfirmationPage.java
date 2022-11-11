package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class PARPartnershipConfirmationPage extends BasePageObject {
	public PARPartnershipConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	String partnershipDetails = "//div/p[contains(text(),'?')]";
	String businessname = "//div[contains(text(),'?')]";
	String businessAddress1 = "//div/p[contains(text(),'?')]";
	String businessTown1 = "//div/p[contains(text(),'?')]";
	String businessPCode = "//div/p[contains(text(),'?')]";
	String businessFName = "//div[contains(text(),'?')]";
	String businessLName = "//div[contains(text(),'?')]";
	String businessEmailid = "//a[contains(text(),'?')]";
	String authorityName = "//div[contains(text(),'?')]";

	public PARPartnershipConfirmationPage confirmDetails() {
		WebElement checkbox = driver.findElement(By.id("edit-terms-authority-agreed"));
		// If the checkbox is unchecked then isSelected() will return false
		// and NOT of false is true, hence we can click on checkbox
		if (!checkbox.isSelected())
			checkbox.click();
		return PageFactory.initElements(driver, PARPartnershipConfirmationPage.class);
	}

	public PARPartnershipCompletionPage saveChanges() {
		if (saveBtn.isDisplayed())
			saveBtn.click();
		return PageFactory.initElements(driver, PARPartnershipCompletionPage.class);
	}

	public boolean checkPartnershipApplication() {
		WebElement partnershipDets = driver.findElement(
				By.xpath(partnershipDetails.replace("?", DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO))));
		WebElement businessNm = driver
				.findElement(By.xpath(businessname.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME))));
		WebElement addLine1 = driver.findElement(
				By.xpath(businessAddress1.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_ADDRESSLINE1))));
		WebElement businessTown = driver
				.findElement(By.xpath(businessTown1.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_TOWN))));
		WebElement businessPostcode = driver.findElement(
				By.xpath(businessPCode.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_POSTCODE))));
		WebElement businessFirstName = driver.findElement(
				By.xpath(businessFName.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_FIRSTNAME))));
		WebElement businessLastName = driver.findElement(
				By.xpath(businessLName.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_LASTNAME))));
		WebElement businessEmail = driver.findElement(
				By.xpath(businessEmailid.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_EMAIL))));
		return (partnershipDets.isDisplayed() && businessNm.isDisplayed() && addLine1.isDisplayed()
				&& businessTown.isDisplayed() && businessPostcode.isDisplayed() && businessFirstName.isDisplayed()
				&& businessLastName.isDisplayed() && businessEmail.isDisplayed());
	}
}
