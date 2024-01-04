package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.time.LocalDate;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.InspectionPlanPageObjects.InspectionPlanSearchPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MemberOrganisationSummaryPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.MembershipCeasedPage;
import uk.gov.beis.pageobjects.OrganisationPageObjects.TradingPage;
import uk.gov.beis.pageobjects.TransferPartnerships.ConfirmThisTranferPage;
import uk.gov.beis.utility.DataStore;
import uk.gov.beis.utility.DateFormatter;

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
	public void enterCurrentDate() {
		dayField.sendKeys(String.valueOf(LocalDate.now().getDayOfMonth()));
		monthField.sendKeys(String.valueOf(LocalDate.now().getMonthValue()));
		yearField.sendKeys(String.valueOf(LocalDate.now().getYear()));
		
		String fullDate = String.valueOf(LocalDate.now().getDayOfMonth()) + " " + convertMonthDate(String.valueOf(LocalDate.now().getMonthValue())) + " " + String.valueOf(LocalDate.now().getYear());
		
		DataStore.saveValue(UsableValues.MEMBERSHIP_CEASE_DATE, fullDate);
	}
	
	public void  enterDate(String value) {
		String dateToInput = DateFormatter.getDynamicDate(value);
		
		LOG.info("Date is: " + dateToInput);
		
		dayField.sendKeys(dateToInput.substring(0, 2));
		monthField.sendKeys(dateToInput.substring(2, 4));
		yearField.sendKeys(dateToInput.substring(4, 8));
	}

	public InspectionPlanSearchPage goToInspectionPlanSearchPage() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionPlanSearchPage.class);
	}
	
	public TradingPage clickContinueButtonForMembershipBegan() {
		continueBtn.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
	
	public MemberOrganisationSummaryPage goToMemberOrganisationSummaryPage() {
		getMembershipDate();
		
		saveBtn.click();
		return PageFactory.initElements(driver, MemberOrganisationSummaryPage.class);
	}
	
	public MembershipCeasedPage goToMembershipCeasedPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, MembershipCeasedPage.class);
	}
	
	public ConfirmThisTranferPage goToConfirmThisTranferPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, ConfirmThisTranferPage.class);
	}
	
	private void getMembershipDate() {
		String fullDate = dayField.getAttribute("value") + " " + convertMonthDate(monthField.getAttribute("value")) + " " + yearField.getAttribute("value");
		
		if(fullDate.startsWith("0")) {
			String newDate = fullDate.substring(1); // Removing the 0.
			
			DataStore.saveValue(UsableValues.MEMBERSHIP_START_DATE, newDate);
		}
		else {
			DataStore.saveValue(UsableValues.MEMBERSHIP_START_DATE, fullDate);
		}
		
	}
	
	private String convertMonthDate(String value) {
		String month = "";
		String newValue = "";
		
		if(value.startsWith("0")) {
			newValue = value.substring(1); // Removing the 0.
		}
		else {
			newValue = value;
		}
		
		switch(newValue) {
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
