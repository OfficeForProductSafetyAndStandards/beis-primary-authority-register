package uk.gov.beis.pageobjects.InspectionPlanFeedbackPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class InspectionFeedbackSearchPage extends BasePageObject {
	
	private String feedbknotice = "(//tr/td[contains(normalize-space(),'?')]/following-sibling::td[3]/a)[1]";
	
	public InspectionFeedbackSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public InspectionFeedbackConfirmationPage selectInspectionFeedbackNotice() {
		driver.findElement(By.xpath(feedbknotice.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME)))).click();
		return PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
	}
}
