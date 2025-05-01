package uk.gov.beis.pageobjects.AuthorityPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AuthorityNamePage extends BasePageObject {

	@FindBy(id = "edit-name")
	private WebElement authorityName;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	public AuthorityNamePage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterAuthorityName(String name) {
		authorityName.clear();
		authorityName.sendKeys(name);
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
