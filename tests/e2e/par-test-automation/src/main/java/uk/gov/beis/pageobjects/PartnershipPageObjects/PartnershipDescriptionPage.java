package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipDescriptionPage extends BasePageObject {

	@FindBy(id = "edit-about-partnership")
	private WebElement descriptionBox;

	@FindBy(id = "edit-about-business")
	private WebElement businessDescriptionBox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	public PartnershipDescriptionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}

	public void updateBusinessDescription(String description) {
		businessDescriptionBox.clear();
		businessDescriptionBox.sendKeys(description);
	}

	public void clickContinueButton() {
        waitForElementToBeVisible(By.id("edit-next"), 2000);
        continueBtn.click();
        waitForPageLoad();
	}

	public void clickSaveButton() {
        waitForElementToBeVisible(By.id("edit-save"), 2000);
        saveBtn.click();
        waitForPageLoad();
	}
}
