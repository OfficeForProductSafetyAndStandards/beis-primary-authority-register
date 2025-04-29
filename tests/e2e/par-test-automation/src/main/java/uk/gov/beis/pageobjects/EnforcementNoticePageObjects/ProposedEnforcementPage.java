package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ProposedEnforcementPage extends BasePageObject{

	@FindBy(id = "edit-par-component-enforcement-action-review-0-primary-authority-status-approved")
	private WebElement allowRadial;

	@FindBy(id = "edit-par-component-enforcement-action-review-0-primary-authority-status-blocked")
	private WebElement blockRadial;

	@FindBy(id = "edit-par-component-enforcement-action-review-0-primary-authority-notes")
	private WebElement blockReasonTextArea;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	public ProposedEnforcementPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectAllow() {
		allowRadial.click();
	}

	public void selectBlock() {
		blockRadial.click();
	}

	public void enterReasonForBlockingEnforcement(String reason) {
		blockReasonTextArea.clear();
		blockReasonTextArea.sendKeys(reason);
	}

	public void clickContinueButton() {
        waitForElementToBeClickable(By.id("edit-next"), 3000);
        continueBtn.click();
        waitForPageLoad();
	}
}
