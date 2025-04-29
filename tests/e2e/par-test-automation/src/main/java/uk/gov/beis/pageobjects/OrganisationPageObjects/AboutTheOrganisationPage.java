package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AboutTheOrganisationPage extends BasePageObject {

	@FindBy(id = "edit-about-business")
	private WebElement descriptionBox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	public AboutTheOrganisationPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}

	public void clickContinueButton() {
        waitForElementToBeClickable(By.id("edit-next"), 3000);
        continueBtn.click();
        waitForPageLoad();
	}

	public void clickSaveButton() {
        waitForElementToBeClickable(By.id("edit-save"), 3000);
        saveBtn.click();
        waitForPageLoad();
	}
}
