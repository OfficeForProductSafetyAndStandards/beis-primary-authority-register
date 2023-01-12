package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class EnforcementLegalEntityPage extends BasePageObject {

	public EnforcementLegalEntityPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;
	
	String legEnt = "//div/label[contains(text(),'?')]";

	public EnforcementLegalEntityPage selectLegalEntity(String ent) {
		driver.findElement(By.xpath(legEnt.replace("?", ent))).click();
		return PageFactory.initElements(driver, EnforcementLegalEntityPage.class);
	}

	public EnforcementDetailsPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementDetailsPage.class);
	}
}
