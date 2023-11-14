package uk.gov.beis.pageobjects.GeneralEnquiryPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class EnquiriesSearchPage extends BasePageObject {
	
	private String enquiry = "(//tr/td[contains(normalize-space(),'?')]/following-sibling::td[3])[1]";
	
	public EnquiriesSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	public BasePageObject selectEnquiry() {
		driver.findElement(By.xpath(enquiry.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME)))).click();
		return PageFactory.initElements(driver, BasePageObject.class);
	}
}