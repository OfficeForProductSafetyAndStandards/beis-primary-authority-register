package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AccountInvitePage extends BasePageObject {

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	@FindBy(id = "edit-next")
	private WebElement inviteBtn;

	public AccountInvitePage() throws ClassNotFoundException, IOException {
		super();
	}

	public void clickContinueButton() {
        waitForElementToBeVisible(By.id("edit-next"), 2000);
        continueBtn.click();
        waitForPageLoad();
	}

	public void clickInviteButton() {
        waitForElementToBeVisible(By.id("edit-next"), 2000);
		inviteBtn.click();
        waitForPageLoad();
	}
}
