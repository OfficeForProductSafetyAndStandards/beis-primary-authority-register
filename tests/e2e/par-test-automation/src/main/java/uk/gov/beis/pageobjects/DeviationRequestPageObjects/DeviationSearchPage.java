package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class DeviationSearchPage extends BasePageObject {

	private String devReq = "(//tr/td[contains(normalize-space(),'?')]/following-sibling::td[5]/a)[1]";
	
	public DeviationSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectDeviationRequest() {
		driver.findElement(By.xpath(devReq.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME)))).click();
	}
}
