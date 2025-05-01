package uk.gov.beis.pageobjects.AdvicePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AdviceArchivePage extends BasePageObject {

	@FindBy(id = "edit-archive-reason")
	private WebElement archiveReasonBox;

	@FindBy(id = "edit-save")
	private WebElement saveBtn;

	public AdviceArchivePage() throws ClassNotFoundException, IOException {
		super();
	}

	public void enterArchiveReason(String reason) {
        waitForElementToBeVisible(By.id("edit-archive-reason"), 2000);
		archiveReasonBox.clear();
		archiveReasonBox.sendKeys(reason);
	}

	public void selectSaveButton() {

        waitForElementToBeVisible(By.id("edit-save"), 2000   );
        saveBtn.click();
	}
}
