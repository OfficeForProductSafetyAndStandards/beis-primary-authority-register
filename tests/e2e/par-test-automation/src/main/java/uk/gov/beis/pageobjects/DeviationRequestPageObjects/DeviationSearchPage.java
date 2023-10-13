package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class DeviationSearchPage extends BasePageObject {

	String devReq = "(//tr/td[contains(text(),'?')]/following-sibling::td[5])[1]";
	
	public DeviationSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public BasePageObject selectDeviationRequest() {
		driver.findElement(By.xpath(devReq.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME)))).click();
		return PageFactory.initElements(driver, BasePageObject.class);
	}
}
