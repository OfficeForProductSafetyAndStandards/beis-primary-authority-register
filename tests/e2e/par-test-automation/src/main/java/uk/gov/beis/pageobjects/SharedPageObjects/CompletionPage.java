package uk.gov.beis.pageobjects.SharedPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class CompletionPage extends BasePageObject {

	@FindBy(id = "edit-done")
	private WebElement doneBtn;

	@FindBy(linkText = "Done")
	private WebElement doneLink;

	public CompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void clickDoneForPartnership() {
        waitForElementToBeClickable(By.id("edit-done"), 2000);
        doneBtn.click();
	}

	public void clickDoneForInvitation() {
        waitForElementToBeClickable(By.linkText("Done"), 2000);
        doneLink.click();
	}
}
