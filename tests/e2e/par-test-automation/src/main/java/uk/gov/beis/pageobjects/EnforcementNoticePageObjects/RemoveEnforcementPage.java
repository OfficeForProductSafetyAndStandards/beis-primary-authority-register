package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class RemoveEnforcementPage extends BasePageObject {

	@FindBy(id = "edit-reason-description")
	private WebElement descriptionBox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	private String removalReasonLocator = "//label[contains(text(),'?')]/preceding-sibling::input";

	public RemoveEnforcementPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectRemovalReason(String reason) {
		driver.findElement(By.xpath(removalReasonLocator.replace("?", reason))).click();
	}

	public void enterRemovalDescription(String reason) {
		descriptionBox.clear();
		descriptionBox.sendKeys(reason);
	}

	public void clickContinueButton() {
        waitForElementToBeClickable(By.id("edit-next"), 3000);
        continueBtn.click();
        waitForPageLoad();
	}
}
