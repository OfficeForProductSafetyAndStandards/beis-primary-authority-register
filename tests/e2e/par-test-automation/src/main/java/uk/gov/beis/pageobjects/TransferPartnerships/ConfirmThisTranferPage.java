package uk.gov.beis.pageobjects.TransferPartnerships;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ConfirmThisTranferPage extends BasePageObject {

	@FindBy(id = "edit-confirmation")
	private WebElement confirmationCheckbox;

	@FindBy(id = "edit-next")
	private WebElement transferBtn;

	public ConfirmThisTranferPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void confirmPartnershipTransfer() {

		if(!confirmationCheckbox.isSelected()) {
			confirmationCheckbox.click();
		}
	}

	public void clickTransferButton() {
        waitForElementToBeVisible(By.id("edit-next"), 2000);
        transferBtn.click();
        waitForPageLoad();
	}
}
