package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARPartnershipTermsPage extends BasePageObject {

	public PARPartnershipTermsPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public PARPartnershipDescriptionPage acceptTerms() {
		WebElement checkbox = driver.findElement(By.id("edit-confirm"));
		//If the checkbox is unchecked then isSelected() will return false 
		//and NOT of false is true, hence we can click on checkbox
		if(!checkbox.isSelected())
			checkbox.click();
		if (continueBtn.isDisplayed())
			continueBtn.click();
		
		return PageFactory.initElements(driver, PARPartnershipDescriptionPage.class);
	}

}