package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

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
            waitForElementToBeClickable(By.id("edit-alternative-legal-entity"), 2000);
			WebElement legalEntityNameField = driver.findElement(By.id("edit-alternative-legal-entity"));
			legalEntityNameField.clear();
			legalEntityNameField.sendKeys(name);
		}
	}

	// Add a method which does the same as above but instead of selecting the radio button, it tests the second radio button which then requires the name being entered into a text field.

	public void clickContinueButton() {
        waitForElementToBeClickable(By.id("edit-next"), 3000);
        continueBtn.click();
        waitForPageLoad();
	}
}
