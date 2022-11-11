package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARPartnershipTypePage extends BasePageObject {

	public PARPartnershipTypePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	private String locator = "//label[contains(text(),'?')]";

	public PARPartnershipTermsPage selectPartnershipType(String type) {
		WebElement link = driver.findElement(By.xpath(locator.replace("?", type)));
		link.click();
		if (continueBtn.isDisplayed())
			continueBtn.click();
		return PageFactory.initElements(driver, PARPartnershipTermsPage.class);
	}

}
