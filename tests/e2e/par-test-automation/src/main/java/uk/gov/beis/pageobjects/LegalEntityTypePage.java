package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.helper.ScenarioContext;

public class LegalEntityTypePage extends BasePageObject {

	public LegalEntityTypePage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	@FindBy(id = "edit-par-component-legal-entity-0-registered-legal-entity-number")
	WebElement regNo;

	String legalEntType = "//label[contains(text(),'?')]";

	public LegalEntityReviewPage selectEntityType(String name, String type, String reg) {
		if (!type.equalsIgnoreCase("unregistered")) {
			ScenarioContext.registered = true;
			driver.findElement(By.xpath(legalEntType.replace("?", type))).click();
			driver.findElement(By.id("edit-par-component-legal-entity-0-registered-legal-entity-number")).sendKeys(reg);
		} else {
			ScenarioContext.registered = false;
			driver.findElement(By.xpath(legalEntType.replace("?", type))).click();
			driver.findElement(By.xpath("//label[contains(text(),'Other')]")).click();
			driver.findElement(By.xpath("//input[@class='form-group form-text form-control govuk-input']")).sendKeys(name);
		}
		continueBtn.click();
		return PageFactory.initElements(driver, LegalEntityReviewPage.class);
	}
}
