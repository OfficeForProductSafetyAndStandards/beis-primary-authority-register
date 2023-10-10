package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipTypePage extends BasePageObject {

	public PartnershipTypePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	private String locator = "//label[contains(text(),'?')]";

	public PartnershipTermsPage selectPartnershipType(String type) {
		WebElement link = driver.findElement(By.xpath(locator.replace("?", type)));
		link.click();
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipTermsPage.class);
	}

}
