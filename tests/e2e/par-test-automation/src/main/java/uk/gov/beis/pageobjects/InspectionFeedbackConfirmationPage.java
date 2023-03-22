package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class InspectionFeedbackConfirmationPage extends BasePageObject {

	public InspectionFeedbackConfirmationPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Save')]")
	WebElement saveBtn;
	
	@FindBy(linkText = "Submit a response")
	WebElement submitResponse;

	public BasePageObject saveChanges() {
		try {
			driver.findElement(By.xpath("//input[contains(@value,'Save')]")).click();
			return PageFactory.initElements(driver, InspectionFeedbackCompletionPage.class);
		} catch (Exception e) {
			driver.findElement(By.linkText("Done")).click();
			return PageFactory.initElements(driver, InspectionFeedbackSearchPage.class);
		}
	}
	
	public ReplyInspectionFeedbackPage submitResponse() {
		submitResponse.click();
		return PageFactory.initElements(driver, ReplyInspectionFeedbackPage.class);
	}

	String desc = "//div/p[contains(text(),'?')]";
	String enfFile = "//span/a[contains(text(),'?')]";

	public boolean checkInspectionFeedback() {
		WebElement desc1 = driver.findElement(By
				.xpath(desc.replace("?", DataStore.getSavedValue(UsableValues.INSPECTIONFEEDBACK_DESCRIPTION))));

		return (desc1.isDisplayed());
	}

}
