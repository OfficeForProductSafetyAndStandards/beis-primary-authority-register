package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class RevokePage extends BasePageObject {

	@FindBy(id = "edit-revocation-reason")
	private WebElement reasonTextArea;

	@FindBy(id = "edit-next")
	private WebElement nextBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	public RevokePage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterReasonForRevocation(String reason) {
		reasonTextArea.clear();
		reasonTextArea.sendKeys(reason);
	}

	public void clickRevokeButton() {
        waitForElementToBeVisible(By.id("edit-next"), 2000);
        nextBtn.click();
        waitForPageLoad();
	}

	public void selectRevokeButton(){
        waitForElementToBeVisible(By.id("edit-save"), 2000);
        saveBtn.click();
        waitForPageLoad();
	}
}
