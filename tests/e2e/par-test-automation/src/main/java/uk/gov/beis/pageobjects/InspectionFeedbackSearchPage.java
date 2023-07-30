package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class InspectionFeedbackSearchPage extends BasePageObject {

	public InspectionFeedbackSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(linkText = "Upload inspection plan")
	WebElement uploadBtn;

	String feedbknotice = "(//tr/td[contains(text(),'?')]/following-sibling::td[3])[1]";

	public InspectionFeedbackConfirmationPage selectInspectionFeedbackNotice() {
		driver.findElement(By.xpath(feedbknotice.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME))))
				.click();
		return PageFactory.initElements(driver, InspectionFeedbackConfirmationPage.class);
	}
}
