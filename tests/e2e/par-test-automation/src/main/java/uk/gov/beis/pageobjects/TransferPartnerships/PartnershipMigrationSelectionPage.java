package uk.gov.beis.pageobjects.TransferPartnerships;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class PartnershipMigrationSelectionPage extends BasePageObject {

	@FindBy(id = "edit-authority")
	private WebElement authorityTextfield;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;

	private String partnershipLocator = "//label[contains(text(), '?')]/preceding-sibling::input";

	public PartnershipMigrationSelectionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectPartnership(String businessName) {
//waitForElementToBeVisible(By.xpath(partnershipLocator.replace("?", businessName)), 2000);
		WebElement checkbox = driver.findElement(By.xpath(partnershipLocator.replace("?", businessName)));

		if(!checkbox.isSelected()) {
			checkbox.click();
            waitForPageLoad();
		}
	}

	public void clickContinueButton() {

        waitForElementToBeVisible(By.id("edit-next"), 2000);
        continueBtn.click();
	}
}
