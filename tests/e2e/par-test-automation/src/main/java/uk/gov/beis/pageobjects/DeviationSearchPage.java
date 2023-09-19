package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class DeviationSearchPage extends BasePageObject {

	public DeviationSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	String devReq = "(//tr/td[contains(text(),'?')]/following-sibling::td[5])[1]";

	public BasePageObject selectDeviationRequest() {
		driver.findElement(By.xpath(devReq.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME)))).click();
		return PageFactory.initElements(driver, BasePageObject.class);
	}
}
