package uk.gov.beis.pageobjects.GeneralEnquiryPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class EnquiriesSearchPage extends BasePageObject {
	
	private String enquiry = "(//tr/td[contains(normalize-space(),'?')]/following-sibling::td[3]/a)[1]";
	
	public EnquiriesSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectEnquiry() {
		driver.findElement(By.xpath(enquiry.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME)))).click();
	}
}