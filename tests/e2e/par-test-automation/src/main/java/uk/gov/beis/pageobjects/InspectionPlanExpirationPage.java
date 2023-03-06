package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.utility.DateFormatter;

public class InspectionPlanExpirationPage extends BasePageObject {

	public InspectionPlanExpirationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;

	@FindBy(id = "edit-day")
	WebElement day;

	@FindBy(id = "edit-month")
	WebElement month;

	@FindBy(id = "edit-year")
	WebElement year;

	public InspectionPlanExpirationPage enterDate(String value) {
		String dateToInput = DateFormatter.getDynamicDate(value);
		LOG.info("Date is: " + dateToInput);
		day.sendKeys(dateToInput.substring(0, 2));
		month.sendKeys(dateToInput.substring(2, 4));
		year.sendKeys(dateToInput.substring(4, 8));
		return PageFactory.initElements(driver, InspectionPlanExpirationPage.class);
	}

	public InspectionPlanSearchPage save() {
		saveBtn.click();
		return PageFactory.initElements(driver, InspectionPlanSearchPage.class);
	}

}
