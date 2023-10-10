package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MemberOrganisationSummaryPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.TradingPage;
import uk.gov.beis.utility.DataStore;

public class EnterTheDatePage extends BasePageObject {
	
	@FindBy(id = "edit-day")
	private WebElement dayField;
	
	@FindBy(id = "edit-month")
	private WebElement monthField;
	
	@FindBy(id = "edit-year")
	private WebElement yearField;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public EnterTheDatePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	// date field can be used for Sad path tests or future date tests.
	
	public TradingPage clickContinueButtonForMembershipBegan() {
		continueBtn.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
	
	public MemberOrganisationSummaryPage goToMemberOrganisationSummaryPage() {
		getMembershipDate();
		
		saveBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
	
	private void getMembershipDate() {
		String fullDate = dayField.getAttribute("value") + " " + convertMonthDate() + " " + yearField.getAttribute("value");
		
		DataStore.saveValue(UsableValues.MEMBERSHIP_START_DATE, fullDate);
	}
	
	private String convertMonthDate() {
		String month = "";
		
		switch(monthField.getAttribute("value")) {
		case "1":
			month = "January";
			break;
		case "2":
			month = "February";
			break;
		case "3":
			month = "March";
			break;
		case "4":
			month = "April";
			break;
		case "5":
			month = "May";
			break;
		case "6":
			month = "June";
			break;
		case "7":
			month = "July";
			break;
		case "8":
			month = "August";
			break;
		case "9":
			month = "September";
			break;
		case "10":
			month = "October";
			break;
		case "11":
			month = "November";
			break;
		case "12":
			month = "December";
			break;
		}
		
		return month;
	}
}
