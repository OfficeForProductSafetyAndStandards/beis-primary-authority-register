package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class RemovePage extends BasePageObject {

	@FindBy(id = "edit-remove-reason")
	private WebElement removeReasonTextArea;

	@FindBy(id = "edit-next")
	private WebElement nextBtn;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	public RemovePage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterRemoveReason(String reason) {
		removeReasonTextArea.clear();
		removeReasonTextArea.sendKeys(reason);
	}

	public void selectRemoveButton() {
        waitForElementToBeVisible(By.id("edit-next"), 2000);
        nextBtn.click();
        waitForPageLoad();
	}

	public void clickRemoveButton() {
        waitForElementToBeVisible(By.id("edit-save"), 2000);
        saveBtn.click();
        waitForPageLoad();
	}
}
