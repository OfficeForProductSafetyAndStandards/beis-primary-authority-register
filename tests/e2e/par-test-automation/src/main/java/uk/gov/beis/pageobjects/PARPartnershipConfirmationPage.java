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

	private boolean twopartjourney = false;

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
	String sic = "//div[contains(text(),'?')]";
	String noEmplyees = "//div[contains(text(),'?')]";
	String entName = "//div[contains(text(),'?')]";
	String entType = "//div[contains(text(),'?')]";
	String regNo = "//div[contains(text(),'?')]";
	String tradename = "//div[contains(text(),'?')]";

	public void setJourneyPart(boolean value) {
		this.twopartjourney = value;
	}

	public boolean getJourneyPart() {
		return this.twopartjourney;
	}

	public PARPartnershipConfirmationPage confirmDetails() {
		System.out.println("journey value = " + getJourneyPart());
		WebElement checkbox = getJourneyPart() ? driver.findElement(By.id("edit-terms-organisation-agreed"))
				: driver.findElement(By.id("edit-terms-authority-agreed"));
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
//		WebElement partnershipDets = driver.findElement(
//				By.xpath(partnershipDetails.replace("?", DataStore.getSavedValue(UsableValues.PARTNERSHIP_INFO))));
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

		return (businessNm.isDisplayed() && addLine1.isDisplayed() && businessTown.isDisplayed()
				&& businessPostcode.isDisplayed() && businessFirstName.isDisplayed() && businessLastName.isDisplayed()
				&& businessEmail.isDisplayed());
	}

	public boolean checkPartnershipApplicationSecondPart() {
		WebElement sicCd = driver
				.findElement(By.xpath(sic.replace("?", DataStore.getSavedValue(UsableValues.SIC_CODE))));
		WebElement nEmplyees = driver
				.findElement(By.xpath(noEmplyees.replace("?", DataStore.getSavedValue(UsableValues.NO_EMPLOYEES))));
		WebElement entityNm = driver
				.findElement(By.xpath(entName.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_NAME))));
		WebElement entityTyp = driver
				.findElement(By.xpath(entType.replace("?", DataStore.getSavedValue(UsableValues.ENTITY_TYPE))));
		WebElement regNum = driver
				.findElement(By.xpath(regNo.replace("?", DataStore.getSavedValue(UsableValues.REGISTRATION_NO))));
		WebElement tradeNm = driver
				.findElement(By.xpath(tradename.replace("?", DataStore.getSavedValue(UsableValues.TRADING_NAME))));

		return (checkPartnershipApplication() && sicCd.isDisplayed() && nEmplyees.isDisplayed()
				&& entityNm.isDisplayed() && entityTyp.isDisplayed() && regNum.isDisplayed() && regNum.isDisplayed()
				&& tradeNm.isDisplayed());
	}
}
