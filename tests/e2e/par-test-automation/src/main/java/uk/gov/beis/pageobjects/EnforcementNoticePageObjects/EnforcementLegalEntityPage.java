package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class EnforcementLegalEntityPage extends BasePageObject {

	public EnforcementLegalEntityPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;
	
	@FindBy(id = "edit-alternative-legal-entity")
	WebElement ent;
	
	
	String legEnt = "//div/label[contains(text(),'?')]";

	public EnforcementLegalEntityPage selectLegalEntity(String ent) {
		driver.findElement(By.xpath(legEnt.replace("?", ent))).click();
		return PageFactory.initElements(driver, EnforcementLegalEntityPage.class);
	}
	
	public EnforcementLegalEntityPage enterEntity(String entity) {
		ent.sendKeys(entity);
		return PageFactory.initElements(driver, EnforcementLegalEntityPage.class);
	}

	public EnforcementDetailsPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementDetailsPage.class);
	}
}
