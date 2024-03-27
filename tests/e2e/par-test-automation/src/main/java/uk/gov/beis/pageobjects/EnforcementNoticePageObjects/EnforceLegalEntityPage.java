package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class EnforceLegalEntityPage extends BasePageObject {
	
	//@FindBy(id = "edit-alternative-legal-entity")
	//private WebElement legalEntityNameField;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	private String legalEntityLocator = "//div/label[contains(normalize-space(),'?')]/preceding-sibling::input";
	
	public EnforceLegalEntityPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterLegalEntityName(String name) {
		if(!driver.findElements(By.xpath(legalEntityLocator.replace("?", name))).isEmpty()) {
			WebElement authorityRadio = driver.findElement(By.xpath(legalEntityLocator.replace("?", name)));
			authorityRadio.click();
		}
		else {
			WebElement legalEntityNameField = driver.findElement(By.id("edit-alternative-legal-entity"));
			legalEntityNameField.clear();
			legalEntityNameField.sendKeys(name);
		}
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}

	public EnforcementDetailsPage goToEnforcementDetailsPage() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementDetailsPage.class);
	}
}
