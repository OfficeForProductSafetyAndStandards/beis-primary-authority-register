package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class EnquiriesSearchPage extends BasePageObject {

	public EnquiriesSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	String devReq = "(//tr/td[contains(text(),'?')]/following-sibling::td[3])[1]";

	public BasePageObject selectEnquiry() {
		driver.findElement(By.xpath(devReq.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME)))).click();
		return PageFactory.initElements(driver, BasePageObject.class);
	}
}