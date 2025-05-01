package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipApprovalPage extends BasePageObject {

	@FindBy(id = "edit-done")
	private WebElement doneBtn;

	public PartnershipApprovalPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void clickDoneButton() {
        waitForElementToBeVisible(By.id("edit-done"), 2000);
        doneBtn.click();
        waitForPageLoad();
	}
}
