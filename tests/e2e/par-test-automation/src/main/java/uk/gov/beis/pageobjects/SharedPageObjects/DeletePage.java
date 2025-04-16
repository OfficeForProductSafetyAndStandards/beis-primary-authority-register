package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class DeletePage extends BasePageObject {

	@FindBy(id = "edit-deletion-reason")
	private WebElement deletionReasonTextArea;

	@FindBy(id = "edit-next")
	private WebElement deleteBtn;

	public DeletePage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterReasonForDeletion(String reason) {
		deletionReasonTextArea.clear();
		deletionReasonTextArea.sendKeys(reason);
	}

	public void clickDoneButton() {
        waitForElementToBeClickable(By.id("edit-next"), 2000);
        deleteBtn.click();
	}
}
